<?php

include_once ('Controller.php');
include_once ('ModuleSettingsReaderController.php');
include_once ('TraitsTaxonController.php');

// run as .../export_versatile.php?printquery to print actual query as well

class VersatileExportController extends Controller
{
    
    public $usedModels = array(
        'presence',
        'names',
        'name_types',
        'traits_matrix'
    );
    
    public $modelNameOverride='VersatileExportModel';
    public $controllerPublicName = 'Export';
    
    public $usedHelpers = array(
        'zip_file'
    );
    
    public $cssToLoad = array(
        'lookup.css',
        'nsr_taxon_beheer.css'
    );
    
    public $jsToLoad = array(
        'all' => array(
            'lookup.js',
            'nsr_taxon_beheer.js'
        )
    );
    
    private $branch_top_id;
    private $presence_labels;
    private $selected_ranks;
    private $selected_synonym_types;
    private $all_ranks;
    private $rank_operator;
    private $cols;
    private $name_parts;
    private $ancestors;
    private $doSynonyms;
    private $doHybridMarker;
    private $field_sep;
    private $new_line;
    private $no_quotes;
    private $keep_tags;
    private $utf8_to_utf16;
    private $add_utf8_BOM;
    private $output_target;
    private $print_query=false;
    private $_nameTypeIds;
    private $suppress_underscored_fields=true;
    private $replaceUnderscoresInHeaders=true;
    private $doPrintQueryParameters=true;
    private $printEOFMarker=false;
    private $quoteChar='"';
    private $query_bit_name_parts;
    private $query;
    private $queries;
    private $concept_url;
    private $names=array();
    private $synonyms=array();
    private $tc;
    private $traits = [];
    private $traitGroups;
    private $selectedTraits = [];
    private $parentRegister=array();
    private $operators=
    [
        "eq"=>["operator"=>"=","label"=>"is:"],
        "ge"=>["operator"=>">=","label"=>"gelijk aan of onder:"],
        "in"=>["operator"=>"in","label"=>"in:"],
    ];
    
    private $orderBy="_f.rank_id asc,_r.id, _t.taxon";  // rank, rank id, taxon
    private $show_nsr_specific_stuff;
    private $spoof_settings;
    
    private $limit = 20000;
    private $offset = 0;

    private $taxonTraits = [];
    private $traitGroupLookup = [];

    /*
     when the number of names is larger than synonymStrategyThrehold, the
     program will fetch all synonyms in one query, and filter and assign them
     to the correct taxon in PHP next. below the threshold, a separate query
     will be executed for each taxon.
     2000 is an arbitrary number, and shoud be calibrated.
     */
    private $synonymStrategyThrehold=2000;
    
    private $fhNames;
    private $names_file_name = "%s-export--%s.csv";
    private $names_file_path;
    
    private $fhSynonyms;
    private $synonyms_file_name = "%s-export-synonyms--%s.csv";
    private $synonyms_file_path;
    
    private $fh;
    private $EOFMarker='(end of file)';
    
    private $columnHeaders=[
        'sci_name'=>'scientific_name',
        'dutch_name'=>'common_name',
        'rank'=>'rank',
        'nsr_id'=>'nsr_id',
        'presence_status'=>'presence_status',
        'presence_status_publication'=>'presence_status_publication',
        'habitat'=>'habitat',
        'concept_url'=>'concept_url',
        'database_id'=>'database_id',
        'parent_taxon'=>'parent_taxon',
        'parent_taxon_nsr_id'=>'parent_taxon_nsr_id',
        'uninomial'=>'uninomial',
        'specific_epithet'=>'specific_epithet',
        'infra_specific_epithet'=>'infra_specific_epithet',
        'authorship'=>'authorship',
        'name_author'=>'name_author',
        'authorship_year'=>'authorship_year',
        'synonym'=>'synonym',
        'synonym_type'=>'type_synonym',
        'language'=>'language',
        'taxon'=>'taxon',
    ];
    
    
    public function __construct ()
    {
        parent::__construct();
        $this->initialize();
    }
    
    public function __destruct ()
    {
        parent::__destruct();
    }
    
    private function initialize()
    {
        $this->UserRights->setRequiredLevel( ID_ROLE_LEAD_EXPERT );
        $this->checkAuthorisation();
        $this->setNameTypeIds();
        $this->getTraits();
        
        $this->moduleSettings=new ModuleSettingsReaderController;
        $this->concept_url=$this->moduleSettings->getGeneralSetting( 'concept_base_url' );
        $this->show_nsr_specific_stuff=$this->moduleSettings->getGeneralSetting( 'show_nsr_specific_stuff' , 0)==1;
        
        $this->names_file_name = sprintf($this->names_file_name, $this->getProjectTitle(true), date('Ymd-His'));
        $this->synonyms_file_name = sprintf($this->synonyms_file_name, $this->getProjectTitle(true), date('Ymd-His'));
        
        if ( method_exists( $this->customConfig , 'getVersatileExportSpoof' ) )
        {
            $this->spoof_settings=$this->customConfig->getVersatileExportSpoof();
            if ( $this->spoof_settings->do_spoof_export )
            {
                $this->smarty->assign( 'spoof_settings_warning', $this->spoof_settings->texts->warning );
            }
        }
        
        foreach ($this->columnHeaders as $key=>$val)
        {
            $this->columnHeaders[$key]=$this->translate($val);
        }
    }

    private function saveTaxonTraitValues ($values, $type)
    {
        $i = 0;
        $method = 'getTaxonTraits' . ucfirst(strtolower($type)) . 'Values';

        foreach ($values as $row) {
            $data = [
                'language_id' => $this->getDefaultProjectLanguage(),
                'project_id' => $this->getCurrentProjectId(),
                'taxon_id' => $row['taxon_id']
            ];
            foreach (explode(',', $row['trait_ids']) as $traitId) {
                $i++;
                // Simplified query for values used
                $taxonValues = $this->models->VersatileExportModel->{$method}([
                    'project_id' => $this->getCurrentProjectId(),
                    'language_id' => $this->getDefaultProjectLanguage(),
                    'taxon_id' => $row['taxon_id'],
                    'trait_id' => $traitId,
                ]);

                if (!empty($taxonValues)) {
                    // Values have not been formatted yet
                    $taxonValues = $this->tc->formatTraitsTaxonValues($taxonValues);
                    $values = [];
                    foreach ($taxonValues[0]['values'] as $value) {
                        $values[] = $value['value_start'] . (!empty($value['value_end']) ? '-' . $value['value_end'] : '');
                    }
                    $traits[] = [
                        'trait_group_id' => $this->traitGroupLookup($traitId)['id'],
                        'trait_group_name' => $this->traitGroupLookup($traitId)['name'],
                        'trait_id' => $traitId,
                        'trait_name' => $taxonValues[0]['trait']['name'],
                        'trait_value' => implode('|', $values),
                    ];
                }
            }
            $data['traits'] = $traits;

            print_r( $data); die();


            $this->models->VersatileExportModel->saveTaxonTraitValues($data);
        }

        die('taxa:' . count($d) . '; traits:' . $i);
    }

    private function createTraitsMatrix ()
    {
        set_time_limit(600);
        $this->tc = new TraitsTaxonController();
        $this->models->VersatileExportModel->emptyTraitsMatrix();

        // Fixed values
        $values = $this->models->VersatileExportModel->getTaxaTraitsFixedValues([
            'project_id' => $this->getCurrentProjectId(),
        ]);
        $this->saveTaxonTraitValues($values, 'fixed');

        // Get free value traits per taxon
        $values = $this->models->VersatileExportModel->getTaxaTraitsFreeValues([
            'project_id' => $this->getCurrentProjectId(),
        ]);
        $this->saveTaxonTraitValues($values, 'free');

        die('Ready');
    }

    private function traitGroupLookup ($traitId) {
        if (!isset($this->traitGroupLookup[$traitId])) {
            $trait = $this->tc->getTraitGroupTrait(['trait' => $traitId]);
            $group = $this->tc->getTraitgroup($trait['trait_group_id']);

            $this->traitGroupLookup[$traitId] = [
                'id' => $group['id'],
                'name' => isset($group['names'][$this->getDefaultProjectLanguage()]) ?
                    $group['names'][$this->getDefaultProjectLanguage()] :
                    $group['sysname']
             ];
        }
        return $this->traitGroupLookup[$traitId];
    }

    public function exportAction()
    {
        $this->setPageName( $this->translate('Multi-purpose export') );

        if ($this->rHasVal('action','matrix')) {
            $this->createTraitsMatrix();
            die();
        }

        if ($this->rHasVal('action','export'))
        {
            $this->setBranchTopSession( array( $this->rGetVal('branch_top_id'), $this->rGetVal('branch_top_label') ) );
            $this->setBranchTopId( $this->rGetVal('branch_top_id') );
            $this->setPresenceStatusLabels( $this->rGetVal('presence_labels') );
            $this->setAllRanks( $this->rGetVal('all_ranks') );
            $this->setSelectedRanks( $this->rGetVal('selected_ranks') );
            $this->setRankOperator( $this->rGetVal('rank_operator') );
            $this->setCols( $this->rGetVal('cols') );
            $this->setDoHybridMarker( $this->rGetVal('add_hybrid_marker') );
            $this->setNameParts( $this->rGetVal('name_parts') );
            $this->setAncestors( $this->rGetVal('ancestors') );
            $this->setDoSynonyms( $this->rGetVal('synonyms') );
            $this->setSelectedTraits( $this->rGetVal('traits') );
            $this->setSelectedSynonymTypes( $this->rGetVal('nametypes') );
            $this->setOrderBy( $this->rGetVal('order_by') );
            $this->setFieldSep( $this->rGetVal('field_sep') );
            $this->setNewLine( $this->rGetVal('new_line') );
            $this->setNoQuotes( $this->rGetVal('no_quotes') );
            $this->setReplaceUnderscoresInHeaders( $this->rGetVal('replace_underscores_in_headers') );
            //$this->setUtf8ToUtf16( $this->rGetVal('utf8_to_utf16') );
            $this->setKeepTags( $this->rGetVal('keep_tags') );
            $this->setAddUtf8BOM( $this->rGetVal('add_utf8_BOM') );
            $this->setDoPrintQueryParameters( $this->rGetVal('print_query_parameters') );
            $this->setPrintEOFMarker( $this->rGetVal('print_eof_marker') );
            $this->setOutputTarget( $this->rGetVal('output_target') );
            $this->setPrintQuery( $this->rHasVar('printquery') ); // undoc'd feat. start as export_versatile.php?printquery to add full queries to output
            
            $this->setNameTypeIds();
            
            echo $this->doOutputStart();
            do {
                $this->doMainQuery();
                $this->doAncestry();
                $this->doTraits();
                $this->doSynonymsQuery();
                $this->doOutput();
                $this->offset += $this->limit;
            } while (!empty($this->names));
            $this->doOutputEnd();
        }
        
        $this->smarty->assign( 'presence_labels', $this->getPresenceStatuses() );
        $this->smarty->assign( 'ranks', $this->getRanks() );
        $this->smarty->assign('traits', $this->traitGroups);
        $this->smarty->assign( 'branch_top', $this->getBranchTopSession() );
        $this->smarty->assign( 'nametypes', $this->_nameTypeIds );
        $this->smarty->assign('is_nsr', $this->show_nsr_specific_stuff);
        
        $this->printPage();
        
    }
    
    private function setSelectedTraits ($selected = false)
    {
        if (!empty($selected) && !empty($this->traitGroups)) {
            foreach ($selected as $group => $traits) {
                $groupName = $this->traitGroups[$group]['name'];
                $groupTraits = array_column($this->traitGroups[$group]['traits'], 'name', 'id');
                foreach ($traits as $trait) {
                    $this->selectedTraits[$group . '-' . $trait] =
                    strtolower(str_replace(' ', '_', $groupName . ':' . $groupTraits[$trait]));
                }
            }
        }
    }
    
    private function getPresenceStatuses()
    {
        return $this->models->VersatileExportModel->getPresenceStatuses(array(
            "project_id"=>$this->getCurrentProjectId()
        ));
    }
    
    private function getTraits ()
    {
        $this->tc = new TraitsTaxonController;
        $groups = $this->tc->getTraitgroups();
        if (!empty($groups)) {
            foreach ($groups as $group) {
                $this->traitGroups[$group['id']] = $this->tc->getTraitgroup($group['id']);
            }
            $this->customSortArray($this->traitGroups, ['key' => 'name', 'maintainKeys' => true]);
            foreach ($this->traitGroups as $i => $d) {
                $traits = $d['traits'];
                $this->customSortArray($traits, ['key' => 'name']);
                $this->traitGroups[$i]['traits'] = $traits;
            }
            return $this->traitGroups;
        }
        return false;
    }
    
    private function getRanks()
    {
        return $this->models->VersatileExportModel->getRanks(array(
            "project_id"=>$this->getCurrentProjectId()
        ));
    }
    
    private function setNameTypeIds()
    {
        $this->_nameTypeIds=$this->models->NameTypes->_get(array(
            'id'=>array(
                'project_id'=>$this->getCurrentProjectId()
            ),
            'columns'=>"
				id,
				nametype,substring(substring(name_types.nametype,3),1,length(name_types.nametype)-4) as nametype_hr,
				case
					when
						nametype = '" . PREDICATE_PREFERRED_NAME . "'
					then 0
					when
						nametype = '" . PREDICATE_ALTERNATIVE_NAME . "'
					then 1
					else 2
				end as sortfield",
            'fieldAsIndex'=>'nametype',
            'order'=>'sortfield,nametype'
        ));
    }
    
    private function doMainQuery()
    {
        
        $this->query_bit_name_parts='';
        
        if ( $this->hasCol( 'name_parts' ) )
        {
            foreach($this->getNameParts() as $key=>$val)
            {
                $this->query_bit_name_parts.='_names.'.$key.' as ' .  $this->columnHeaders[$key] . ', ';
            }
        }
        
        $ranks_clause="";
        if ( !$this->getAllRanks() )
        {
            $ranks_clause="and _f.rank_id ".$this->operators[$this->getRankOperator()]['operator'] . " (".implode( "," , $this->getSelectedRanks() ).")";
        }
        
        $presence_status_clause="";
        $p = $this->getPresenceStatusLabels();
        if ( !empty( $p ) )
        {
            $presence_status_clause="and _h.index_label in ('".implode("','",$p)."') ";
        }
        
        
        $this->query="
			select
				".( $this->hasCol( 'sci_name' ) ? " _t.taxon as " . $this->columnHeaders['sci_name'] . ", " : "" )."
				".( $this->query_bit_name_parts )."
				".( $this->hasCol( 'dutch_name' ) ? " _z.name as " . $this->columnHeaders['dutch_name'] . ", " : "" )."
				".( $this->hasCol( 'rank' ) ? " ifnull(_lpr.label,_r.rank) as " . $this->columnHeaders['rank'] . ", " : "" )."
				".( $this->hasCol( 'nsr_id' ) ? " replace(_b.nsr_id,'tn.nlsr.concept/','') as " . $this->columnHeaders['nsr_id'] . ", " : "" )."
				".( $this->hasCol( 'presence_status' ) ? " _h.index_label as " . $this->columnHeaders['presence_status'] . ", " : "" )."
				".( $this->hasCol( 'presence_status_publication' ) ? " concat(_gl.author,' ',_gl.`date`,' ',_gl.label) as " . $this->columnHeaders['presence_status_publication'] . ", " : "" )."
				".( $this->hasCol( 'habitat' ) ? " _hab.label as " . $this->columnHeaders['habitat'] . ", " : "" )."
				".( $this->hasCol( 'concept_url' ) ? " concat('".$this->concept_url."',replace(_b.nsr_id,'tn.nlsr.concept/','')) as " . $this->columnHeaders['concept_url'] . ", " : "" )."
				".( $this->hasCol( 'database_id' ) ? " _q.taxon_id as " . $this->columnHeaders['database_id'] . ", " : "" )."
				".( $this->hasCol( 'parent_taxon' ) ? " _pnames.name as " . $this->columnHeaders['parent_taxon'] . ", " : "" )."
				".( $this->hasCol( 'parent_taxon_nsr_id' ) ? " replace(_pid.nsr_id,'tn.nlsr.concept/','') as " . $this->columnHeaders['parent_taxon_nsr_id'] . ", " : "" )."
				_q.taxon_id as _taxon_id,
				_t.parent_id as _parent_id,
				_r.id as _base_rank_id
				    
			from %PRE%taxon_quick_parentage _q
				    
			right join %PRE%taxa _t
				on _q.taxon_id = _t.id
				    
			left join %PRE%names _z
				on _q.taxon_id=_z.taxon_id
				and _q.project_id=_z.project_id
				and _z.type_id= ".$this->_nameTypeIds[PREDICATE_PREFERRED_NAME]['id']."
				and _z.language_id=".$this->getDefaultProjectLanguage()."
				    
			left join %PRE%names _names
				on _q.taxon_id=_names.taxon_id
				and _q.project_id=_names.project_id
				and _names.type_id= ".$this->_nameTypeIds[PREDICATE_VALID_NAME]['id']."
				and _names.language_id=".LANGUAGE_ID_SCIENTIFIC."
				    
			".( $this->hasCol( 'habitat' ) ? "
			    
				left join %PRE%presence_taxa _pre
					on _q.taxon_id=_pre.taxon_id
					and _q.project_id=_pre.project_id
			    
				left join %PRE%habitat_labels _hab
					on _pre.habitat_id = _hab.habitat_id
					and _pre.project_id=_hab.project_id
					and _hab.language_id=".LANGUAGE_ID_DUTCH."
			    
			" : "" ).
			
			( $this->hasCol( 'parent_taxon' ) || $this->hasCol( 'parent_taxon_nsr_id' ) ? "
			    
				left join %PRE%taxa _ptaxa
					on _t.parent_id=_ptaxa.id
					and _t.project_id=_ptaxa.project_id
			    
				left join %PRE%names _pnames
					on _ptaxa.id=_pnames.taxon_id
					and _ptaxa.project_id=_pnames.project_id
					and _pnames.type_id= ".$this->_nameTypeIds[PREDICATE_VALID_NAME]['id']."
					and _pnames.language_id=".LANGUAGE_ID_SCIENTIFIC."
			    
				left join %PRE%nsr_ids _pid
					on _ptaxa.project_id = _pid.project_id
					and _ptaxa.id = _pid.lng_id
					and _pid.item_type = 'taxon'
			    
			" : "" )."
			    
			left join %PRE%projects_ranks _f
				on _t.rank_id=_f.id
				and _t.project_id=_f.project_id
			    
			left join %PRE%ranks _r
				on _f.rank_id=_r.id
			    
			left join %PRE%labels_projects_ranks _lpr
				on _f.project_id=_lpr.project_id
				and _f.id=_lpr.project_rank_id
				and _lpr.language_id = " . LANGUAGE_ID_DUTCH . "
				    
			left join %PRE%nsr_ids _b
				on _t.project_id = _b.project_id
				and _t.id = _b.lng_id
				and _b.item_type = 'taxon'
				    
			left join %PRE%presence_taxa _g
				on _t.id=_g.taxon_id
				and _t.project_id=_g.project_id
				    
			left join %PRE%literature2 _gl
				on _g.reference_id=_gl.id
				and _t.project_id=_gl.project_id
				    
			left join %PRE%presence_labels _h
				on _g.presence_id = _h.presence_id
				and _g.project_id=_h.project_id
				and _h.language_id=".LANGUAGE_ID_DUTCH."
				    
			left join %PRE%trash_can _trash
				on _t.project_id = _trash.project_id
				and _t.id =  _trash.lng_id
				and _trash.item_type='taxon'
				    
			where
				_q.project_id=". $this->getCurrentProjectId() ."
				".$ranks_clause."
				".$presence_status_clause."
				and (
					match(_q.parentage) against ('". Controller::generateTaxonParentageId( $this->getBranchTopId() )."' in boolean mode) or
					_q.taxon_id = ".$this->getBranchTopId()."
				)
					    
			and ifnull(_trash.is_deleted,0)=0
					    
			order by " .$this->getOrderBy() . "
			    
			limit " . $this->offset . ',' . $this->limit;

     		$this->names=$this->models->VersatileExportModel->doMainQuery( array("query"=>$this->query) );
			
			if ( $this->getPrintQuery() )
			{
			    $this->queries['main'] = $this->models->VersatileExportModel->getLastQuery();
			}
			
			if ( $this->hasCol( 'sci_name' ) && $this->getDoHybridMarker() )
			{
			    foreach((array)$this->names as $key=>$val)
			    {
			        $this->names[$key]['scientific_name']= $this->addHybridMarkerAndInfixes( [ 'name'=>$val['scientific_name'],'base_rank_id'=>$val['_base_rank_id'], 'taxon_id'=>$val['_taxon_id'],'parent_id'=>$val['_parent_id'] ] );
			    }
			}
			
			if ( $this->hasCol( 'sci_name' ) && $this->getKeepTags()==false )
			{
			    foreach((array)$this->names as $key=>$val)
			    {
			        $this->names[$key]['scientific_name']=html_entity_decode(strip_tags($this->names[$key]['scientific_name']));
			        //$this->names[$key]['scientific_name']=preg_replace('/(\s+)/',' ',strip_tags($val['scientific_name']));
			    }
			}
			
    }
    
    private function findAncestor( $id,$rank_id )
    {
        if ( !isset($this->parentRegister[$id]) )
        {
            $row=$this->models->VersatileExportModel->findAncestor(array(
                "project_id"=>$this->getCurrentProjectId(),
                "taxon_id"=>$id
            ));
            
            $this->parentRegister[$id]=$row;
        }
        else
        {
            $row=$this->parentRegister[$id];
        }
        
        if ($row['rank_id']==$rank_id)
        {
            return $row;
        }
        else
            if (!empty($row['parent_id']))
            {
                return $this->findAncestor($row['parent_id'],$rank_id);
            }
    }
    
    private function doAncestry()
    {
        if ( !$this->hasCol( 'ancestors' ) )
            return;
            
            foreach((array)$this->names as $key => $val)
            {
                foreach((array)$this->getAncestors() as $label=>$id)
                {
                    $d=$this->findAncestor($val['_parent_id'],$id);
                    $this->names[$key][$label]=$d['taxon'];
                }
            }
    }
    
    private function doTraits ()
    {
        if (!$this->getDoTraits()) {
            return;
        }
        
        if (empty($this->tc)) {
            $this->tc = new TraitsTaxonController;
        }
        
        foreach ((array)$this->names as $key => $val) {
            foreach ($this->selectedTraits as $id => $name) {
                list($groupId, $traitId) = explode('-', $id);

                /*
                $traits = [];
                $taxonTraits = $this->tc->getTaxonValues([
                    'taxon' => $val['_taxon_id'],
                    'trait' => $traitId,
                    'group' => $groupId
                ]);
                if (!empty($taxonTraits)) {
                    foreach ($taxonTraits[0]['values'] as $value) {
                        $traits[] = $value['value_start'] . (!empty($value['value_end']) ? '-' . $value['value_end'] : '');
                    }
                }
                $this->names[$key][$name] = implode('|', $traits);
                */

                $value = $this->getTraitValueFromMatrix([
                    'group_id' => $groupId,
                    'trait_id' => $traitId,
                    'taxon_id' => $val['_taxon_id'],
                ]);
                $this->names[$key][$name] = $value;
            }
        }
    }

    private function getTraitValueFromMatrix ($p)
    {
        $groupId = $p['group_id'] ?? null;
        $traitId = $p['trait_id'] ?? null;
        $taxonId = $p['taxon_id'] ?? null;

        if (is_null($groupId) || is_null($traitId) || is_null($taxonId)) {
            return false;
        }

        return $this->models->VersatileExportModel->getTraitValueFromMatrix([
            'group_id' => $groupId,
            'taxon_id' => $taxonId,
            'trait_id' => $traitId,
            'language_id' => $this->getDefaultProjectLanguage(),
            'project_id' => $this->getCurrentProjectId(),
        ]);
    }
    
    private function doSynonymsQuery()
    {
        if ( !$this->getDoSynonyms() )
            return;
            
        // Ruud: restart synonyms for each $names output cycle
        $this->synonyms = [];

        $this->query="
            SELECT
                _names.id,
                _names.name,
                ".( $this->query_bit_name_parts )."
                ".( $this->hasCol( 'database_id' ) ? " _names.id as database_id, " : "" )."
                ".( $this->hasCol( 'rank' ) ? " ifnull(_lpr.label,_r.rank) as " . $this->columnHeaders['rank'] . ", " : "" )."
                _b.nametype,
                _c.language,
                _names.taxon_id as _taxon_id
                    
            FROM
                %PRE%names _names
                    
            left join %PRE%name_types _b
                on _names.project_id=_b.project_id
                and _names.type_id=_b.id
                    
            left join %PRE%languages _c
                on _names.language_id=_c.id
                    
            left join %PRE%projects_ranks _f
                on _names.rank_id=_f.id
                and _names.project_id=_f.project_id
                    
            left join %PRE%ranks _r
                on _f.rank_id=_r.id
                    
            left join %PRE%labels_projects_ranks _lpr
                on _f.project_id=_lpr.project_id
                and _f.id=_lpr.project_rank_id
                and _lpr.language_id = " . LANGUAGE_ID_DUTCH . "
                    
            where
                _names.project_id=".$this->getCurrentProjectId()."
                and _names.type_id in (" . implode(",",(array)$this->getSelectedSynonymTypes()) . ")
                %ID-CLAUSE%
            order by
                _names.name
            ";

        $get_all=count((array)$this->names) >= $this->synonymStrategyThrehold;

        if ( !$get_all )
        {
            $this->query=str_replace('%ID-CLAUSE%','and _names.taxon_id = %ID%',$this->query);
        }
        else
        {
            $all_synonyms=array();
            $q=str_replace('%ID-CLAUSE%','',$this->query);
            $synonyms=$this->models->Names->freeQuery( $q );
            foreach((array)$synonyms as $key=>$val)
            {
                $all_synonyms[$val['_taxon_id']][]=$val;
            }
            unset($synonyms);
        }

        foreach( (array)$this->names as $key=>$val )
        {
            if ( !$get_all )
            {
                $q=str_replace( '%ID%', $val['_taxon_id'], $this->query );
                $synonyms=$this->models->Names->freeQuery( $q );
            }
            else
            {
                $synonyms=isset($all_synonyms[$val['_taxon_id']]) ? $all_synonyms[$val['_taxon_id']] : array();
            }

            foreach( (array)$synonyms as $row )
            {
                $tmp=array();

                if ( $this->hasCol( 'name_parts' ) )
                {
                    foreach($this->getNameParts() as $name_part=>$name_part_state)
                    {
                        $tmp[$name_part]=isset($row[$name_part]) ? $row[$name_part] : null;
                    }
                }
                if ( $this->hasCol( 'database_id' ) )
                {
                    if (isset($row[$this->columnHeaders['database_id']])) $tmp[$this->columnHeaders['database_id']]=$row[$this->columnHeaders['database_id']];
                }

                if ( $this->hasCol( 'rank' ) )
                {
                    if (isset($row[$this->columnHeaders['rank']])) $tmp[$this->columnHeaders['rank']]=$row[$this->columnHeaders['rank']];
                }

                $d=
                array(
                    $this->columnHeaders['synonym']=>isset($row['name']) ? $row['name'] : null,
                    $this->columnHeaders['synonym_type']=>isset($row['nametype']) ? $row['nametype'] : null,
                    $this->columnHeaders['language']=>isset($row['language']) ? $row['language'] : null,
                    $this->columnHeaders['taxon']=>isset($val['scientific_name']) ? $val['scientific_name'] : null,
                );

                if (isset($val['nsr_id']))
                {
                    $d['taxon_nsr_id'] = $val['nsr_id'];
                }

                array_push(
                    $this->synonyms,
                    array_merge($d,$tmp)
                    );

                unset( $d );
            }
        }
    }
    
    private function doOutputStart ()
    {
       
        if ($this->getOutputTarget()=='download') {
            $this->openFileHandlers();
            $this->setFileHandler($this->fhNames);
            $this->printUtf8BOM();
        }
        
        if ($this->getOutputTarget()=='screen')  {
            header('Content-Type: text/html; charset=utf-8');
            $this->print("<pre>",$this->getNewLine());
        }
        
        $this->doQueryParametersOutput();
    }
    
    private function doSynonymsOutputStart ()
    {
        $this->printUtf8BOM();
        $this->doQueryParametersOutput();
    }
    
    private function doOutputEnd ()
    {
        $this->doEOFMarkerOutput();
 
        if ($this->getOutputTarget() == 'download') {
            $this->closeFileHandlers();
            $this->createZipFile();
            $this->deleteTmpFiles();
        }
        
        if ($this->getOutputTarget() == 'screen') {
            $this->print("</pre>",$this->getNewLine());
        }
    }
    
    
    private function doOutput()
    {
        if ( isset($this->spoof_settings->do_spoof_export) && $this->spoof_settings->do_spoof_export )
        {
            $this->doSpoofOutput();
        }
        else
        {
            $this->doNamesOutput();
            $this->doSynonymsOutput();
            $this->doQueriesOutput();
        }
    }
    
    private function doSpoofOutput()
    {
        $this->doQueryParametersOutput();
        $this->printHeaderLine( $this->names );
        $this->printNewLine();
        $this->printNewLine();
        $this->print($this->spoof_settings->texts->download_body);
        $this->printNewLine();
        
        if ( $this->getDoSynonyms() )
        {
            $this->printNewLine();
            $this->printHeaderLine( $this->synonyms );
            $this->printNewLine();
            $this->printNewLine();
            $this->print($this->spoof_settings->texts->download_synonyms);
            $this->printNewLine();
        }
    }
    
   // Used to switch between two file handlers
    private function setFileHandler ($fh)
    {
        $this->fh = $fh;
    }
    
    private function print ($string)
    {
        if ($this->getOutputTarget() == 'screen') {
            echo $string;
        } else if (!empty($this->fh)) {
            fwrite($this->fh, $string);
        }
    }
    
    private function printHeaders()
    {
        header('Content-Type: application/csv');
        header('Content-Disposition: attachment; filename=' . $this->names_file_name);
        header('Pragma: no-cache');
    }
    
    private function openFileHandlers ()
    {
        $basePath = $this->getProjectsMediaStorageDir();
        //$basePath = sys_get_temp_dir() . '/';
        
        $this->names_file_path = $basePath . $this->names_file_name;
        $this->fhNames = fopen($this->names_file_path, 'w');
        
        if ($this->getDoSynonyms()) {
            $this->synonyms_file_path = $basePath . $this->synonyms_file_name;
            $this->fhSynonyms = fopen($this->synonyms_file_path, 'w');
        }
    }
    
    private function closeFileHandlers ()
    {
        foreach ([$this->fhNames, $this->fhSynonyms] as $fh) {
            if (!empty($fh)) {
                fclose($fh);
            }
        }
    }
    
    private function printUtf8BOM()
    {
        if ( !$this->getAddUtf8BOM() )
            return;
            
        //http://stackoverflow.com/questions/5601904/encoding-a-string-as-utf-8-with-bom-in-php
        $this->print(chr(239).chr(187).chr(191));
    }
    
    private function printHeaderLine ($lines)
    {
        $header_line=array();
        
        foreach((array)$lines as $key=>$line)
        {
            if (count((array)$line)>count($header_line))
            {
                $header_line=$line;
            }
        }
        
        // printing headers
        foreach((array)$header_line as $rkey=>$rcol)
        {
            if ( $this->getSuppressUnderscoredFields() && substr($rkey,0,1)=='_' ) continue;
            
            $this->print($this->getEnclosure());
            
            $this->print($this->getReplaceUnderscoresInHeaders() ? str_replace( '_', ' ', $rkey) : $rkey );
            
            $this->print($this->getEnclosure());
            
            $this->print($this->getFieldSep());
        }
        
    }
    
    private function printBodyLines( $body_lines )
    {
        // printing lines
        foreach((array)$body_lines as $line=>$row)
        {
            foreach($row as $key=>$cell)
            {
                if (is_int($key)) continue;
                
                $cell = trim(preg_replace('#\R+#', '   ', $cell));
                
                if ($this->getSuppressUnderscoredFields() && substr($key,0,1)=='_') continue;
                
                $this->print($this->getEnclosure());
                
                $this->print($this->getUtf8ToUtf16() ? mb_convert_encoding($cell,'utf-16','utf-8') : $cell );
                
                $this->print($this->getEnclosure());
                
                $this->print($this->getFieldSep());
                
            }
            
            $this->print($this->getNewLine());
        }
        
    }
    
    private function printNewLine( )
    {
        $this->print($this->getNewLine());
    }
    
    private function getEnclosure ()
    {
        return !$this->getNoQuotes() ? $this->getQuoteChar() : "";
    }
    
    private function doQueryParametersOutput () {
        if (!$this->getDoPrintQueryParameters())
            return;
        
        $d = $this->getTaxonById($this->getBranchTopId());
        $this->print($this->translate("top") . $this->getFieldSep() . $this->getEnclosure() . $d['taxon'] . $this->getEnclosure());
        $this->printNewLine();
        
        $d = array();
        foreach ($this->getRanks() as $val) {
            if (in_array($val["id"], (array) $this->getSelectedRanks())) {
                $d[] = $val["rank"];
            }
        }
        $this->print($this->translate("rangen") . $this->getFieldSep() . $this->getEnclosure() . ($this->getAllRanks() ? $this->translate("(alle)") : $this->translate(
            $this->operators[$this->getRankOperator()]['label']) . " " . implode(", ", $d) . $this->getEnclosure()));
        $this->printNewLine();
        
        $this->print($this->translate("statussen") . $this->getFieldSep() . $this->getEnclosure());
        
        $p = $this->getPresenceStatusLabels();
        if (!empty($p)) {
            $this->print("(" . implode(",", $this->getPresenceStatusLabels()) . ")");
        } else {
            $this->print($this->translate("(alle)"));
        }
        $this->print($this->getEnclosure());
        
        $this->printNewLine();
        
        $this->printNewLine();
    }
    
    private function doNamesOutput()
    {
        $this->setFileHandler($this->fhNames);
        // Ruud: print header line only at start (when offset = 0)
        if ($this->offset == 0) {
            $this->printHeaderLine($this->names);
            $this->printNewLine();
        }
        $this->printBodyLines($this->names);
    }
    
    private function doSynonymsOutput()
    {
        // Ruud: exclude synonyms from screen output
        if (!$this->getDoSynonyms() || $this->getOutputTarget() == 'screen') {
            return;
        }
        $this->setFileHandler($this->fhSynonyms);
        // Ruud: create separate file for synonyms
        if ($this->offset == 0) {
            $this->doSynonymsOutputStart();
            $this->printHeaderLine($this->synonyms);
            $this->printNewLine();
        }
        $this->printBodyLines($this->synonyms);
    }
    
    private function doQueriesOutput()
    {
        if ( !$this->getPrintQuery() )
            return;
            
        $this->printNewLine();
        $this->printNewLine();
        
        foreach((array)$this->queries as $key=>$val)
        {
            $this->print("query:",$key,str_repeat('-',60));
            $this->printNewLine();
            $this->print($val);
            $this->printNewLine();
        }
        $this->printNewLine();
    }
    
    private function setBranchTopId( $branch_top_id )
    {
        $this->branch_top_id=$branch_top_id;
    }
    
    private function getBranchTopId()
    {
        return $this->branch_top_id;
    }
    
    private function setPresenceStatusLabels( $presence_labels )
    {
        $this->presence_labels=$presence_labels;
    }
    
    private function getPresenceStatusLabels()
    {
        return $this->presence_labels;
    }
    
    private function setSelectedRanks( $selected_ranks )
    {
        $this->selected_ranks=!is_null($selected_ranks) ? array_unique($selected_ranks) : null;
    }
    
    private function getSelectedRanks()
    {
        return $this->selected_ranks;
    }
    
    private function setAllRanks( $all_ranks )
    {
        $this->all_ranks=isset($all_ranks) && $all_ranks=='on';
    }
    
    private function getAllRanks()
    {
        return $this->all_ranks;
    }
    
    private function setRankOperator( $rank_operator )
    {
        $this->rank_operator=$rank_operator;
    }
    
    private function getRankOperator()
    {
        return $this->rank_operator;
    }
    
    private function setCols( $cols )
    {
        $this->cols=$cols;
    }
    
    private function getCols()
    {
        return $this->cols;
    }
    
    private function setOrderBy( $order )
    {
        if ( $order=='rank-sci_name' && $this->hasCol( 'rank' ) && $this->hasCol( 'sci_name' ) )
        {
            $this->orderBy='_r.id,_t.taxon';
        }
        else
            if ( $order=='rank-dutch_name' && $this->hasCol( 'rank' ) && $this->hasCol( 'dutch_name' ) )
            {
                $this->orderBy='_r.id,_z.name';
            }
        else
            if ( $order=='sci_name' && $this->hasCol( 'sci_name' ) )
            {
                $this->orderBy='_t.taxon';
            }
        else
            if ( $order=='dutch_name' && $this->hasCol( 'dutch_name' ) )
            {
                $this->orderBy='_z.name';
            }
        else
            if ( $order=='presence_status-sci_name' && $this->hasCol( 'presence_status' ) && $this->hasCol( 'sci_name' ) )
            {
                $this->orderBy='_h.index_label,_t.taxon';
            }
        else
            if ( $order=='presence_status-dutch_name' && $this->hasCol( 'presence_status' ) && $this->hasCol( 'dutch_name' ) )
            {
                $this->orderBy='_h.index_label,_z.name';
            }
    }
    
    private function getOrderBy()
    {
        return $this->orderBy;
    }
    
    private function hasCol( $col )
    {
        return isset($this->cols[$col]) && $this->cols[$col]=='on';
    }
    
    private function setNameParts( $name_parts )
    {
        $this->name_parts=$name_parts;
    }
    
    private function getNameParts()
    {
        return $this->name_parts;
    }
    
    private function setAncestors( $ancestors )
    {
        $this->ancestors=$ancestors;
    }
    
    private function getAncestors()
    {
        return $this->ancestors;
    }
    
    private function setDoSynonyms( $state )
    {
        $this->doSynonyms=isset($state) && $state=='on';
    }
    
    private function getDoSynonyms()
    {
        return $this->doSynonyms;
    }
    
    private function getDoTraits()
    {
        return !empty($this->selectedTraits);
    }
    
    private function setDoHybridMarker( $state )
    {
        $this->doHybridMarker=isset($state) && $state=='on';
    }
    
    private function getDoHybridMarker()
    {
        return $this->doHybridMarker;
    }
    
    private function setSelectedSynonymTypes( $selected_synonym_types )
    {
        $this->selected_synonym_types=$selected_synonym_types;
    }
    
    private function getSelectedSynonymTypes()
    {
        return $this->selected_synonym_types;
    }
    
    private function setFieldSep( $field_sep )
    {
        $field_sep = ($field_sep=='tab' ? "\t" : ($field_sep=='comma' ? "," : $field_sep ));
        $this->field_sep=$field_sep;
    }
    
    private function getFieldSep()
    {
        return $this->field_sep;
    }
    
    private function setNewLine( $new_line )
    {
        $new_line = ( $new_line=="CrLf" ? chr(13).chr(10) : ( $new_line=="Cr" ? chr(13) : chr(10) ) );
        $this->new_line=$new_line;
    }
    
    private function getNewLine()
    {
        return $this->new_line;
    }
    
    private function setNoQuotes( $state )
    {
        $this->no_quotes=isset($state) && $state=='on';
    }
    
    private function getNoQuotes()
    {
        return $this->no_quotes;
    }
    
    private function setQuoteChar( $char )
    {
        $this->quoteChar=$char;
    }
    
    private function getQuoteChar()
    {
        return $this->quoteChar;
    }
    
    private function setReplaceUnderscoresInHeaders( $state )
    {
        $this->replaceUnderscoresInHeaders=isset($state) && $state=='on';
    }
    
    private function getReplaceUnderscoresInHeaders()
    {
        return $this->replaceUnderscoresInHeaders;
    }
    
    private function setDoPrintQueryParameters( $state )
    {
        $this->doPrintQueryParameters=isset($state) && $state=='on';
    }
    
    private function getDoPrintQueryParameters()
    {
        return $this->doPrintQueryParameters;
    }
    
    private function setPrintEOFMarker( $state )
    {
        $this->printEOFMarker=isset($state) && $state=='on';
    }
    
    private function getPrintEOFMarker()
    {
        return $this->printEOFMarker;
    }
    
    private function doEOFMarkerOutput()
    {
        if ($this->getPrintEOFMarker())
        {
            $this->print($this->EOFMarker);
        }
    }
    
    private function setOutputTarget( $target )
    {
        $this->output_target=$target;
    }
    
    private function getOutputTarget()
    {
        return $this->output_target;
    }
    
    private function setUtf8ToUtf16( $state )
    {
        $this->utf8_to_utf16=isset($state) && $state=='on';
    }
    
    private function getUtf8ToUtf16()
    {
        return $this->utf8_to_utf16;
    }
    
    private function setAddUtf8BOM( $state )
    {
        $this->add_utf8_BOM=isset($state) && $state=='on';
    }
    
    private function getAddUtf8BOM()
    {
        return $this->add_utf8_BOM;
    }
    
    private function setKeepTags( $state )
    {
        $this->keep_tags=isset($state) && $state=='on';
    }
    
    private function getKeepTags()
    {
        return $this->keep_tags;
    }
    
    private function setPrintQuery( $state )
    {
        $this->print_query=$state;
    }
    
    private function getPrintQuery()
    {
        return $this->print_query;
    }
    
    private function getSuppressUnderscoredFields()
    {
        return $this->suppress_underscored_fields;
    }
    
    private function setBranchTopSession( $p )
    {
        if ( is_null($p) )
        {
            unset($_SESSION['admin']['user']['export']['selected_branch_top']);
        }
        else
        {
            $_SESSION['admin']['user']['export']['selected_branch_top']=array('id'=>$p[0],'label'=>$p[1]);
        }
    }
    
    private function getBranchTopSession()
    {
        return isset($_SESSION['admin']['user']['export']['selected_branch_top']) ? $_SESSION['admin']['user']['export']['selected_branch_top'] : null;
    }
    
    private function createZipFile ()
    {
        $this->helpers->ZipFile->createArchive(str_replace('.csv', '', $this->names_file_name));
        
        $this->helpers->ZipFile->addFile($this->names_file_path, $this->names_file_name);
        
        
        if ($this->getDoSynonyms()) {
            $this->helpers->ZipFile->addFile($this->synonyms_file_path, $this->synonyms_file_name);
        }
        
        $this->helpers->ZipFile->downloadArchive();
     }
     
     private function deleteTmpFiles ()
     {
         unlink($this->names_file_path);
         
         if ($this->getDoSynonyms()) {
              unlink($this->synonyms_file_path);
         }
     }
    
}

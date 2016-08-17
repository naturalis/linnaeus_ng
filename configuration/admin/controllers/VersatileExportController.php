<?php

include_once ('Controller.php');
include_once ('ModuleSettingsReaderController.php');

class VersatileExportController extends Controller
{

    public $usedModels = array(
		'presence',
		'names',
		'name_types'
    );

    public $modelNameOverride='VersatileExportModel';
    public $controllerPublicName = 'Export';

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
	private $_nameTypeIds;
	private $suppress_underscored_fields=true;
	private $replaceUnderscoresInHeaders=true;
	private $doPrintQueryParameters=true;
	private $printEOFMarker=false;
	private $quoteChar='"';
	private $query_bit_name_parts;
	private $query;
	private $concept_url;
	private $names=array();
	private $synonyms=array();
	private $parentRegister=array();
	private $operators=
		[
			"eq"=>["operator"=>"=","label"=>"is:"],
			"ge"=>["operator"=>">=","label"=>"gelijk aan of onder:"],
			"in"=>["operator"=>"in","label"=>"in:"],
		];

	private $orderBy="_f.rank_id asc,_r.id, _t.taxon";  // rank, rank id, taxon
	private $limit=9999999;
	private $show_nsr_specific_stuff;
	private $spoof_settings;

	/*
		when the number of names is larger than synonymStrategyThrehold, the
		program will fetch all synonyms in one query, and filter and assign them
		to the correct taxon in PHP next. below the threshold, a separate query
		will be executed for each taxon.
		2000 is an arbitrary number, and shoud be calibrated.
	*/
	private $synonymStrategyThrehold=2000;

	private $csv_file_name="%s-export--%s.csv";

	private $EOFMarker='(end of file)';

	private $columnHeaders=[
		'sci_name'=>'scientific_name',
		'dutch_name'=>'dutch_name',
		'rank'=>'rank',
		'nsr_id'=>'nsr_id',
		'presence_status'=>'presence_status',
		'habitat'=>'habitat',
		'concept_url'=>'concept_url',
		'database_id'=>'database_id',
		'parent_taxon'=>'parent_taxon',
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

		$this->moduleSettings=new ModuleSettingsReaderController;
		$this->concept_url=$this->moduleSettings->getGeneralSetting( 'concept_base_url' );
		$this->show_nsr_specific_stuff=$this->moduleSettings->getGeneralSetting( 'show_nsr_specific_stuff' , 0)==1;

		if ( method_exists( $this->customConfig , 'getVersatileExportSpoof' ) ) 
		{
			$this->spoof_settings=$this->customConfig->getVersatileExportSpoof();
			if ( $this->spoof_settings->do_spoof_export )
			{
				$this->smarty->assign( 'spoof_settings_warning', $this->spoof_settings->texts->warning );
			}
		}
		
		foreach ((array)$this->columnHeaders as $key=>$val)
		{
			foreach((array)$this->columnHeaders as $key=>$val)
			{
				$this->columnHeaders[$key]=$this->translate($val);
			}
		}		
    }

    public function exportAction()
    {
        $this->setPageName( $this->translate('Multi-purpose export') );

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

			$this->setNameTypeIds();

			$this->doMainQuery();
			$this->doAncestry();
			$this->doSynonymsQuery();
			$this->doOutput();
		}

		$this->smarty->assign( 'presence_labels', $this->getPresenceStatuses() );
		$this->smarty->assign( 'ranks', $this->getRanks() );
		$this->smarty->assign( 'branch_top', $this->getBranchTopSession() );
		$this->smarty->assign( 'nametypes', $this->_nameTypeIds );
		$this->smarty->assign('is_nsr', $this->show_nsr_specific_stuff);

        $this->printPage();

    }

	private function getPresenceStatuses()
	{
		return $this->models->VersatileExportModel->getPresenceStatuses(array(
			"project_id"=>$this->getCurrentProjectId()
		));
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
				".( $this->hasCol( 'habitat' ) ? " _hab.label as " . $this->columnHeaders['habitat'] . ", " : "" )."
				".( $this->hasCol( 'concept_url' ) ? " concat('".$this->concept_url."',replace(_b.nsr_id,'tn.nlsr.concept/','')) as " . $this->columnHeaders['concept_url'] . ", " : "" )."
				".( $this->hasCol( 'database_id' ) ? " _q.taxon_id as " . $this->columnHeaders['database_id'] . ", " : "" )."
				".( $this->hasCol( 'parent_taxon' ) ? " _pnames.name as " . $this->columnHeaders['parent_taxon'] . ", " : "" )."
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
				and _z.language_id=".LANGUAGE_ID_DUTCH."

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

			( $this->hasCol( 'parent_taxon' ) ? "

				left join %PRE%taxa _ptaxa
					on _t.parent_id=_ptaxa.id
					and _t.project_id=_ptaxa.project_id

				left join %PRE%names _pnames
					on _ptaxa.id=_pnames.taxon_id
					and _ptaxa.project_id=_pnames.project_id
					and _pnames.type_id= ".$this->_nameTypeIds[PREDICATE_VALID_NAME]['id']."
					and _pnames.language_id=".LANGUAGE_ID_SCIENTIFIC."

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
					match(_q.parentage) against ('".$this->getBranchTopId()."' in boolean mode) or
					_q.taxon_id = ".$this->getBranchTopId()."
				)

			and ifnull(_trash.is_deleted,0)=0

			order by " .$this->getOrderBy() . "

			limit " . $this->limit . "

			";

		$this->names=$this->models->VersatileExportModel->doMainQuery( array("query"=>$this->query) );

		if ( $this->hasCol( 'sci_name' ) && $this->getDoHybridMarker() )
		{
			foreach((array)$this->names as $key=>$val)
			{
				$this->names[$key]['scientific_name']=
					$this->addHybridMarkerAndInfixes( array( 'name'=>$val['scientific_name'],'base_rank_id'=>$val['_base_rank_id'] ) );
			}
		}

		if ( $this->hasCol( 'sci_name' ) && $this->getKeepTags()==false )
		{
			foreach((array)$this->names as $key=>$val)
			{
				$this->names[$key]['scientific_name']=preg_replace('/(\s+)/',' ',strip_tags($val['scientific_name']));
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

	private function doSynonymsQuery()
	{
		if ( !$this->getDoSynonyms() )
			return;

		//,replace(_c.nsr_id,'tn.nlsr.name/','') as nsr_id
		/*
			left join %PRE%nsr_ids _c
				on _names.project_id = _c.project_id
				and _names.id = _c.lng_id
				and _c.item_type = 'name'
		*/

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
	
	private function doOutput()
	{
		
		if ( $this->getOutputTarget()=='download' ) $this->printHeaders();
		if ( $this->getOutputTarget()=='download' ) $this->printUtf8BOM();

		if ( $this->getOutputTarget()=='screen' )
		{
			header('Content-Type: text/html; charset=utf-8');
			echo "<pre>",$this->getNewLine();
		}

		if ( isset($this->spoof_settings->do_spoof_export) && $this->spoof_settings->do_spoof_export )
		{
			$this->doSpoofOutput();
		}
		else
		{
			$this->doQueryParametersOutput();
			$this->doNamesOutput();
			$this->doSynonymsOutput();
			$this->doEOFMarkerOutput();
		}

		if ( $this->getOutputTarget()=='screen' ) echo "</pre>",$this->getNewLine();

		die();
	}

	private function doSpoofOutput()
	{
		$this->doQueryParametersOutput();
		$this->printHeaderLine( $this->names );
		$this->printNewLine();
		$this->printNewLine();
		echo $this->spoof_settings->texts->download_body;
		$this->printNewLine();
	
		if ( $this->getDoSynonyms() )
		{
			$this->printNewLine();
			$this->printHeaderLine( $this->synonyms );
			$this->printNewLine();
			$this->printNewLine();
			echo $this->spoof_settings->texts->download_synonyms;
			$this->printNewLine();
		}

		$this->doEOFMarkerOutput();		
	}

	private function printHeaders()
	{
		header('Content-Type: application/csv');
		header('Content-Disposition: attachment; filename='.sprintf($this->csv_file_name,$this->getProjectTitle( true ),date('Ymd-His')));
		header('Pragma: no-cache');
	}

	private function printUtf8BOM()
	{
		if ( !$this->getAddUtf8BOM() )
			return;

		//http://stackoverflow.com/questions/5601904/encoding-a-string-as-utf-8-with-bom-in-php
		echo chr(239).chr(187).chr(191);
	}

	private function printHeaderLine( $lines )
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

			if ( !$this->getNoQuotes() ) echo $this->getQuoteChar();

			echo ( $this->getReplaceUnderscoresInHeaders() ? str_replace( '_', ' ', $rkey) : $rkey );

			if ( !$this->getNoQuotes() ) echo $this->getQuoteChar();

			echo $this->getFieldSep();
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

				if ($this->getSuppressUnderscoredFields() && substr($key,0,1)=='_') continue;

				if ( !$this->getNoQuotes() ) echo $this->getQuoteChar();

				echo ( $this->getUtf8ToUtf16() ? mb_convert_encoding($cell,'utf-16','utf-8') : $cell );

				if ( !$this->getNoQuotes() ) echo $this->getQuoteChar();

				echo $this->getFieldSep();

			}

			echo $this->getNewLine();
		}

	}

	private function printNewLine( )
	{
		echo $this->getNewLine();
	}

	private function doQueryParametersOutput()
	{
		if ( !$this->getDoPrintQueryParameters() )
			return;

		$d = $this->getTaxonById( $this->getBranchTopId() );
		echo
			$this->translate( "top" ),
			$this->getFieldSep(),
			( !$this->getNoQuotes() ? $this->getQuoteChar() : "" ),
			$d['taxon'],
			( !$this->getNoQuotes() ? $this->getQuoteChar() : "" );
		$this->printNewLine();

		$d=array();
		foreach($this->getRanks() as $val)
			if(in_array($val["id"],(array)$this->getSelectedRanks( )))
				$d[]=$val["rank"];
		echo
			$this->translate( "rangen" ),
			$this->getFieldSep(),
			( !$this->getNoQuotes() ? $this->getQuoteChar() : "" ),
			$this->getAllRanks() ? $this->translate( "(alle)" ) : $this->translate($this->operators[$this->getRankOperator()]['label'])," ",implode(", ",$d),
			( !$this->getNoQuotes() ? $this->getQuoteChar() : "" );
		$this->printNewLine();

		echo
			$this->translate( "statussen" ),
			$this->getFieldSep(),
			( !$this->getNoQuotes() ? $this->getQuoteChar() : "" );

			$p = $this->getPresenceStatusLabels();
			if ( !empty( $p ) )
			{
				echo "(",implode(",",$this->getPresenceStatusLabels(  )),")";
			}
			else
			{
				echo $this->translate( "(alle)" );
			}
			echo ( !$this->getNoQuotes() ? $this->getQuoteChar() : "" );

		$this->printNewLine();

		$this->printNewLine();
	}

	private function doNamesOutput()
	{
		$this->printHeaderLine( $this->names );
		$this->printNewLine();
		$this->printBodyLines( $this->names );
	}

	private function doSynonymsOutput()
	{
		if ( !$this->getDoSynonyms() )
			return;

		$this->printNewLine();
		$this->printHeaderLine( $this->synonyms );
		$this->printNewLine();
		$this->printBodyLines( $this->synonyms );
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
			echo $this->EOFMarker;
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

}

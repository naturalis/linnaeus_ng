<?php

include_once ('Controller.php');

class VersatileExportController extends Controller
{

    public $usedModels = array(
		'presence',
		'names',
		'name_types'
    );
   
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
	private $rank_operator;
	private $cols;
	private $name_parts;
	private $ancestors;
	private $doSynonyms;
	private $field_sep;
	private $new_line;
	private $no_quotes;
	private $utf8_to_utf16;
	private $add_utf8_BOM;
	private $output_target;
	private $_nameTypeIds;
	private $suppress_underscored_fields=true;
	private $replaceUnderscoresInHeaders=true;
	private $doPrintQueryParameters=true;
	private $quoteChar='"';
	private $query_bit_name_parts;
	private $query;
	private $concept_url='http://www.nederlandsesoorten.nl/nsr/concept/';
	private $names=array();
	private $synonyms=array();
	private $parentRegister=array();
	private $operators=array("eq"=>"=","ge"=>">=","in"=>"in");

	private $order="_f.rank_id asc,_r.id, _t.taxon";
	private $limit=9999999;

	private $csv_file_name="nsr-export--%s.csv";


    public function __construct ()
    {
        parent::__construct();
    }

    public function __destruct ()
    {
        parent::__destruct();
    }

    public function exportAction()
    {
    
        $this->checkAuthorisation();
       
        $this->setPageName( $this->translate('Multi-purpose export') );
		
		if ($this->rHasVal('action','export'))
		{
			$this->setBranchTopSession( array( $this->rGetVal('branch_top_id'), $this->rGetVal('branch_top_label') ) );
			$this->setBranchTopId( $this->rGetVal('branch_top_id') );
			$this->setPresenceStatusLabels( $this->rGetVal('presence_labels') );
			$this->setSelectedRanks( $this->rGetVal('selected_ranks') );
			$this->setRankOperator( $this->rGetVal('rank_operator') );
			$this->setCols( $this->rGetVal('cols') );
			$this->setNameParts( $this->rGetVal('name_parts') );
			$this->setAncestors( $this->rGetVal('ancestors') );
			$this->setDoSynonyms( $this->rGetVal('synonyms') );
			$this->setFieldSep( $this->rGetVal('field_sep') );
			$this->setNewLine( $this->rGetVal('new_line') );
			$this->setNoQuotes( $this->rGetVal('no_quotes') );
			$this->setUtf8ToUtf16( $this->rGetVal('utf8_to_utf16') );
			$this->setAddUtf8BOM( $this->rGetVal('add_utf8_BOM') );
			$this->setDoPrintQueryParameters( $this->rGetVal('print_query_parameters') );
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

        $this->printPage();
    
    }

	private function getPresenceStatuses()
	{
		return $this->models->Presence->freeQuery("
			select
				_b.index_label,
				_b.label,
				_a.established
			from
				%PRE%presence _a
			left join %PRE%presence_labels _b
				on _a.project_id=_b.project_id
				and _a.id=_b.presence_id
				and _b.language_id=".LANGUAGE_ID_DUTCH."
			where
				_a.project_id = ". $this->getCurrentProjectId() ." 
					and _b.index_label!=''
			order by
				_b.index_label
			");
	}

	private function getRanks()
	{
		return $this->models->ProjectRank->freeQuery("
			select
				_a.*
			from
				%PRE%projects_ranks _b
				
			right join %PRE%ranks _a
				on _a.id=_b.rank_id
				
			where _b.project_id = ". $this->getCurrentProjectId() ."
			order by
				_a.id
			");
	}

	private function setNameTypeIds()
	{
		$this->_nameTypeIds=$this->models->NameTypes->_get(array(
			'id'=>array(
				'project_id'=>$this->getCurrentProjectId()
			),
			'columns'=>'id,nametype',
			'fieldAsIndex'=>'nametype'
		));
	}

	private function doMainQuery()
	{
		$this->query_bit_name_parts='';
		
		if ( $this->hasCol( 'name_parts' ) ) 
		{
			foreach($this->getNameParts() as $key=>$val)
			{
				$this->query_bit_name_parts.='_names.'.$key.' as '.$key.', ';
			}
		}
		
		$ranks_clause="and _f.rank_id ".$this->operators[$this->getRankOperator()] . " (".implode( "," , $this->getSelectedRanks() ).")";

		$presence_status_clause="";
		$p = $this->getPresenceStatusLabels();
		if ( !empty( $p ) )
		{
			$presence_status_clause="and _h.index_label in ('".implode("','",$p)."') ";
		}

		$this->query="
			select
				".( $this->hasCol( 'sci_name' ) ? " _t.taxon as wetenschappelijke_naam, " : "" )."
				".( $this->query_bit_name_parts )."
				".( $this->hasCol( 'dutch_name' ) ? " _z.name as nederlandse_naam, " : "" )."
				".( $this->hasCol( 'rank' ) ? " _r.rank as rang, " : "" )."
				".( $this->hasCol( 'nsr_id' ) ? " replace(_b.nsr_id,'tn.nlsr.concept/','') as nsr_id, " : "" )."
				".( $this->hasCol( 'presence_status' ) ? " _h.index_label as voorkomens_status, " : "" )."
				".( $this->hasCol( 'habitat' ) ? " _hab.label as habitat, " : "" )."
				".( $this->hasCol( 'concept_url' ) ? " concat('".$this->concept_url."',replace(_b.nsr_id,'tn.nlsr.concept/','')) as concept_url, " : "" )."
				_q.taxon_id as _taxon_id,
				_t.parent_id as _parent_id

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
			
			" : "" )."
			
			left join %PRE%projects_ranks _f
				on _t.rank_id=_f.id
				and _t.project_id=_f.project_id
			
			left join %PRE%ranks _r
				on _f.rank_id=_r.id
			
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
				and match(_q.parentage) against ('".$this->getBranchTopId()."' in boolean mode)
				
			and ifnull(_trash.is_deleted,0)=0
			
			order by " .$this->order . "
			
			limit " . $this->limit . "
			
			";

		if ( !empty($this->query) )
		{
			$this->names=$this->models->Taxon->freeQuery( $this->query );
		}

	}

	private function findAncestor( $id,$rank_id )
	{
		if ( !isset($this->parentRegister[$id]) )
		{
			$r=$this->models->Taxon->freeQuery("
				select
					_t.taxon,_t.id, _t.parent_id, _f.rank_id
				from
					taxa  _t
				left join projects_ranks _f
					on _t.rank_id=_f.id
					and _t.project_id=_f.project_id
				where 
					_t.project_id=1
					and _t.id = ".$id
			);

			$row=$r[0];
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
				".($this->query_bit_name_parts)."	
				_b.nametype
			
			FROM 
				%PRE%names _names
			
			left join %PRE%name_types _b
				on _names.project_id=_b.project_id
				and _names.type_id=_b.id
			
			where
				_names.project_id=".$this->getCurrentProjectId()." 
				and _names.type_id in (".$this->_nameTypeIds[PREDICATE_SYNONYM]['id'].",".$this->_nameTypeIds[PREDICATE_SYNONYM_SL]['id']	.") 
				and _names.taxon_id = %ID%
			";

 			
		foreach( (array)$this->names as $key=>$val )
		{
			$q=str_replace( '%ID%', $val['_taxon_id'], $this->query );
			$synonyms=$this->models->Names->freeQuery( $q );

			foreach( (array)$synonyms as $row )
			{
				$tmp=array();

				if ( $this->hasCol( 'name_parts' ) )
				{
					if (isset($row['uninomial'])) $tmp['uninomial']=$row['uninomial'];
					if (isset($row['specific_epithet'])) $tmp['specific_epithet']=$row['specific_epithet'];
					if (isset($row['infra_specific_epithet'])) $tmp['infra_specific_epithet']=$row['infra_specific_epithet'];
					if (isset($row['authorship'])) $tmp['authorship']=$row['authorship'];
					if (isset($row['name_author'])) $tmp['name_author']=$row['name_author'];
					if (isset($row['authorship_year'])) $tmp['authorship_year']=$row['authorship_year'];
				}

				array_push(
					$this->synonyms,
					array_merge(
						array(
							'synoniem'=>$row['name'],
							//'nsr_id'=>$row['nsr_id'],
							'type_synoniem'=>$row['nametype'],
							'taxon'=>$val['wetenschappelijke_naam'],
							'taxon_nsr_id'=>$val['nsr_id'],
						),
						$tmp
					)
				);
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
			
		$this->doQueryParametersOutput();
		$this->doNamesOutput();
		$this->doSynonymsOutput();

		if ( $this->getOutputTarget()=='screen' ) echo "</pre>",$this->getNewLine();

		die();
	}

	private function printHeaders()
	{
		header('Content-Type: application/csv');
		header('Content-Disposition: attachment; filename='.sprintf($this->csv_file_name,date('Ymd-His')));
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
			"top",
			$this->getFieldSep(),
			( !$this->getNoQuotes() ? $this->getQuoteChar() : "" ),
			$d['taxon'],
			( !$this->getNoQuotes() ? $this->getQuoteChar() : "" );
		$this->printNewLine();

		$d=array();
		foreach($this->getRanks() as $val)
			if(in_array($val["id"],$this->getSelectedRanks( )))
				$d[]=$val["rank"];
		echo
			"rang",
			$this->getFieldSep(),
			( !$this->getNoQuotes() ? $this->getQuoteChar() : "" ),
			$this->operators[$this->getRankOperator()]," ",implode(", ",$d),
			( !$this->getNoQuotes() ? $this->getQuoteChar() : "" );
		$this->printNewLine();

		echo
			"statussen",
			$this->getFieldSep(),
			( !$this->getNoQuotes() ? $this->getQuoteChar() : "" );

			$p = $this->getPresenceStatusLabels();
			if ( !empty( $p ) )
			{
				echo "(",implode(",",$this->getPresenceStatusLabels(  )),")";
			}
			else
			{
				echo "(alle)";
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
		$this->selected_ranks=array_unique($selected_ranks);
	}

	private function getSelectedRanks()
	{
		return $this->selected_ranks;
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

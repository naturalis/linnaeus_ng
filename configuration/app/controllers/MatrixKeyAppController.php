<?php

include_once ('Controller.php');
class MatrixKeyAppController extends Controller
{
    public $usedModels = array(
        'matrix', 
        'matrix_name', 
        'matrix_taxon', 
        'matrix_taxon_state', 
        'commonname', 
        'characteristic', 
        'characteristic_matrix', 
        'characteristic_label', 
        'characteristic_state', 
        'characteristic_label_state', 
        'chargroup_label', 
        'chargroup', 
        'characteristic_chargroup', 
        'matrix_variation', 
        'nbc_extras', 
        'variation_relations',
		'gui_menu_order'
    );

	public function appControllerInterfaceAction()
	{
		
		/*
			used exclusively by the Javascript app-controller object
			implemented in the web-enabled version of the linnaeus mobile-app

			[request]
			action: action to execute (query)
			query: query to execute (states)
			language: language ID for labels (24)
			matrix: active matrix ID (542
			states: imploded list of state ID's (28037,28062,28267)
			force: force states to have images, discard them if not (1|0)
			time: timestamp against caching of Ajax-calls

		*/
		
		$res=null;

	
		if (!$this->rHasVal('action')) {
			
            $res='error (request lacks an action)';
	
        }
        else if ($this->rHasVal('action', 'query')) {
			
			$data = $this->requestData;
  			
			$functions = array(
				'states' => '_appControllerGetStates',
				'results' => '_appControllerGetResults',
				'detail' => '_appControllerGetDetail'
			);

			$res=$this->$functions[$data['query']]($data);
			
		}
		
		echo json_encode($res);
	
	}

	private function _appControllerGetStates($data)
	{

		function makeCharacterIconName($label)
		{
			return '__menu'.preg_replace('/\W/i','',ucwords($label)).'.png';
		}
		
		$resTaxa = isset($data['results']['taxa']) ? $data['results']['taxa'] : array();
		$resVar = isset($data['results']['variations']) ? $data['results']['variations'] : array();
	
		$selStates=isset($data['states']) ? preg_split('/,/',$data['states'],-1,PREG_SPLIT_NO_EMPTY) : array();
		$stateList=array();

		$count = $this->models->MatrixTaxonState->freeQuery(
			array(
				'query' =>
			"select case count(1) when 0 then -1 else 0 end as can_select, state_id
			from %TABLE%
			where project_id = ".$this->getCurrentProjectId()." and matrix_id = ".$data['matrix'].
			(count($resTaxa)!=0 ? " and taxon_id in (".implode(',',$resTaxa).") " : "" ).
			(count($selStates)!=0 ? " and state_id not in (".$data['states'].") " : "")."
			group by state_id
				union all
			select distinct -1 as can_select, state_id
			from %TABLE%
			where project_id = ".$this->getCurrentProjectId()." and matrix_id = ".$data['matrix']." 
			and taxon_id not in (".(count($resTaxa)!=0 ? implode(',',$resTaxa) : "" ).") 
			and state_id not in (
				select state_id from %TABLE%
				where project_id = ".$this->getCurrentProjectId()." and matrix_id = ".$data['matrix'].
				(count($resTaxa)!=0 ? " and taxon_id in (".implode(',',$resTaxa).") " : "" ).
				(count($selStates)!=0 ? " and state_id not in (".$data['states'].") " : "").
			")
				union all
			select 1 as can_select, id as state_id
			from %PRE%characteristics_states
			where project_id = ".$this->getCurrentProjectId()." 
			and id in (".(count($selStates)!=0 ?  $data['states'] : '-1' ).") ",
				'fieldAsIndex' => 'state_id'
			)
		);
q($this->models->MatrixTaxonState->q());
		$menu = $this->models->GuiMenuOrder->freeQuery(
			"select 
				_a.ref_id as id,'character' as type,_a.show_order as show_order,
				if(locate('|',_b.label)=0,_b.label,substring(_b.label,1,locate('|',_b.label)-1)) as label,
			if(locate('|',_b.label)=0,_b.label,substring(_b.label,locate('|',_b.label)+1)) as description
			from %TABLE% _a
			left join %PRE%characteristics_labels _b on _b.characteristic_id = _a.ref_id and _b.language_id = ".$data['language']."
			where 
				_a.project_id = ".$this->getCurrentProjectId()."
				and _a.matrix_id = ".$data['matrix']."
				and _a.ref_type='char'
			union all
			select 
				_a.ref_id as id,'c_group' as type,_a.show_order as show_order, _c.label as label, null as description from %TABLE% _a
			left join %PRE%chargroups_labels _c on _c.chargroup_id = _a.ref_id and _c.language_id = ".$data['language']."
			where
				_a.project_id = ".$this->getCurrentProjectId()."
				and _a.matrix_id = ".$data['matrix']."
				and _a.ref_type='group'
			order by show_order,label"
		);
		
		foreach((array)$menu as $key=>$val) {

			unset($menu[$key]['show_order']);

			$menu[$key]['img']=makeCharacterIconName($val['label']);

			if ($val['type']=='character') {

				$menu[$key]['img']=makeCharacterIconName($val['label']);
	
				$menu[$key]['states']=$this->models->CharacteristicState->freeQuery(
					"select _a.id,_c.label,_a.file_name as img,'0' as select_state, _a.show_order
					from %TABLE% _a
					left join %PRE%characteristics_labels_states _c on _a.id = _c.state_id and _c.language_id = ".$data['language']." and _c.project_id = ".$this->getCurrentProjectId()."
					where _a.characteristic_id = ".$val['id']." and _a.project_id = ".$this->getCurrentProjectId()."
					order by _a.show_order,_c.label"
				);

				$hasSelected=false;
				foreach((array)$menu[$key]['states'] as $sKey => $sVal) {
					unset($menu[$key]['states'][$sKey]['show_order']);
					$menu[$key]['states'][$sKey]['select_state']=isset($count[$sVal['id']]['can_select']) ? $count[$sVal['id']]['can_select'] : 0;
					if ($menu[$key]['states'][$sKey]['select_state']=='1') $hasSelected=true;
					if (isset($data['force']) && $data['force']=='1' && empty($sVal['img']))
						unset($menu[$key]['states'][$sKey]);
					if (in_array($sVal['id'],$selStates)) {
						array_push($stateList,array_merge($sVal,array('character'=>array('id'=>$val['id'],'label'=>$val['label']))));
					}
				}
				
				$menu[$key]['hasStates']=count((array)$menu[$key]['states'])>0;
				$menu[$key]['hasSelected']=$hasSelected;

			} else
			if ($val['type']=='c_group') {
				
				$c = $this->models->CharacteristicChargroup->freeQuery(
					"select _a.characteristic_id as id,'character' as type,_a.show_order as show_order,
					if(locate('|',_c.label)=0,_c.label,substring(_c.label,1,locate('|',_c.label)-1)) as label,
					if(locate('|',_c.label)=0,_c.label,substring(_c.label,locate('|',_c.label)+1)) as description					
					from %TABLE% _a
					left join %PRE%characteristics _b on _a.characteristic_id = _b.id
					left join %PRE%characteristics_labels _c on _a.characteristic_id = _c.characteristic_id and _c.language_id = ".$data['language']." and _c.project_id = ".$this->getCurrentProjectId()."
					where _a.chargroup_id = ".$val['id']. " and _a.project_id = ".$this->getCurrentProjectId()."
					order by label"
				);

				$hasSelectedGroup=false;

				foreach((array)$c as $cKey=>$cVal) {
					
					$c[$cKey]['img']=makeCharacterIconName($val['label']);

					$c[$cKey]['states']=$this->models->CharacteristicState->freeQuery(
						"select _a.id,_c.label,_a.file_name as img,'0' as select_state, _a.show_order
						from %TABLE% _a
						left join %PRE%characteristics_labels_states _c on _a.id = _c.state_id and _c.language_id = ".$data['language']." and _c.project_id = ".$this->getCurrentProjectId()."
						where _a.characteristic_id = ".$cVal['id']." and _a.project_id = ".$this->getCurrentProjectId()."
						order by _a.show_order,_c.label"
					);
					
					$hasSelected=false;
					foreach((array)$c[$cKey]['states'] as $sKey => $sVal) {
						unset($c[$cKey]['states'][$sKey]['show_order']);
						$c[$cKey]['states'][$sKey]['select_state']=isset($count[$sVal['id']]['can_select']) ? $count[$sVal['id']]['can_select'] : 0;
						if ($c[$cKey]['states'][$sKey]['select_state']=='1') $hasSelected=true;
						if (isset($data['force']) && $data['force']=='1' && empty($sVal['img']))
							unset($c[$cKey]['states'][$sKey]);
						if (in_array($sVal['id'],$selStates))
							array_push($stateList,array_merge($sVal,array('character'=>array('id'=>$val['id'],'label'=>$val['label']))));
					}
					
					$c[$cKey]['hasStates']=count((array)$c[$cKey]['states'])>0;
					$c[$cKey]['hasSelected']=$hasSelected;
					
					if ($hasSelected) $hasSelectedGroup=true;

				}
				
				$menu[$key]['characters']=$c;
				$menu[$key]['hasCharacters']=count((array)$c)>0;
				$menu[$key]['hasSelected']=$hasSelectedGroup;
				
			}
		
		}

		return array('all'=>$menu,'active'=>$stateList);

	}

	private function _appControllerGetResults($data)
	{
		
		$selStateCount=count(isset($data['states']) ? preg_split('/,/', $data['states'],-1,PREG_SPLIT_NO_EMPTY) : array());

		if ($selStateCount==0) {
			
			$res=$this->models->MatrixTaxon->freeQuery("
				select 'taxon' as type, _a.taxon_id as id, 0 as total_states, 100 as score,_c.is_hybrid as is_hybrid, trim(_c.taxon) as sci_name, trim(_d.commonname) as label,_e.value as url_thumbnail
				from %TABLE% _a
				left join %PRE%taxa _c on _a.taxon_id = _c.id and _c.project_id = ".$this->getCurrentProjectId()."
				left join %PRE%commonnames _d on _d.taxon_id = _a.taxon_id and _d.project_id = ".$this->getCurrentProjectId()." and _d.language_id = ".$data['language']." 
				left join %PRE%nbc_extras _e on _c.id = _e.ref_id and _e.ref_type='taxon' and _e.name='url_thumbnail' and _e.project_id = ".$this->getCurrentProjectId()."
				where _a.project_id = ".$this->getCurrentProjectId()."
				group by _a.taxon_id
				union all
				select 'variation' as type, _a.variation_id as id, 0 as total_states, 100 as score,0 as is_hybrid, trim(_d.taxon) as sci_name, trim(_c.label) as label, _e.value as url_thumbnail
				from  %PRE%matrices_variations _a
				left join %PRE%taxa_variations _c on _a.variation_id = _c.id and _c.project_id = ".$this->getCurrentProjectId()."
				left join %PRE%taxa _d on _c.taxon_id = _d.id and _d.project_id = ".$this->getCurrentProjectId()."
				left join %PRE%nbc_extras _e on _a.variation_id = _e.ref_id and _e.ref_type='variation' and _e.name='url_thumbnail' and _e.project_id = ".$this->getCurrentProjectId()."
				where _a.project_id = ".$this->getCurrentProjectId()."
				group by _a.variation_id
				order by label"
			);

		} else {

			$res=$this->models->MatrixTaxon->freeQuery("
				select 'taxon' as type, _a.taxon_id as id, count(_b.state_id) as total_states,
				round((case when count(_b.state_id)>".$selStateCount." then ".$selStateCount." else count(_b.state_id) end/".$selStateCount.")*100,0) as score,
				_c.is_hybrid as is_hybrid, trim(_c.taxon) as sci_name, trim(_d.commonname) as label,_e.value as url_thumbnail
				from %TABLE% _a
				left join %PRE%matrices_taxa_states _b on _a.project_id = _b.project_id and _a.matrix_id = _b.matrix_id and _a.taxon_id = _b.taxon_id and (_b.state_id in (".$data['states'].")) and _b.project_id = ".$this->getCurrentProjectId()."
				left join %PRE%taxa _c on _a.taxon_id = _c.id  and _c.project_id = ".$this->getCurrentProjectId()."
				left join %PRE%commonnames _d on _d.taxon_id = _a.taxon_id and _d.project_id = ".$this->getCurrentProjectId()." and _d.language_id = ".$data['language']." 
				left join %PRE%nbc_extras _e on _c.id = _e.ref_id and _e.ref_type='taxon' and _e.name='url_thumbnail' and _e.project_id = ".$this->getCurrentProjectId()."
				where _a.project_id = ".$this->getCurrentProjectId()."
				group by _a.taxon_id having score=100
				union all
				select 'variation' as type, _a.variation_id as id, count(_b.state_id) as total_states,
				round((case when count(_b.state_id)>".$selStateCount." then ".$selStateCount." else count(_b.state_id) end/".$selStateCount.")*100,0) as score,
				0 as is_hybrid, trim(_d.taxon) as sci_name, trim(_c.label) as label, _e.value as url_thumbnail
				from  %PRE%matrices_variations _a
				left join %PRE%matrices_taxa_states _b on _a.project_id = _b.project_id and _a.matrix_id = _b.matrix_id and _a.variation_id = _b.variation_id and (_b.state_id in (".$data['states'].")) and _b.project_id = ".$this->getCurrentProjectId()."
				left join %PRE%taxa_variations _c on _a.variation_id = _c.id and _c.project_id = ".$this->getCurrentProjectId()."
				left join %PRE%taxa _d on _c.taxon_id = _d.id and _d.project_id = ".$this->getCurrentProjectId()."
				left join %PRE%nbc_extras _e on _a.variation_id = _e.ref_id and _e.ref_type='variation' and _e.name='url_thumbnail' and _e.project_id = ".$this->getCurrentProjectId()."
				where _a.project_id = ".$this->getCurrentProjectId()."
				group by _a.variation_id having score=100
				order by score,label"
			);
		}
		
		return $res;
					
	}
			
	private function _appControllerGetDetail($data)
	{

		$t=$this->models->Taxon->freeQuery("
			select t.id,trim(replace(t.taxon,'%VAR%','')) as name_sci, c.commonname as name_nl, 
				p.id as group_id, p.taxon as groupname_sci, pc.commonname as groupname_nl, 'taxon' as type 
			from %PRE%taxa t
			left join %PRE%commonnames c 
				on c.taxon_id = t.id 
				and c.project_id = ".$this->getCurrentProjectId()."
			left join %PRE%taxa p 
				on t.parent_id = p.id 
				and p.project_id = ".$this->getCurrentProjectId()."
			left join %PRE%commonnames pc 
				on pc.taxon_id = p.id 
				and pc.project_id = ".$this->getCurrentProjectId()."
			where t.id = ".$data['id']."
			and t.project_id = ".$this->getCurrentProjectId()
		);
		$res=$t[0];


		$t=$this->models->Taxon->freeQuery("
			select _b.title,_a.content, _c.page 
			from %PRE%content_taxa _a
			left join %PRE%pages_taxa_titles _b 
				on _a.page_id = _b.page_id 
				and _b.project_id = ".$this->getCurrentProjectId()."
			left join %PRE%pages_taxa _c 
				on _a.page_id = _c.id 
				and _c.project_id = ".$this->getCurrentProjectId()."
			where _a.taxon_id = ".$data['id']."
			and _a.project_id = ".$this->getCurrentProjectId()
		);
		$res['content']=$t;


		$t=$this->models->Taxon->freeQuery("
			select _a.value as file_name,_b.value as copyright, '1' as overview_image 
			from %PRE%nbc_extras _a
			left join %PRE%nbc_extras _b 
				on _b.ref_type = 'taxon' 
				and _b.ref_id=_a.ref_id 
				and _b.name='photographer' 
				and _b.project_id = ".$this->getCurrentProjectId()."
			where _a.ref_id = ".$data['id']." 
				and _a.ref_type='taxon' 
				and _a.name='url_image'
			and _a.project_id = ".$this->getCurrentProjectId()
		);
		$res['img_main']=$t;


		$t=$this->models->Taxon->freeQuery("
			select file_name from %PRE%media_taxon where taxon_id = ".$data['id']." and project_id = ".$this->getCurrentProjectId()
		);
		$res['img_other']=$t;


		$t=$this->models->Taxon->freeQuery("
			select 'taxon' as type, _b.id as id, _b.taxon as taxon,_c.commonname as label, _n.value as img 
			from %PRE%taxa_relations _a 
			left join %PRE%taxa _b 
				on _b.id = _a.relation_id 
				and _b.project_id = ".$this->getCurrentProjectId()."
			left join %PRE%commonnames _c 
				on _c.taxon_id = _b.id 
				and _c.project_id = ".$this->getCurrentProjectId()."
			left join %PRE%nbc_extras _n 
				on _b.id = _n.ref_id 
				and _n.ref_type='taxon' 
				and _n.name='url_thumbnail' 
				and _n.project_id = ".$this->getCurrentProjectId()."
			where _a.ref_type='taxon' and _a.taxon_id = ".$data['id']." 
			and _a.project_id = ".$this->getCurrentProjectId()."
			union all
			select 'variation' as type, _e.id as id,  _f.taxon as taxon, _e.label as label, _n.value as img 
			from %PRE%taxa_relations _d 
			left join %PRE%taxa_variations _e 
				on _e.id = _d.relation_id 
				and _e.project_id = ".$this->getCurrentProjectId()."
			left join %PRE%taxa _f 
				on _f.id = _d.taxon_id 
				and _f.project_id = ".$this->getCurrentProjectId()."
			left join %PRE%nbc_extras _n 
				on _d.taxon_id = _n.ref_id 
				and _n.ref_type='taxon' 
				and _n.name='url_thumbnail' 
				and _n.project_id = ".$this->getCurrentProjectId()."
			where _d.ref_type='variation'  
			and _d.taxon_id =".$data['id']." 
			and _d.project_id = ".$this->getCurrentProjectId()
		);
		$res['similar']=$t;

		return $res;
					
	}
			

}	
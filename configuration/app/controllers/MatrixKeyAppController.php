<?php

include_once ('Controller.php');
class MatrixKeyAppController extends Controller
{
    public $usedModels = array(
		'matrices',
		'matrices_names',
		'matrices_taxa',
		'matrices_taxa_states',
		'matrices_variations',
		'commonnames',
		'characteristics',
		'characteristics_chargroups',
		'characteristics_labels',
		'characteristics_labels_states',
		'characteristics_matrices',
		'characteristics_states',
		'chargroups',
		'chargroups_labels',
        'nbc_extras',
        'variation_relations',
		'gui_menu_order'
    );

	public $modelNameOverride='MatrixKeyAppModel';

	public function appControllerInterfaceAction()
	{

		/*
			used exclusively by the Javascript app-controller object
			implemented in the web-enabled version of the linnaeus mobile-app

			[request]
			action: action to execute (query)
			query: query to execute (states)
			language: language ID for labels (24)
			matrix: active matrix ID (542)
			states: imploded list of state ID's (28037,28062,28267)
			force: force states to have images, discard them if not (1|0)
			time: timestamp against caching of Ajax-calls

		*/

		$res=null;


		if (!$this->rHasVal('action'))
		{
            $res='error (request lacks an action)';
        }
        else
		if (!$this->rHasVal('matrix'))
		{
            $res='error (request lacks a matrix id)';
        }
        else
		if ($this->rHasVal('action', 'query'))
		{
			$data = $this->rGetAll();

			$functions = array(
				'states' => '_appControllerGetStates',
				'results' => '_appControllerGetResults',
				'detail' => '_appControllerGetDetail'
			);

			if (!isset($data['language']))
				$data['language']=$this->getDefaultLanguageId();

			$res=$this->{$functions[$data['query']]}($data);
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

		$count = $this->models->MatrixKeyAppModel->getStateCount(array(
			"project_id = ".$this->getCurrentProjectId(),
			"matrix_id = ".$data['matrix'],
			"taxa"=>$resTaxa,
			"states"=>$data['states']
		));

		$menu=$this->getGuiMenuOrder($data['matrix'],$data['language']);

		foreach((array)$menu as $key=>$val)
		{
			unset($menu[$key]['show_order']);

			$menu[$key]['img']=makeCharacterIconName($val['label']);

			if ($val['type']=='character')
			{
				$menu[$key]['states']=$this->models->MatrixKeyAppModel->getCharacteristicStates(array(
					"language_id"=>$data['language'],
					"project_id"=>$this->getCurrentProjectId(),
					"characteristic_id"=>$val['id']
				));

				$hasSelected=false;
				foreach((array)$menu[$key]['states'] as $sKey => $sVal)
				{
					unset($menu[$key]['states'][$sKey]['show_order']);

					$menu[$key]['states'][$sKey]['select_state']=
						isset($count[$sVal['id']]['can_select']) ? $count[$sVal['id']]['can_select'] : 0;

					if ($menu[$key]['states'][$sKey]['select_state']=='1') $hasSelected=true;

					if (isset($data['force']) && $data['force']=='1' && empty($sVal['img']))
						unset($menu[$key]['states'][$sKey]);

					if (in_array($sVal['id'],$selStates))
					{
						$stateList[]=
							array_merge(
								$sVal,
								array(
									'display'=>array_search($sVal['id'],$selStates),
									'character'=>array('id'=>$val['id'],'label'=>$val['label'])
								)
							);
					}
				}

				$menu[$key]['hasStates']=count((array)$menu[$key]['states'])>0;
				$menu[$key]['hasSelected']=$hasSelected;

			} else
			if ($val['type']=='c_group')
			{

				$c = $this->models->MatrixKeyAppModel->getChargroupCharacteristics(array(
					"language_id"=>$data['language'],
					"project_id"=>$this->getCurrentProjectId(),
					"chargroup_id"=>$val['id']
				));

				$hasSelectedGroup=false;

				foreach((array)$c as $cKey=>$cVal)
				{

					$c[$cKey]['img']=makeCharacterIconName($val['label']);

					$c[$cKey]['states']=$this->models->MatrixKeyAppModel->getCharacteristicStates(array(
						"language_id"=>$data['language'],
						"project_id"=>$this->getCurrentProjectId(),
						"characteristic_id"=>$cVal['id']
					));

					$hasSelected=false;
					foreach((array)$c[$cKey]['states'] as $sKey => $sVal)
					{
						unset($c[$cKey]['states'][$sKey]['show_order']);

						$c[$cKey]['states'][$sKey]['select_state']=
							isset($count[$sVal['id']]['can_select']) ? $count[$sVal['id']]['can_select'] : 0;

						if ($c[$cKey]['states'][$sKey]['select_state']=='1') $hasSelected=true;

						if (isset($data['force']) && $data['force']=='1' && empty($sVal['img']))
							unset($c[$cKey]['states'][$sKey]);

						if (in_array($sVal['id'],$selStates))
						{
							$stateList[]=
								array_merge(
									$sVal,
									array(
										'display'=>array_search($sVal['id'],$selStates),
										'character'=>
											array('id'=>$val['id'],'label'=>$cVal['label'])
										)
									);
						}
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
		usort($stateList,function ($a,$b) {return $a['display']>$b['display'] ? 1 : -1;});

		return array('all'=>$menu,'active'=>$stateList);
	}

	private function _appControllerGetResults( $data )
	{
		return
			$this->models->MatrixKeyAppModel->getResults(array(
				"project_id"=>$this->getCurrentProjectId(),
				"language_id"=>$data['language'],
				"states"=>isset($data['states']) ? $data['states'] : null,
			));
	}

	private function _appControllerGetDetail($data)
	{
		$res=$this->models->MatrixKeyAppModel->getDetail(array(
			"project_id"=>$this->getCurrentProjectId(),
			"taxon_id"=>$data['id']
		));

		$t=$this->models->Taxa->_get(array('id'=>array('project_id' => $this->getCurrentProjectId(),'parent_id'=>$data['id'])));
		foreach((array)$t as $key => $val)
		{
			$t[$key]['label']=$this->getCommonname($val['id']);
			$t[$key]['img']=$this->getNbcExtras(array('id'=>$val['id'],'name'=>'url_thumbnail'));
		}
		if ($t)
		{
			usort($t,function($a,$b) {return ($a['label']>$b['label']?1:-1);});
			$res['children']=$t;
		}

		return $res;

	}

	private function getGuiMenuOrder($mId,$lId)
	{
		if (!isset($_SESSION['app'][$this->spid()]['matrix'][$mId][$lId]['guiMenuOrder']))
		{
			$_SESSION['app'][$this->spid()]['matrix'][$mId][$lId]['guiMenuOrder']=
				$this->models->MatrixKeyAppModel->getGuiMenuOrder(array(
					"language_id"=>$lId,
					"project_id"=>$this->getCurrentProjectId(),
					"matrix_id"=>$mId
				));
		}

		return $_SESSION['app'][$this->spid()]['matrix'][$mId][$lId]['guiMenuOrder'];

	}

	private function getCommonname($tId)
	{
		$c = $this->models->Commonname->_get(
		array(
			'id' => array(
				'project_id' => $this->getCurrentProjectId(),
				'taxon_id' => $tId,
				'language_id' => $this->getCurrentLanguageId()
			)
		));

		return $c[0]['commonname'];

	}




}
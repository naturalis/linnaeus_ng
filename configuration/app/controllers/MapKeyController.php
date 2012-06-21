<?php

include_once ('Controller.php');

class MapKeyController extends Controller
{
    
    public $usedModels = array(
		'map',
		'map_name',
		'occurrence_taxon',
		'geodata_type',
		'geodata_type_title',
		'diversity_index',
		'l2_occurrence_taxon',
		'l2_map'
	);
    
    public $usedHelpers = array('csv_parser_helper');

    public $controllerPublicName = 'Distribution';

	public $cssToLoad = array(
		'basics.css',
		'map.css',
		'map_l2.css',
		'lookup.css',
		'dialog/jquery.modaldialog.css'
	);

	public $jsToLoad = array(
		'all' => 
			array(
				'main.js',
				'mapkey.js',
				'mapkey_l2.js',
				'http://maps.google.com/maps/api/js?sensor=false&libraries=drawing',
				'lookup.js',
				'dialog/jquery.modaldialog.js'
			)
		);

	private $_mapType = 'lng';


    /**
     * Constructor, calls parent's constructor
     *
     * @access     public
     */
    public function __construct ()
    {
        
        parent::__construct();

		$this->checkForProjectId();

		$this->setCssFiles();

		$this->smarty->assign('isOnline',$this->checkRemoteServerAccessibility());
		
		$this->_mapType = $this->getDistributionMapType();
		
		$this->removeGoogleMapsJS();

		$this->smarty->assign('mapType',$this->getSetting('maptype'));

    }

    /**
     * Destroys
     *
     * @access     public
     */
    public function __destruct ()
    {
        
        parent::__destruct();
    
    }

    public function indexAction()
    {

		unset($_SESSION['app']['user']['search']['hasSearchResults']);

		$this->getTaxonTree(array('includeOrphans' => false));

		if ($this->_mapType=='l2') {
		
			$taxa = $this->l2GetTaxaWithOccurrences();
		
			$d = current($taxa);

			if (isset($d['id'])) 
				$this->redirect('l2_examine_species.php?id='.$d['id']);
			else
				$this->redirect('l2_examine.php');
		
		} else {

			$taxa = $this->getTaxaWithOccurrences();

			$d = current($taxa);
		
			if (isset($d['id'])) 
				$this->redirect('examine_species.php?id='.$d['id']);
			else
				$this->redirect('examine.php');
			
		}

	
		/*
        $this->setPageName( _('Index'));
		
        $this->printPage();
		*/
    
    }

	public function examineAction()
	{

		if ($this->_mapType=='l2') $this->redirect('l2_examine.php');

		$this->setPageName(_('Choose a species'));
		
		$pagination = $this->getPagination($this->getTaxaWithOccurrences(),$this->controllerSettings['speciesPerPage']);

		$this->smarty->assign('prevStart', $pagination['prevStart']);
	
		$this->smarty->assign('nextStart', $pagination['nextStart']);

		if(isset($pagination['items'])) $this->smarty->assign('taxa',$pagination['items']);

		$this->printPage();	

	}

	public function l2ExamineAction()
	{

		$this->setPageName(_('Choose a species'));
		
		$pagination = $this->getPagination($this->l2GetTaxaWithOccurrences(),$this->controllerSettings['speciesPerPage']);

		$this->smarty->assign('prevStart', $pagination['prevStart']);
	
		$this->smarty->assign('nextStart', $pagination['nextStart']);

		if(isset($pagination['items'])) $this->smarty->assign('taxa',$pagination['items']);

		$this->printPage('examine');	

	}

	public function examineSpeciesAction()
	{

		if ($this->_mapType=='l2') $this->redirect('l2_examine_species.php?id='.$this->requestData['id']);

		if (!$this->rHasId()) $this->redirect('examine.php');

		$taxon = $this->getTaxonById($this->requestData['id']);

		$this->setPageName(sprintf(_('Displaying "%s"'),$taxon['taxon']));

		$d = $this->getTaxonOccurrences($taxon['id'],$this->rHasVal('o') ? $this->requestData['o'] : null);

		$geoDataTypes = $this->getGeoDataTypes();

		$this->smarty->assign('geoDataTypes',$geoDataTypes);

		$this->smarty->assign('taxon',$taxon);

		$this->smarty->assign('count',$d['count']);

		$this->smarty->assign('occurrences',$d['occurrences']);

		$this->smarty->assign('mapBorder',$this->getMapBorder($d['occurrences']));
		
		$this->smarty->assign('adjacentItems', $this->getAdjacentItems($taxon['id']));

		$this->printPage();	

	}

	public function l2ExamineSpeciesAction()
	{

		if (!$this->rHasId()) $this->redirect('l2_examine.php');
		
		if (!$this->rHasVal('ref','search')) unset($_SESSION['app']['user']['map']['search']);

		if (!$this->rHasVal('ref','diversity')) unset($_SESSION['app']['user']['map']['index']);

		$taxon = $this->getTaxonById($this->requestData['id']);

		$this->setPageName(sprintf(_('Displaying "%s"'),$taxon['taxon']));

		if (!$this->rHasVal('m'))
			$mapId = $this->l2GetFirstOccurrenceMapId($this->requestData['id']);
		else
			$mapId = $this->requestData['m'];

		$d = $this->l2GetTaxonOccurrences($taxon['id'],$mapId);

		$this->smarty->assign('mapId',$mapId);

		$this->smarty->assign('maps',$this->l2GetMaps());

		$this->smarty->assign('geoDataTypes',$this->getGeoDataTypes());

		$this->smarty->assign('taxon',$taxon);

		$this->smarty->assign('count',$d['count']);

		$this->smarty->assign('occurrences',$d['occurrences']);

		$this->smarty->assign('adjacentItems', $this->l2getAdjacentItems($taxon['id']));

		$this->smarty->assign('allLookupNavigateOverrideUrl', 'l2_examine_species.php?m='.$mapId.'&id=');

		$this->printPage();	
		
	}

	public function compareAction()
	{

		if ($this->_mapType=='l2') $this->redirect('l2_compare.php');

		$occurrencesA = array();
		$occurrencesB = array();

		if ($this->rHasVal('idA')) {

			$taxonA = $this->getTaxonById($this->requestData['idA']);

			$d = $this->getTaxonOccurrences($taxonA['id']);

			$occurrencesA = $d['occurrences'];

			$countA = $d['count'];

		}

		if ($this->rHasVal('idB')) {
		
			$taxonB = $this->getTaxonById($this->requestData['idB']);

			$d = $this->getTaxonOccurrences($taxonB['id']);

			$occurrencesB = $d['occurrences'];

			$countB = $d['count'];

		}

		$this->setPageName(_('Comparing taxa'));

		if ($this->rHasVal('idA') && $this->rHasVal('idB')) {
		
			$overlap = $this->getOverlap($this->requestData['idA'],$this->requestData['idB']);
	
			$this->setPageName(sprintf(_('Comparing taxa "%s" and "%s"'),$taxonA['taxon'],$taxonB['taxon']));

		}

		$taxa = $this->getTaxaWithOccurrences();

		$this->smarty->assign('geoDataTypes',$this->getGeoDataTypes());

		if (isset($taxa)) $this->smarty->assign('taxa',$taxa);

		if (isset($overlap)) $this->smarty->assign('overlap',$overlap);

		if (isset($taxonA)) $this->smarty->assign('taxonA',$taxonA);

		if (isset($taxonB)) $this->smarty->assign('taxonB',$taxonB);

		if (isset($occurrencesA)) $this->smarty->assign('occurrencesA',$occurrencesA);

		if (isset($occurrencesB)) $this->smarty->assign('occurrencesB',$occurrencesB);

		if (isset($countA)) $this->smarty->assign('countA',$countA);

		if (isset($countB)) $this->smarty->assign('countB',$countB);

		$this->smarty->assign('mapBorder',$this->getMapBorder(array_merge((array)$occurrencesA,(array)$occurrencesB)));

		$this->printPage();	

	}

	public function L2CompareAction()
	{

		$this->setPageName(_('Comparing taxa'));

		$maps = $this->l2GetMaps();

		if ($this->rHasVal('m')) {

			$mapId = $this->requestData['m'];

		} elseif ($this->rHasVal('idA') && $this->rHasVal('idB')) {

			if ($this->rHasVal('idA'))
				$mapId = $this->l2GetFirstOccurrenceMapId($this->requestData['idA']);
			elseif ($this->rHasVal('idB'))
				$mapId = $this->l2GetFirstOccurrenceMapId($this->requestData['idB']);

		} else {

			$d = current($maps);

			$mapId = $d['id'];

		}
			

		if ($this->rHasVal('idA'))  $taxonA = $this->getTaxonById($this->requestData['idA']);

		if ($this->rHasVal('idB')) $taxonB = $this->getTaxonById($this->requestData['idB']);

		if ($this->rHasVal('idA') && $this->rHasVal('idB')) {
		
			$overlap = $this->l2GetOverlap(
				$this->requestData['idA'],
				$this->requestData['idB'],
				$mapId,
				$this->rHasVal('selectedDataTypes') ? $this->requestData['selectedDataTypes'] : null
			);
	
			$this->setPageName(sprintf(_('Comparing taxa "%s" and "%s"'),$taxonA['taxon'],$taxonB['taxon']));

		}

		if (isset($overlap)) $this->smarty->assign('overlap',$overlap);

		if (isset($taxonA)) $this->smarty->assign('taxonA',$taxonA);

		if (isset($taxonB)) $this->smarty->assign('taxonB',$taxonB);

		$this->smarty->assign('selectedDataTypes',$this->rHasVal('selectedDataTypes') ? $this->requestData['selectedDataTypes'] : '*');

		$this->smarty->assign('mapId',$mapId);

		//$this->smarty->assign('taxa',$this->l2GetTaxaWithOccurrences());

		$this->smarty->assign('maps',$maps);

		$this->smarty->assign('geoDataTypes',$this->getGeoDataTypes());

		$this->printPage();	

	}

	public function searchAction()
	{

		if ($this->_mapType=='l2') $this->redirect('l2_search.php');

		$this->setPageName(_('Search'));

		if ($this->rHasVal('coordinates')) {

			$coordinates = $this->rectangleIntoPolygon($this->requestData['coordinates']);

			$results = $this->searchPolygon($coordinates);

			if ($results['count']['total']>0) {

				$this->getTaxonTree(array('includeOrphans' => false));
		
				$taxa = $this->getTreeList();				

				$geoDataTypes = $this->getGeoDataTypes();

			}

			$c = explode('),(',$coordinates);
			
			foreach((array)$c as $key => $val) {
				$d = explode(',',trim($val,')('));
				$nodes[] = array('latitude' => $d[0], 'longitude' => $d[1]);
			}

			$this->smarty->assign('coordinates',$this->requestData['coordinates']);

			$this->smarty->assign('mapBorder',$this->getMapBorder(array_merge((array)$results['results'],(array)$nodes)));

		}

		if (isset($results)) $this->smarty->assign('results',$results['results']);

		if (isset($results)) $this->smarty->assign('count',$results['count']);

		if (isset($taxa)) $this->smarty->assign('taxa',$taxa);

		if (isset($geoDataTypes)) $this->smarty->assign('geoDataTypes',$geoDataTypes);

		$this->smarty->assign('mapInitString','{drawingmanager:true}');

		$this->printPage();	

	}
	
	public function l2SearchAction()
	{

		$this->setPageName(_('Search'));
		
		$maps = $this->l2GetMaps();

		if ($this->rHasVal('mapId')) {

			$mapId = $this->requestData['mapId'];
		
		} else {

			$d = current($maps);
	
			$mapId = $d['id'];
			
		}
		
		$didSearch = false;

		if ($this->rHasVal('action','research') && isset($_SESSION['app']['user']['map']['search'])) {

			$taxa = $_SESSION['app']['user']['map']['search']['taxa'];
			$selectedCells = $_SESSION['app']['user']['map']['search']['selectedCells'];
			$selectedDataTypes = $_SESSION['app']['user']['map']['search']['selectedDataTypes'];
			$mapId = $_SESSION['app']['user']['map']['search']['mapId'];
			$didSearch = true;
	
		} else
		if ($this->rHasVal('selectedCells') && $this->rHasVal('mapId')) {

			$taxa = $this->l2DoSearchMap(
				$this->requestData['mapId'],
				$this->requestData['selectedCells'],
				$this->rHasVal('dataTypes') ? $this->requestData['dataTypes'] : null
			);

			foreach((array)$this->requestData['selectedCells'] as $val)
				$selectedCells[$val] = true;
				
				
			if ($this->rHasVal('dataTypes')) {
		
				foreach((array)$this->requestData['dataTypes'] as $val)
					$selectedDataTypes[$val] = true;
					
			} else {
			
				$selectedDataTypes = null;
			
			}
		
			$_SESSION['app']['user']['map']['search'] = array(
				'mapId' => $mapId, 
				'selectedCells' => $selectedCells, 
				'selectedDataTypes' => $selectedDataTypes, 
				'taxa' => $taxa
				);

			$didSearch = true;

		} else {

			unset($_SESSION['app']['user']['map']['search']);

		}


		if (isset($selectedCells)) $this->smarty->assign('selectedCells',$selectedCells);

		if (isset($selectedDataTypes)) $this->smarty->assign('selectedDataTypes',$selectedDataTypes);

		if (isset($taxa)) $this->smarty->assign('taxa',$taxa);

		$this->smarty->assign('didSearch',$didSearch);

		$this->smarty->assign('geoDataTypes',$this->getGeoDataTypes());

		$this->smarty->assign('mapId',$mapId);

		$this->smarty->assign('maps',$maps);

		$this->printPage();	

	}
	
	public function diversityAction()
	{

		if ($this->_mapType=='l2') $this->redirect('l2_diversity.php');
	
		$this->setPageName(_('Diversity index'));

		$data = $this->getDiversityIndex();

		foreach((array)$data as $key => $val) $data[$key]['nodes'] = json_decode($val['boundary_nodes']);

		$geoDataTypes = $this->getGeoDataTypes();

		$this->smarty->assign('geoDataTypes',$geoDataTypes);

		$this->smarty->assign('data',$data);

		$this->smarty->assign('mapBorder',$this->getMapBorder($data));
		
		$this->printPage();		
	
	}

	public function l2DiversityAction()
	{
	
		$this->setPageName(sprintf(_('Diversity index')));
		
		$maps = $this->l2GetMaps();

		if (!$this->rHasVal('m')) {
			$d = current($maps);
			$mapId = $d['id'];
		} else
			$mapId = $this->requestData['m'];


		if ($this->rHasVal('action','reindex') && isset($_SESSION['app']['user']['map']['index'])) {

			$d = $_SESSION['app']['user']['map']['index'];

			$taxa = isset($d['taxa']) ? $d['taxa'] : null;
			$selectedCell = isset($d['selectedCell']) ? $d['selectedCell'] : null;
			$selectedDatatypes = isset($d['selectedDatatypes']) ? $d['selectedDatatypes'] : null;
			$mapId = isset($d['mapId']) ? $d['mapId'] : null;
			$index = isset($d['index']) ? $d['index'] : null;

		} else {

			unset($_SESSION['app']['user']['map']['index']);

			$index = $this->l2GetDiversityIndex($mapId,($this->rHasVal('selectedDatatypes') ? $this->requestData['selectedDatatypes'] : null));

			$_SESSION['app']['user']['map']['index'] = array(
				'mapId' => $mapId,
				'index' => $index
			);
			
			if ($this->rHasVal('selectedDatatypes')) {
	
				foreach((array)$this->requestData['selectedDatatypes'] as $val)
					$selectedDatatypes[$val] = true;

				$_SESSION['app']['user']['map']['index']['selectedDatatypes'] = $selectedDatatypes;

			}
	
			if ($this->rHasVal('selectedCell')) {
			
				$taxa = $this->l2DoSearchMap($this->requestData['m'],(array)$this->requestData['selectedCell'],'*');
			
				$selectedCell = $this->requestData['selectedCell'];
	
				$_SESSION['app']['user']['map']['index']['taxa'] = $taxa;
				$_SESSION['app']['user']['map']['index']['selectedCell'] = $this->requestData['selectedCell'];

			}
			
		}
				
		if (isset($taxa)) $this->smarty->assign('taxa',$taxa);

		if (isset($selectedCell)) $this->smarty->assign('selectedCell',$selectedCell);

		if (isset($selectedDatatypes)) $this->smarty->assign('selectedDatatypes',$selectedDatatypes);

		$this->smarty->assign('index',$index);

		$this->smarty->assign('mapId',$mapId);

		$this->smarty->assign('maps',$maps);

		$this->smarty->assign('geoDataTypes',$this->getGeoDataTypes());

		$this->printPage();	

	
	}

	public function ajaxInterfaceAction()
	{

		if (!$this->rHasVal('action')) $this->smarty->assign('returnText','error');
		
		if ($this->rHasVal('action','get_lookup_list') && !empty($this->requestData['search'])) {

            $this->getLookupList($this->requestData);
			
		}

		$this->allowEditPageOverlay = false;
		
        $this->printPage();
	
	}
	
	private function getDiversityIndex()
	{
	
		return $this->models->DiversityIndex->_get(array('id' => array('project_id' => $this->getCurrentProjectId())));

	}

	private function checkRemoteServerAccessibility()
	{

		$f = @fopen($this->controllerSettings['urlToCheckConnectivity'], 'r');

		if (!$f) return false;

		fclose($f);

		return true;
		
	}

	private function getTaxaOccurrenceCount($taxaToFilter=null)
	{

		$ot = $this->models->OccurrenceTaxon->_get(
			array(
				'id' => array(
					'project_id' => $this->getCurrentProjectId(),
				),
				'columns' => 'taxon_id,count(*) as total',
				'group' => 'taxon_id',
				'fieldAsIndex' => 'taxon_id'
			)
		);
		
		
		if (isset($taxaToFilter)) {
		
			foreach((array)$ot as $key => $val) {
			
				if ($val['total']!=0 && isset($taxaToFilter[$key])) {
				
					$d[$key] = $taxaToFilter[$key];
					$d[$key]['total'] = $val['total'];

				}
			
			}
			
		} else {
		
			$d = $ot;
		
		}

		return isset($d) ? $d : null;
	
	}

	private function inverseColour($hexColour)
	{

		$r = dechex(255-hexdec(substr($hexColour,0,2)));
		$g = dechex(255-hexdec(substr($hexColour,2,2)));
		$b = dechex(255-hexdec(substr($hexColour,4,2)));

		return (strlen($r)==1 ? $r.$r : $r).(strlen($g)==1 ? $g.$g : $g).(strlen($b)==1 ? $b.$b : $b);
	
	}
	
	private function rectangleIntoPolygon($coo)
	{

		$d = explode(',',str_replace(array('(',')'),'',$coo));
		
		return '(('.$d[0].','.$d[1].'),('.$d[0].','.$d[3].'),('.$d[2].','.$d[3].'),('.$d[2].','.$d[1].'))';

	}

	private function getTaxonOccurrences($id,$occ=null)
	{
	
		if (!isset($id)) return;

		$d = array(
				'project_id' => $this->getCurrentProjectId(),
				'taxon_id' => $id,
			);
			
		if (isset($occ)) $d['id'] = $occ;

		$ot = $this->models->OccurrenceTaxon->_get(
			array(
				'id' => $d,
				'columns' => 'id,type,type_id,latitude,longitude,boundary_nodes'
			)
		);

		$count['total']=0;

		foreach((array)$ot as $key => $val) {

			if (!isset($count['data'][$val['type_id']])) {
				$count['data'][$val['type_id']]=1;
			} else {
				$count['data'][$val['type_id']]++;
			}

			$count['total']++;

			$d = $this->getGeoDataTypes($val['type_id']);	
			$ot[$key]['type_title'] = $d['title'];
			$ot[$key]['colour'] = $d['colour'];
			$ot[$key]['colour_inverse'] = $this->inverseColour($d['colour']);
			if ($val['type']=='polygon' && isset($val['boundary_nodes'])) $ot[$key]['nodes'] = json_decode($val['boundary_nodes']);

		}
	
		return array(
			'occurrences' => $ot,
			'count' => $count
		);
	
	}

	private function getGeodataTypes($id=null)
	{

		$d['project_id'] = $this->getCurrentProjectId();

		if (isset($id)) $d['id'] = $id;
	
		$gt = $this->models->GeodataType->_get(
			array(
				'id' => $d,
				'fieldAsIndex' => 'id',
				'columns' => 'id,colour'
			)
		);
		
		$gtl = $this->models->GeodataTypeTitle->_get(
			array(
				'id' => array(
					'project_id' => $this->getCurrentProjectId(),
					'language_id' => $this->getCurrentLanguageId()
				),
				'columns' => 'title,type_id',
				'fieldAsIndex' => 'type_id'
			)
		);		

		foreach ((array)$gt as $key => $val) {

			$g = $gtl[$val['id']];
			$gt[$key]['title'] = isset($g['title']) ? $g['title'] : '-';
			$gt[$key]['colour_inverse'] = $this->inverseColour($val['colour']);
	
		}
			
		if (isset($id))
			return $gt[$id];
		else
			return $gt;
	
	}

	private function getMapBorder($occurrences)
	{

		$sLat = $sLng = 999;
		$lLat = $lLng = -999;

		foreach((array)$occurrences as $key => $val) {

			if (isset($val['latitude']) && isset($val['longitude'])) {

				if (!empty($val['latitude']) && $val['latitude'] < $sLat) $sLat = $val['latitude'];
				if (!empty($val['latitude']) && $val['latitude'] > $lLat) $lLat = $val['latitude'];
				if (!empty($val['longitude']) && $val['longitude'] < $sLng) $sLng = $val['longitude'];
				if (!empty($val['longitude']) && $val['longitude'] > $lLng) $lLng = $val['longitude'];

			} else
			if (isset($val['nodes'])) {

				foreach((array)$val['nodes'] as $nKey => $nVal) {
		
					if ($nVal[0] && $nVal[1]) {
		
						if (!empty($nVal[0]) && $nVal[0] < $sLat) $sLat = $nVal[0];
						if (!empty($nVal[0]) && $nVal[0] > $lLat) $lLat = $nVal[0];
						if (!empty($nVal[1]) && $nVal[1] < $sLng) $sLng = $nVal[1];
						if (!empty($nVal[1]) && $nVal[1] > $lLng) $lLng = $nVal[1];
		
					}

				}
		
			} else
			if (isset($val['boundary_nodes'])) {

				foreach((array)json_decode($val['boundary_nodes']) as $nKey => $nVal) {
				
					if (is_array($nVal)) {

						foreach($nVal as $oVal) {
				
							if (isset($oVal[0]) && isset($oVal[1]) && is_numeric($oVal[0]) && is_numeric($oVal[1])) {
				
								if (!empty($oVal[0]) && $oVal[0] < $sLat) $sLat = $oVal[0];
								if (!empty($oVal[0]) && $oVal[0] > $lLat) $lLat = $oVal[0];
								if (!empty($oVal[1]) && $oVal[1] < $sLng) $sLng = $oVal[1];
								if (!empty($oVal[1]) && $oVal[1] > $lLng) $lLng = $oVal[1];
				
							}
	
						}


					} else {
		
						if (isset($oVal[0]) && isset($oVal[1]) && is_numeric($oVal[0]) && is_numeric($oVal[1])) {
			
							if (!empty($nVal[0]) && $nVal[0] < $sLat) $sLat = $nVal[0];
							if (!empty($nVal[0]) && $nVal[0] > $lLat) $lLat = $nVal[0];
							if (!empty($nVal[1]) && $nVal[1] < $sLng) $sLng = $nVal[1];
							if (!empty($nVal[1]) && $nVal[1] > $lLng) $lLng = $nVal[1];
			
						}

					}

				}
		
			}

		}

		if ($sLat==999) $sLat = -1;
		if ($sLng==999) $sLng = -5;

		if ($lLat==-999) $lLat = 1;
		if ($lLng==-999) $lLng = 5;

		return
			array(
				'sw' => array('lat' => $sLat,'lng' => $sLng),
				'ne' => array('lat' =>  $lLat,'lng' =>  $lLng)
			);

	}

	private function getOverlap($id1,$id2)
	{

		if (!isset($id1) || !isset($id2)) return;

		$ot = $this->models->OccurrenceTaxon->_get(
			array(
				'id' => 'select
							a.type_id,
							count(*) as total
						from
						%table% a, 
						%table% b
							where a.project_id = '.$this->getCurrentProjectId().' 
							and b.project_id = '.$this->getCurrentProjectId().' 
							and a.taxon_id = '.$id1.'
							and b.taxon_id = '.$id2.'
							and a.type_id = b.type_id
							and (
								Intersects(
									if(a.type=\'polygon\',a.boundary,a.coordinate),
									if(b.type=\'polygon\',b.boundary,b.coordinate)
								)=1
								or
								Contains(
									if(a.type=\'polygon\',a.boundary,a.coordinate),
									if(b.type=\'polygon\',b.boundary,b.coordinate)
								)=1
								or
								Overlaps(
									if(a.type=\'polygon\',a.boundary,a.coordinate),
									if(b.type=\'polygon\',b.boundary,b.coordinate)
								)=1
							)
							group by a.type_id'
			)
		);

		return $ot;

	}

	private function searchPolygon($coordinates)
	{

		if (!isset($coordinates)) return;

		$d = explode('),(',trim($coordinates,'()'));

		if (count((array)$d)>2) {

			foreach((array)$d as $key => $val) {

				$g = explode(',',$val);
				$nodes[] = array($g[0],$g[1]);

			}

		} else return;

		// remove the last node if it is identical to the first, just in case
		if ($nodes[0]==$nodes[count((array)$nodes)-1]) array_pop($nodes);

		// create a string for mysql (which does require the first and last to be the same)
		foreach((array)$nodes as $key => $val) $geoStr[] = $val[0].' '.$val[1];
		$geoStr = implode(',',$geoStr).','.$geoStr[0];

		$ot = $this->models->OccurrenceTaxon->_get(	
			array(
				'id' => 'select
							id,taxon_id,type_id,type,latitude,longitude,boundary_nodes
						from
						%table%
						where project_id = '.$this->getCurrentProjectId().' 
						and (
							Intersects(
								GeomFromText(\'POLYGON(('.$geoStr.'))\','.$this->controllerSettings['SRID'].') ,
								if(type=\'polygon\',boundary,coordinate)
							)=1
						)
						order by taxon_id,type_id'
			)
		);

		$count['total'] = 0;

		foreach((array)$ot as $key => $val) {

			if (!isset($count['data'][$val['type_id']]))
				$count['data'][$val['type_id']] = 1;
			else
				$count['data'][$val['type_id']]++;

			if (!isset($count['taxa'][$val['taxon_id']]))
				$count['taxa'][$val['taxon_id']] = 1;
			else
				$count['taxa'][$val['taxon_id']]++;

			$count['total']++;

			$ot[$key]['nodes'] = json_decode($val['boundary_nodes']);

		}
		
		return array(
			'results' => $ot, 
			'count' => $count
		);

	}

	private function getTaxaWithOccurrences()
	{

		$this->getTaxonTree(array('includeOrphans' => false));

		$taxa = $this->getTaxaOccurrenceCount($this->getTreeList());
		
		$this->customSortArray($taxa,array('key' => 'taxon','maintainKeys' => true));
		
		return $taxa;
	
	}

	private function getLookupList($p)
	{

		$search = isset($p['search']) ? $p['search'] : null;
		$matchStartOnly = isset($p['match_start']) ? $p['match_start']=='1' : false;
		$getAll = isset($p['get_all']) ? $p['get_all']=='1' : false;
		$l2MustHaveGeo = false;

		if (isset($p['vars'])) {

			foreach((array)$p['vars'] as $val) {

				if ($val[0]=='l2_must_have_geo' && $val[1]=='1') $l2MustHaveGeo = true;
			
			}
		
		}

		$search = str_replace(array('/','\\'),'',$search);

		if (empty($search) && !$getAll) return;

		if ($matchStartOnly)
			$regexp = '/^'.preg_quote($search).'/i';
		else
			$regexp = '/'.preg_quote($search).'/i';

		$l = array();

		$this->getTaxonTree(array('includeOrphans' => false));

		if ($l2MustHaveGeo)
			$taxa = $this->getTaxaOccurrenceCount($this->l2GetTaxaWithOccurrences());
		else				
			$taxa = $this->getTaxaOccurrenceCount($this->getTreeList());

		foreach((array)$taxa as $key => $val) {

			if ($getAll || preg_match($regexp,$val['taxon']) == 1)
				$l[] = array(
					'id' => $val['id'],
					'label' => $val['taxon']
				);

		}

		$this->customSortArray($l,array('key' => 'taxon','maintainKeys' => true));
		
		$this->smarty->assign(
			'returnText',
			$this->makeLookupList(
				$l,
				'species',
				'../mapkey/examine_species.php?id=%s',
				true
			)
		);

	}

	private function getAdjacentItems($id)
	{

		$taxa = $this->getTaxaWithOccurrences();
		
		reset($taxa);
		
		$prev = $next = null;

		while (list($key, $val) = each($taxa)) {
		
			if ($key==$id) {

				$next = current($taxa); // current = next because the pointer has already shifted forward

				return array(
					'prev' => isset($prev) ? array('id' => $prev['id'],'label' => $prev['taxon']) : null,
					'next' => isset($next) ? array('id' => $next['id'],'label' => $next['taxon']) : null
				);

			}

			$prev = $val;

		}
		
		return null;

	}


	// Linnaeus 2 map functions
	private function getDistributionMapType()
	{
	
		return $this->l2CountMaps()!=0 ? 'l2' : 'lng';
	
	}

	private function removeGoogleMapsJS()
	{

		if ($this->_mapType=='l2') {
			
			foreach((array)$this->jsToLoad['all'] as $val) {
			
				if (!preg_match('/(http:\/\/maps.google.com)/i',$val)) $d[] = $val;
			
			}
			
			$this->jsToLoad['all'] = $d;
			
		}
		
	}

	private function l2CountMaps()
	{

		$d = $this->models->L2Map->_get(
			array('id' =>
				array(
					'project_id' => $this->getCurrentProjectId(),
					'name !=' => '\'\''
				),
				'columns' => 'count(*) as total'
			)
		);

		return $d[0]['total'];

	}
	
	private function l2GetTaxaWithOccurrences()
	{

		$this->getTaxonTree(array('includeOrphans' => false));

		$taxa = $this->l2GetTaxaOccurrenceCount($this->getTreeList());
		
		$this->customSortArray($taxa,array('key' => 'taxon','maintainKeys' => true));

		return $taxa;
	
	}

	private function l2GetTaxaOccurrenceCount($taxaToFilter=null)
	{

		$ot = $this->models->L2OccurrenceTaxon->_get(
			array(
				'id' => array(
					'project_id' => $this->getCurrentProjectId(),
				),
				'columns' => 'taxon_id,count(*) as total',
				'group' => 'taxon_id',
				'fieldAsIndex' => 'taxon_id'
			)
		);
		
		
		if (isset($taxaToFilter)) {
		
			foreach((array)$ot as $key => $val) {
			
				if ($val['total']!=0 && isset($taxaToFilter[$key])) {
				
					$d[$key] = $taxaToFilter[$key];
					$d[$key]['total'] = $val['total'];

				}
			
			}
			
		} else {
		
			$d = $ot;
		
		}

		return isset($d) ? $d : null;
	
	}

	private function l2GetFirstOccurrenceMapId($id)
	{

		if (!isset($id)) return;

		$d = $this->models->L2OccurrenceTaxon->_get(
			array(
				'id' => array(
					'project_id' => $this->getCurrentProjectId(),
					'taxon_id' => $id
				),
				'columns' => 'map_id',
				'limit' => 1
			)
		);

		return $d[0]['map_id'];

	}

	private function l2getAdjacentItems($id)
	{

		$taxa = $this->l2GetTaxaWithOccurrences();
		
		reset($taxa);
		
		$prev = $next = null;

		while (list($key, $val) = each($taxa)) {
		
			if ($key==$id) {

				$next = current($taxa); // current = next because the pointer has already shifted forward

				return array(
					'prev' => isset($prev) ? array('id' => $prev['id'],'label' => $prev['taxon']) : null,
					'next' => isset($next) ? array('id' => $next['id'],'label' => $next['taxon']) : null
				);

			}

			$prev = $val;

		}
		
		return null;

	}

	private function l2GetMaps($id=null)
	{

		if (!isset($_SESSION['app']['user']['map']['L2Maps'])) {

			$m = $this->models->L2Map->_get(
				array(
					'id' => array('project_id' => $this->getCurrentProjectId()),
					'fieldAsIndex' => 'id'
				)
			);

			foreach((array)$m as $key => $val) {
			
				$m[$key]['mapExists'] = false;

				if (!empty($val['image'])) {
				
					if (file_exists($_SESSION['app']['project']['urls']['projectL2Maps'].$val['image'])) {

						$m[$key]['mapExists'] = true;
					
						$m[$key]['imageFullName'] = $_SESSION['app']['project']['urls']['projectL2Maps'].$val['image'];
					
					} else {

						$m[$key]['mapExists'] = file_exists($_SESSION['app']['project']['urls']['systemL2Maps'].$val['image']);
	
						$m[$key]['imageFullName'] = $_SESSION['app']['project']['urls']['systemL2Maps'].$val['image'];
					
					}
				
				} else {
				
					$mapName = strtolower($val['name']).'.gif';
				
					$m[$key]['mapExists'] = file_exists($_SESSION['app']['project']['urls']['systemL2Maps'].$mapName);

					$m[$key]['imageFullName'] = $_SESSION['app']['project']['urls']['systemL2Maps'].$mapName;

				}

				if ($m[$key]['mapExists']) $m[$key]['size'] = getimagesize($m[$key]['imageFullName']);
				
				$d = json_decode($val['coordinates']);

				$m[$key]['coordinates'] = array(
					'topLeft' => array(
						'lat' => (string)$d->topLeft->lat,
						'long' => (string)$d->topLeft->long
					),
					'bottomRight' => array(
						'lat' => (string)$d->bottomRight->lat,
						'long' => (string)$d->bottomRight->long
					),
					'original' => $val['coordinates']
				);

			}
			
			$_SESSION['app']['user']['map']['L2Maps'] = $m;
	
		}

		return isset($id) ? $_SESSION['app']['user']['map']['L2Maps'][$id] : $_SESSION['app']['user']['map']['L2Maps'];
	
	}

	private function l2GetTaxonOccurrences($id,$mapId,$typeId=null)
	{
	
		if (!isset($id) || !isset($mapId)) return;

		$d = array(
			'project_id' => $this->getCurrentProjectId(),
			'taxon_id' => $id,
			'map_id' => $mapId
		);
		
		if (isset($typeId)) $d['type_id'] = $typeId;

		$ot = $this->models->L2OccurrenceTaxon->_get(
			array(
				'id' => $d,
				'columns' => 'id,taxon_id,map_id,type_id,square_number,legend,coordinates',
				'fieldAsIndex' => 'square_number'
				
			)
		);

		$dataTypes = array();
		$dt = $this->getGeodataTypes();

		foreach((array)$ot as $key => $val) {
		
			$dataTypes[$val['type_id']] = $val['type_id'];
		
			$d = $dt[$val['type_id']];
			$ot[$key]['type_title'] = $d['title'];
			$ot[$key]['colour'] = $d['colour'];

		}

		// why the count??
		return array('occurrences' => $ot, 'count' => count((array)$ot));

	}

	private function l2GetOverlap($id1,$id2,$mapId,$dataTypes)
	{

		if (!isset($id1) || !isset($id2) || !isset($mapId)) return;

		$o1 = $this->l2GetTaxonOccurrences($id1,$mapId);
		$o2 = $this->l2GetTaxonOccurrences($id2,$mapId);
		
		$d = array();
		
		foreach((array)$o1['occurrences'] as $key => $val) {
		
			if (is_null($dataTypes) || (!is_null($dataTypes) && isset($dataTypes[$val['type_id']])))
				$d[$val['square_number']] = 'A';
		
		}

		foreach((array)$o2['occurrences'] as $key => $val) {
		
			if (is_null($dataTypes) || (!is_null($dataTypes) && isset($dataTypes[$val['type_id']])))
				$d[$val['square_number']] = isset($d[$val['square_number']]) && $d[$val['square_number']] == 'A' ? 'AB' : 'B';
		
		}

		return $d;

	}

	private function l2DoSearchMap($mapId,$selectedCells,$dataTypes)
	{
	
		if (!isset($mapId) || !isset($selectedCells)|| !isset($dataTypes)) return;
		
		$d =  array(
				'project_id' => $this->getCurrentProjectId(),
				'map_id' => $mapId,
				'square_number in' => '('.implode(',',$selectedCells).')'
			);
			
		if ($dataTypes!='*') $d['type_id in'] = '('.implode(',',$dataTypes).')';
		
		$ot = $this->models->L2OccurrenceTaxon->_get(
			array(
				'id' => $d,
				'columns' => 'distinct taxon_id'
				
			)
		);
		
		foreach((array)$ot as $val) $p[] = $this->getTaxonById($val['taxon_id']);

		$this->customSortArray($p,array(
			'key' => 'taxon', 
			'dir' => 'asc', 
			'case' => 'i'
			)
		);

		return $p;

	}

	private function l2GetDiversityIndex($mapId,$typeId=null)
	{

		$sessIdx = 	$mapId.':'.(isset($typeId) ? implode('-',$typeId) : '');	

		if (!isset($_SESSION['app']['user']['map']['divIndex'][$mapId][$sessIdx])) {

			$d = array(
					'project_id' => $this->getCurrentProjectId(),
					'map_id' => $mapId
				);
				
			if (isset($typeId)) $d['type_id in'] = '('.implode(',',$typeId).')';
		
			$ot = $this->models->L2OccurrenceTaxon->_get(
				array(
					'id' => $d,
					'columns' => 'count(*) as total, square_number',
					'group' => 'square_number',
					'order' => 'total desc',
					'fieldAsIndex' => 'square_number',				
				)
			);
	
			if ($ot) {
				$d = current($ot);
				$max = $d['total'];
				end($ot);
				$d = current($ot);
				$min = $d['total'];
			} else {
				$max = $min = 0;
			}
	
			foreach((array)$ot as $key => $val) {
			
				$ot[$key]['pct'] = round(($val['total'] / $max) * 100);
				$ot[$key]['class'] = ceil($ot[$key]['pct'] / (100 / $this->controllerSettings['l2DiversityIndexNumOfClasses']));
	
			}

			$_SESSION['app']['user']['map']['divIndex'][$mapId][$sessIdx] = array(
				'index' => $ot,
				'min' => $min,
				'max' => $max
			);

		}
		
		return $_SESSION['app']['user']['map']['divIndex'][$mapId][$sessIdx];

	}
	
}
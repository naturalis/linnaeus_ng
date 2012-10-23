<?php

include_once ('Controller.php');

class MapKeyController extends Controller
{
    
    public $usedModels = array(
		'occurrence_taxon',
		'geodata_type',
		'geodata_type_title',
		'diversity_index',
		'l2_occurrence_taxon',
		'l2_occurrence_taxon_combi',
		'l2_diversity_index',
		'l2_map'
	);
    
    public $usedHelpers = array('csv_parser_helper');

    public $controllerPublicName = 'Distribution';

	public $cssToLoad = array(
		'basics.css',
		'map.css',
		'map_l2.css',
		'dialog/jquery.modaldialog.css',
		'lookup.css'
	);

	public $jsToLoad = array(
		'all' => 
			array(
				'main.js',
				'mapkey.js',
				'mapkey_l2.js',
				'http://maps.google.com/maps/api/js?sensor=false&libraries=drawing',
				'lookup.js',
				'dialog/jquery.modaldialog.js',
				'modernizr.custom.48242.js'
			)
		);

	private $_mapType = 'lng';


    /**
     * Constructor, calls parent's constructor
     *
     * @access     public
     */
    public function __construct($p=null)
    {

        parent::__construct($p);

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

 		if (!$this->rHasVal('id')) {

			if (isset($_SESSION['app']['user']['species']['lastTaxon'])) {

				$id = $_SESSION['app']['user']['species']['lastTaxon'];
				
			} else {

				$this->buildTaxonTree();
	
				if ($this->_mapType=='l2') {
				
					$taxa = $this->l2GetTaxaWithOccurrences();
				
					$d = current($taxa);
					
					$id = $d['id'];
			
				} else {
		
					$taxa = $this->getTaxaWithOccurrences();
		
					$d = current($taxa);
					
					$id = $d['id'];
				}
				
			}
			
		} else {
			
			$id = $this->requestData['id'];
				
		}

		if ($this->_mapType=='l2') {
		
			if (isset($id)) 
				$this->redirect('l2_examine_species.php?id='.$id);
			else
				$this->redirect('l2_examine.php');
		
		} else {

			if (isset($id)) 
				$this->redirect('examine_species.php?id='.$id);
			else
				$this->redirect('examine.php');
			
		}
    
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

		if (!$this->rHasVal('m')) {

			$mapId = $this->l2GetFirstOccurrenceMapId($this->requestData['id']);

		} else {

			$mapId = $this->requestData['m'];

		}
		
		$maps = $this->l2GetMaps();
		
		if (empty($mapId)) $mapId = key($maps);

		$d = $this->l2GetTaxonOccurrences($taxon['id'],$mapId);

		$this->smarty->assign('mapId',$mapId);

		$this->smarty->assign('maps',$maps);

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

		} else		
		if (isset($_SESSION['app']['user']['species']['lastTaxon'])) {

			$taxonA = $this->getTaxonById($_SESSION['app']['user']['species']['lastTaxon']);

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
			

		if ($this->rHasVal('idA'))  {

			$taxonA = $this->getTaxonById($this->requestData['idA']);

		} else
		if (isset($_SESSION['app']['user']['species']['lastTaxon'])) {
	
			$taxonA = $this->getTaxonById($_SESSION['app']['user']['species']['lastTaxon']);

		}

		if ($this->rHasVal('idB')) $taxonB = $this->getTaxonById($this->requestData['idB']);

		if (isset($taxonA) && isset($taxonB)) {
		
			$overlap = $this->l2GetOverlap(
				$taxonA['id'],
				$taxonB['id'],
				$mapId,
				$this->rHasVal('selectedDataTypes') ? $this->requestData['selectedDataTypes'] : null
			);
	
			$this->setPageName(sprintf(_('Comparing taxa "%s" and "%s"'),$taxonA['taxon'],$taxonB['taxon']));

		
		// Ruud 14-09-12: set values for Taxon A and B if not yet entered
		} else {
			$taxa = $this->l2GetTaxaWithOccurrences();
			// Default state
			if (!isset($taxonA) && !isset($taxonB)) {
				$taxonA = reset($taxa);
				$taxonB = next($taxa);
			// Taxon A has been set already
			} else {
				$taxonB = reset($taxa);
				// Taxon B has already been selected in Species
				if ($taxonA['taxon'] == $taxonB['taxon']) {
					$taxonB = next($taxa);
				}
			}
		}
		
		

		if (isset($overlap)) $this->smarty->assign('overlap',$overlap);
		
		if (isset($taxonA)) $this->smarty->assign('taxonA',$taxonA);

		if (isset($taxonB)) $this->smarty->assign('taxonB',$taxonB);

		$this->smarty->assign('selectedDataTypes',$this->rHasVal('selectedDataTypes') ? $this->requestData['selectedDataTypes'] : '*');

		$this->smarty->assign('mapId',$mapId);

		$this->smarty->assign('maps',$maps);

		$this->smarty->assign('geoDataTypes',$this->getGeoDataTypes());

		$this->printPage();	

	}

	public function searchAction()
	{

		if ($this->_mapType=='l2') $this->redirect('l2_search.php'.($this->rHasVal('mapId') ? '?mapId='.$this->requestData['mapId'] : '' ));

		$this->setPageName(_('Search'));

		if ($this->rHasVal('coordinates')) {

			$coordinates = $this->rectangleIntoPolygon($this->requestData['coordinates']);

			$results = $this->searchPolygon($coordinates);

			if ($results['count']['total']>0) {

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

			$d = current((array)$maps);
	
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

		if (isset($taxa)) {

			// hell knows why, but $.parseJSON(data); started complaining about the "'s in the <span> all of a sudden
			array_walk($taxa, create_function('&$v,$k', '$v[\'label\'] = addslashes($v[\'label\']);'));

			$this->smarty->assign('taxa',
				$this->makeLookupList(
					$taxa,
					'species',
					'../species/taxon.php?id=%s'
				)
			);
			
			$this->smarty->assign('numOfTaxa',count((array)$taxa));
			
		}

		if (isset($selectedCells)) $this->smarty->assign('selectedCells',$selectedCells);

		if (isset($selectedDataTypes)) $this->smarty->assign('selectedDataTypes',$selectedDataTypes);

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
		} else {
			$mapId = $this->requestData['m'];
		}


		$index = $this->l2GetDiversityIndex($mapId,null);

		/*
		$index = $this->l2GetDiversityIndex($mapId,($this->rHasVal('selectedDatatypes') ? $this->requestData['selectedDatatypes'] : null));

		if ($this->rHasVal('selectedDatatypes')) {

			foreach((array)$this->requestData['selectedDatatypes'] as $val)
				$selectedDatatypes[$val] = true;

		}

		if ($this->rHasVal('selectedCell')) {
		
			$taxa = $this->l2DoSearchMap(
				$this->requestData['m'],
				(array)$this->requestData['selectedCell'],
				($this->rHasVal('selectedDatatypes') ? $this->requestData['selectedDatatypes'] : '*')
			);
		
			$selectedCell = $this->requestData['selectedCell'];

		}

		/*
		if (isset($taxa)) {

			// hell knows why, but $.parseJSON(data); started complaining about the "'s in the <span> all of a sudden
			array_walk($taxa, create_function('&$v,$k', '$v[\'label\'] = addslashes($v[\'label\']);'));

			$this->smarty->assign('taxa',
				$this->makeLookupList(
					$taxa,
					'species',
					'../species/taxon.php?id=%s'
				)
			);
			
		}
		*/

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
			
		} else
		if ($this->rHasVal('action','get_l2_diversity_results') && !empty($this->requestData['search'])) {

            $this->getLookupList($this->requestData);
			
		} else
		if ($this->rHasVal('action','get_cell_diversity')) {

            $this->l2GetCellDiversity($this->requestData);
			
		} else
		if ($this->rHasVal('action','get_diversity')) {

            $this->l2GetDiversity($this->requestData);
			
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

	public function getGeodataTypes($id=null)
	{

		$d['project_id'] = $this->getCurrentProjectId();

		if (isset($id)) $d['id'] = $id;
	
		$gt = $this->models->GeodataType->_get(
			array(
				'id' => $d,
				'fieldAsIndex' => 'id',
				'columns' => 'id,colour',
				'order' => 'show_order'
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

			$g = isset($gtl[$val['id']]) ? $gtl[$val['id']] : null;
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

		$taxa = $this->getTaxaOccurrenceCount($this->buildTaxonTree());
		
		$this->customSortArray($taxa,array('key' => 'taxon','maintainKeys' => true));
		
		return $taxa;
	
	}

	public function getLookupList($p)
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
		
		$index = md5($search.':'.(int)$matchStartOnly.':'.(int)$getAll.':'.(int)$l2MustHaveGeo);

		$l = $this->getCache('map-contents-'.$index);
	
		if (!$l) {

			$search = str_replace(array('/','\\'),'',$search);
	
			if (empty($search) && !$getAll) return;
	
			if ($matchStartOnly)
				$regexp = '/^'.preg_quote($search).'/i';
			else
				$regexp = '/'.preg_quote($search).'/i';
	
			$l = array();
	
			if ($l2MustHaveGeo)
				$taxa = $this->getTaxaOccurrenceCount($this->l2GetTaxaWithOccurrences());
			else				
				$taxa = $this->getTaxaOccurrenceCount($this->buildTaxonTree());
	
			foreach((array)$taxa as $key => $val) {
	
				if ($getAll || preg_match($regexp,$val['taxon']) == 1)
					$l[] = array(
						'id' => $val['id'],
						'label' => $this->formatSpeciesEtcNames($val['taxon'],$val['rank_id'])
					);
	
			}
	
			$this->customSortArray($l,array('key' => 'taxon','maintainKeys' => true));
			
			$this->saveCache('map-contents-'.$index, $l);

		}

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
	
		return $this->getSetting('maptype');
	
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

	public function l2GetTaxaWithOccurrences()
	{

		$taxa = $this->getCache('map-L2OccurringTaxa');
		
		if (!$taxa) {

			$taxa = $this->l2GetTaxaOccurrenceCount($this->buildTaxonTree());
	
			$this->customSortArray($taxa,array('key' => 'taxon','maintainKeys' => true));
	
			$this->saveCache('map-L2OccurringTaxa',$taxa);
			
		}

		return $taxa;
	
	}

	public function l2GetTaxaOccurrenceCount($taxaToFilter=null)
	{

		$ot = $this->getCache('map-l2TaxaOccurrencesCount');
		
		if (!$ot) {
	
			if ($this->l2HasTaxonOccurrencesCompacted()) {
		
				$ot = $this->models->L2OccurrenceTaxonCombi->_get(
					array(
						'id' => array(
							'project_id' => $this->getCurrentProjectId(),
						),
						'columns' => 'taxon_id,square_numbers',
						'fieldAsIndex' => 'taxon_id'
					)
				);
				
				foreach((array)$ot as $key => $val) {
				
					$ot[$key]['total'] = count((array)explode(',',$val['square_numbers']));
						
				}

			} else {
		
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
		
			}
			
			$this->saveCache('map-l2TaxaOccurrencesCount', $ot);
			
		}
		
		if (!empty($taxaToFilter)) {
		
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

		if ($this->l2HasTaxonOccurrencesCompacted()) {
	
			$d = $this->models->L2OccurrenceTaxonCombi->_get(
				array(
					'id' => array(
						'project_id' => $this->getCurrentProjectId(),
						'taxon_id' => $id
					),
					'columns' => 'map_id',
					'limit' => 1
				)
			);

		} else {
	
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
			
		}

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

	public function l2GetMaps($id=null)
	{
	
		$m = $this->getCache('map-l2Maps');
		
		if (!$m) {
		
			$m = $this->models->L2Map->_get(
				array(
					'id' => array('project_id' => $this->getCurrentProjectId()),
					'fieldAsIndex' => 'id',
					'order' => 'id'
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
	
				if ($m[$key]['mapExists']) {
					
					$m[$key]['size'] = getimagesize($m[$key]['imageFullName']);
	
					if ($this->controllerSettings['l2MaxMapWidth'] > 0 &&
							$m[$key]['size'][0] > $this->controllerSettings['l2MaxMapWidth']) {
					
						$tmpHeight = $m[$key]['size'][1]*($this->controllerSettings['l2MaxMapWidth']/$m[$key]['size'][0]);
							
						$m[$key]['cellWidth'] = (floor($this->controllerSettings['l2MaxMapWidth']/$val['cols']))-1;
						$m[$key]['cellHeight'] = (floor($tmpHeight/$val['rows']))-1;
							
						// Set map dimensions based on cell size in order to avoid rogue cells spoiling layout
						$m[$key]['width'] = ($val['cols']*($m[$key]['cellWidth']+1))-1;
						$m[$key]['height'] = ($val['rows']*($m[$key]['cellHeight']+1))-1;
						$m[$key]['resized'] = 1;
							
					} else {
	
						$m[$key]['width'] = $m[$key]['size'][0];
						$m[$key]['height'] = $m[$key]['size'][1];
						$m[$key]['cellWidth'] = (floor($m[$key]['width']/$val['cols']))-1;
						$m[$key]['cellHeight'] = (floor($m[$key]['height']/$val['rows']))-1;
						$m[$key]['resized'] = 0;
					}
				}
	
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
			
			$this->saveCache('map-l2Maps', $m);
		}
		
		return isset($id) ? $m[$id] : $m;
	
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


		if ($this->l2HasTaxonOccurrencesCompacted()) 
			$ot = $this->_l2GetTaxonOccurrencesCompacted($d);
		else
			$ot = $this->_l2GetTaxonOccurrencesPerSquare($d);

		/*
			v1			v2
			68.553ms	2.8732ms
			45.755ms	2.8363ms
			92.8979ms	2.7211ms
			32.9481ms	3.212ms
			1.9209ms	1.2671ms
			66.4689ms	3.5623ms
		
			factor 17!
		*/

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

	private function _l2GetTaxonOccurrencesPerSquare($d)
	{

		//echo '<!--using non-compacted data (bad news)-->';
		
		// all separate squares
		$ot = $this->models->L2OccurrenceTaxon->_get(
			array(
				'id' => $d,
				'columns' => 'id,taxon_id,map_id,type_id,square_number,legend,coordinates',
				'fieldAsIndex' => 'square_number'
				
			)
		);

		return $ot;

	}

	private function l2HasTaxonOccurrencesCompacted()
	{
	
		if (!isset($_SESSION['app']['user']['map']['l2CompactedData'])) {
	
			$combi = $this->models->L2OccurrenceTaxonCombi->_get(
				array(
					'id' => array('project_id' => $this->getCurrentProjectId()),
					'columns' => '1 as data',
					'limit' => 1
				)
			);
			
			$_SESSION['app']['user']['map']['l2CompactedData'] = (isset($combi[0]['data']) && $combi[0]['data']==1);

		}
		
		return $_SESSION['app']['user']['map']['l2CompactedData'];
		
	}
	
	private function _l2GetTaxonOccurrencesCompacted($d)
	{
	
		//echo '<!--using compacted data-->';
	
		// squares compacted to serialized text field per map/type/taxon-combi
		$combi = $this->models->L2OccurrenceTaxonCombi->_get(
			array(
				'id' => $d,
				'columns' => 'id,taxon_id,map_id,type_id,square_numbers'
			)
		);
		
		foreach((array)$combi as $key => $val) {
		
			$x = explode(',',$val['square_numbers']);
			
			foreach((array)$x as $val2) {
			
				$ot[$val2]['taxon_id'] = $val['taxon_id'];
				$ot[$val2]['map_id'] = $val['map_id'];
				$ot[$val2]['type_id'] = $val['type_id'];
				$ot[$val2]['square_number'] = $val2;
			
			}
		
		}
		
		return isset($ot) ? $ot : null;

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

		/*
		if ($this->l2HasTaxonOccurrencesCompacted()) {
		
			// this statement turns out to be slower than the one beneath, more so as the number of selected cells increases

			$ot = $this->models->L2OccurrenceTaxonCombi->_get(
				array(
					'where' =>
						'project_id = '.$this->getCurrentProjectId(). ' and
						 map_id = '.$mapId.' and
						'.($dataTypes!='*' ? 'type_id in ('.implode(',',$dataTypes).') and' : '').'
						concat(\',\',square_numbers,\',\') regexp \'(,'.implode(',|,',$selectedCells).',)\'',
					'columns' => 'distinct taxon_id'
					
				)
			);

		} else {
		*/

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
			
		/*}*/
		
		foreach((array)$ot as $val) {
			$d = $this->getTaxonById($val['taxon_id']);
			$p[] = array(
				'label' => $this->formatSpeciesEtcNames($d['taxon'],$d['rank_id']),
				'id' => $d['id']
			);
		}

		$this->customSortArray($p,array(
			'key' => 'label', 
			'dir' => 'asc', 
			'case' => 'i'
			)
		);

		return $p;

	}
	
	public function l2GetDiversityIndex($mapId,$typeId=null)
	{

		$sessIdx = 	$mapId.'-'.(isset($typeId) ? implode('-',$typeId) : '');
		
		$storedData = $this->getCache('map-divIndex-'. $mapId. '-' . $sessIdx);
		
		if ($storedData) return $storedData;

		$d = array(
			'project_id' => $this->getCurrentProjectId(),
			'map_id' => $mapId
		);

		if (isset($typeId)) $d['type_id in'] = '('.implode(',',$typeId).')';

		if ($this->l2HasTaxonOccurrencesCompacted()) {

			//echo '<!--using compacted index-->';

			$ot = $this->models->L2DiversityIndex->_get(
					array(
							'id' => $d,
							'columns' => 'sum(diversity_count) as total, square_number',
							'group' => 'square_number',
							'order' => 'total desc',
							'fieldAsIndex' => 'square_number',
					)
			);

		} else {

			//echo '<!--using uncompacted index (bad news)-->';

			$ot = $this->models->L2OccurrenceTaxon->_get(
					array(
							'id' => $d,
							'columns' => 'count(*) as total, square_number',
							'group' => 'square_number',
							'order' => 'total desc',
							'fieldAsIndex' => 'square_number',
					)
			);

		}

		if ($ot) {
			$d = current($ot);
			$max = $d['total'];
			end($ot);
			$d = current($ot);
			$min = $d['total'];
		} else {
			$max = $min = 0;
		}
			
		$legend = array();

		foreach((array)$ot as $key => $val) {
				
			$ot[$key]['pct'] = round(($val['total'] / $max) * 100);
			$x = ceil($ot[$key]['pct'] / (100 / $this->controllerSettings['l2DiversityIndexNumOfClasses']));
			$ot[$key]['class'] = $x;
			$legend[$x] = $x;


		}
			
		ksort($legend);

		$prevmin = $min;
			
		foreach((array)$legend as $key => $val) {
			$thisMax =
			$max +
			(
					($val - $this->controllerSettings['l2DiversityIndexNumOfClasses']) *
					($max / $this->controllerSettings['l2DiversityIndexNumOfClasses'])
			);

			$legend[$key] =
			array(
					'min' => floor($prevmin),
					'max' => floor($thisMax)
			);
				
			$prevmin = $thisMax;
		};
			
		$dataToStore = array(
				'index' => $ot,
				'min' => $min,
				'max' => $max,
				'legend' => $legend
		);
	
		$this->saveCache('map-divIndex-'. $mapId. '-' . $sessIdx, $dataToStore);
		
		return $dataToStore;
	}

	private function l2GetDiversity($p)
	{
	
		$index = $this->l2GetDiversityIndex($p['m'],(isset($p['types']) ? $p['types'] : null));

		if (isset($index)) {

			foreach((array)$index['index'] as $key => $val) {
		
				$d[] = array('id' => $key,'class' => $val['class'],'total' => $val['total']);
				
			}
			
			$index['index'] = $d;
			
			unset($d);

			foreach((array)$index['legend'] as $key => $val) {
		
				$d[] = array('id' => $key,'min' => $val['min'],'max' => $val['max']);
				
			}
			
			$index['legend'] = $d;

			$this->smarty->assign('returnText',json_encode($index));
			
		}
	
	}

	private function l2GetCellDiversity($p)
	{
	
		$taxa = $this->l2DoSearchMap(
			$p['m'],
			(array)$p['id'],
			($this->rHasVal('types') ? $this->requestData['types'] : '*')
		);
		
		if (isset($taxa)) {

			$this->smarty->assign('returnText',
				$this->makeLookupList(
					$taxa,
					'species',
					'../species/taxon.php?id=%s'
				)
			);
			
		}
	
	}
	
}
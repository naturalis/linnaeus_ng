<?php
/*

div index:
	values of "-"
	double nodes in boudary nodes


update dev_diversity_index set project_id = 278;
update dev_diversity_index set type_id = 468 where type_id = 418;
update dev_diversity_index set type_id = 469 where type_id = 419;
update dev_diversity_index set type_id = 470 where type_id = 420;
update dev_diversity_index set type_id = 471 where type_id = 421;
update dev_diversity_index set type_id = 472 where type_id = 422;
update dev_diversity_index set type_id = 473 where type_id = 423;
update dev_diversity_index set type_id = 475 where type_id = 425;
update dev_diversity_index set type_id = 476 where type_id = 426;
update dev_diversity_index set type_id = 477 where type_id = 427;



*/

include_once ('Controller.php');

class MapKeyController extends Controller
{
    
    public $usedModels = array(
		'map',
		'map_name',
		'occurrence_taxon',
		'geodata_type',
		'geodata_type_title',
		'diversity_index'
	);
    
    public $usedHelpers = array('csv_parser_helper');

    public $controllerPublicName = 'Distribution';

	public $cssToLoad = array(
		'basics.css',
		'map.css',
		'lookup.css'
	);

	public $jsToLoad = array(
		'all' => 
			array(
				'main.js',
				'mapkey.js',
				'http://maps.google.com/maps/api/js?sensor=false&libraries=drawing',
				'lookup.js'
			)
		);


    /**
     * Constructor, calls parent's constructor
     *
     * @access     public
     */
    public function __construct ()
    {
        
        parent::__construct();

		$this->checkForProjectId();

		$this->smarty->assign('isOnline',$this->checkRemoteServerAccessibility());

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

		$this->getTaxonTree(array('includeOrphans' => false,'forceLookup' => !isset($this->treeList)));

		$taxa = $this->getTaxaWithOccurrences();

		$d = current($taxa);
		
		if (isset($d['id'])) 
			$this->redirect('examine_species.php?id='.$d['id']);
		else
			$this->redirect('examine.php');
	
		/*
        $this->setPageName( _('Index'));
		
        $this->printPage();
		*/
    
    }

	public function examineAction()
	{

		$this->setPageName(_('Choose a species'));
		
		$pagination = $this->getPagination($this->getTaxaWithOccurrences(),$this->controllerSettings['speciesPerPage']);

		$this->smarty->assign('prevStart', $pagination['prevStart']);
	
		$this->smarty->assign('nextStart', $pagination['nextStart']);

		if(isset($pagination['items'])) $this->smarty->assign('taxa',$pagination['items']);

		$this->printPage();	

	}

	public function examineSpeciesAction()
	{

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

	public function compareAction()
	{

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

	public function searchAction()
	{

		$this->setPageName(_('Search'));

		if ($this->rHasVal('coordinates')) {
		
			$coordinates = $this->rectangleIntoPolygon($this->requestData['coordinates']);

			$results = $this->searchPolygon($coordinates);

			if ($results['count']['total']>0) {

				$this->getTaxonTree(array('includeOrphans' => false,'forceLookup' => !isset($this->treeList)));
		
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
	
	public function diversityAction()
	{
	
		$this->setPageName(_('Diversity index'));

		$data = $this->getDiversityIndex();

		foreach((array)$data as $key => $val) $data[$key]['nodes'] = json_decode($val['boundary_nodes']);

		$geoDataTypes = $this->getGeoDataTypes();

		$this->smarty->assign('geoDataTypes',$geoDataTypes);

		$this->smarty->assign('data',$data);

		$this->smarty->assign('mapBorder',$this->getMapBorder($data));
		
		$this->printPage();		
	
	}
	
	private function getDiversityIndex()
	{
	
		return $this->models->DiversityIndex->_get(array('id' => array('project_id' => $this->getCurrentProjectId())));
	
	
	}

	public function ajaxInterfaceAction()
	{

		if (!$this->rHasVal('action')) $this->smarty->assign('returnText','error');
		
		if ($this->rHasVal('action','get_lookup_list') && !empty($this->requestData['search'])) {

            $this->getLookupList($this->requestData['search']);
			
		}

		$this->allowEditPageOverlay = false;
		
        $this->printPage();
	
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

		foreach ((array)$gt as $key => $val) {

			$gtl = $this->models->GeodataTypeTitle->_get(
				array(
					'id' => array(
						'project_id' => $this->getCurrentProjectId(),
						'language_id' => $this->getCurrentLanguageId(),
						'type_id' => $val['id']
					),
					'columns' => 'title'
				)
			);
			
			$gt[$key]['title'] = isset($gtl[0]['title']) ? $gtl[0]['title'] : '-';
			$gt[$key]['colour_inverse'] = $this->inverseColour($val['colour']);
	
			$_SESSION['app']['user']['map']['geoDataTypes'] = $gt;

		}

		if (isset($id))
			return $_SESSION['app']['user']['map']['geoDataTypes'][$id];
		else
			return $_SESSION['app']['user']['map']['geoDataTypes'];
	
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

		$this->getTaxonTree(array('includeOrphans' => false,'forceLookup' => !isset($this->treeList)));

		$taxa = $this->getTaxaOccurrenceCount($this->getTreeList());
		
		$this->customSortArray($taxa,array('key' => 'taxon','maintainKeys' => true));
		
		return $taxa;
	
	}

	private function getLookupList($search)
	{

		$search = str_replace(array('/','\\'),'',$search);

		if (empty($search)) return;
		
		$regexp = '/'.preg_quote($search).'/i';

		$l = array();

		$this->getTaxonTree(array('includeOrphans' => false,'forceLookup' => !isset($this->treeList)));

		$taxa = $this->getTaxaOccurrenceCount($this->getTreeList());
				
		foreach((array)$taxa as $key => $val) {

			if (preg_match($regexp,$val['taxon']) == 1)
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

}
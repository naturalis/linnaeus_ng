<?php

include_once ('Controller.php');

class MapKeyController extends Controller
{
    
    public $usedModels = array(
		'map',
		'map_name',
		'occurrence_taxon',
		'geodata_type',
		'geodata_type_title'
	);
    
    public $usedHelpers = array('csv_parser_helper');

    public $controllerPublicName = 'Map key';

	public $cssToLoad = array(
		'basics.css',
		'map.css'
	);

	public $jsToLoad = array(
		'all' => 
			array(
				'main.js',
				'mapkey.js',
				'http://maps.google.com/maps/api/js?sensor=false'
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
    
        $this->setPageName( _('Index'));
		
        $this->printPage();
    
    }

	public function examineAction()
	{

		$this->setPageName(_('Choose a species'));

		$this->getTaxonTree(array('includeOrphans' => false,'forceLookup' => !isset($this->treeList)));

		// get taxa
		$taxa = $this->getTreeList();
		
		$pagination = $this->getPagination($taxa,$this->controllerSettings['speciesPerPage']);

		$this->smarty->assign('prevStart', $pagination['prevStart']);
	
		$this->smarty->assign('nextStart', $pagination['nextStart']);

		if(isset($pagination['items'])) $this->smarty->assign('taxa',$pagination['items']);

		if(isset($pagination['items'])) $this->smarty->assign('taxonOccurrenceCount',$this->getTaxaOccurrenceCount());

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

		$this->getTaxonTree(array('includeOrphans' => false,'forceLookup' => !isset($this->treeList)));

		$taxa = $this->getTreeList();

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

			$results = $this->searchPolygon($this->requestData['coordinates']);

			if ($results['count']['total']>0) {

				$this->getTaxonTree(array('includeOrphans' => false,'forceLookup' => !isset($this->treeList)));
		
				$taxa = $this->getTreeList();				

				$geoDataTypes = $this->getGeoDataTypes();

			}

		}

		if (isset($this->requestData['coordinates'])) {
		
			$c = explode('),(',$this->requestData['coordinates']);
			
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

		$this->printPage();	

	}




	private function checkRemoteServerAccessibility()
	{

		$f = @fopen($this->controllerSettings['urlToCheckConnectivity'], 'r');

		if (!$f) return false;

		fclose($f);

		return true;
		
	}

	private function getTaxaOccurrenceCount()
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

		return $ot;
	
	}

	private function inverseColour($hexColour)
	{

		$r = dechex(255-hexdec(substr($hexColour,0,2)));
		$g = dechex(255-hexdec(substr($hexColour,2,2)));
		$b = dechex(255-hexdec(substr($hexColour,4,2)));

		return (strlen($r)==1 ? $r.$r : $r).(strlen($g)==1 ? $g.$g : $g).(strlen($b)==1 ? $b.$b : $b);
	
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
	
			$_SESSION['user']['map']['geoDataTypes'] = $gt;

		}

		if (isset($id))
			return $_SESSION['user']['map']['geoDataTypes'][$id];
		else
			return $_SESSION['user']['map']['geoDataTypes'];
	
	}

	private function getMapBorder($occurrences)
	{

		$sLat = $sLng = 999;
		$lLat = $lLng = -999;

		foreach((array)$occurrences as $key => $val) {

			if ($val['latitude'] && $val['longitude']) {

				if (!empty($val['latitude']) && $val['latitude'] < $sLat) $sLat = $val['latitude'];
				if (!empty($val['latitude']) && $val['latitude'] > $lLat) $lLat = $val['latitude'];
				if (!empty($val['longitude']) && $val['longitude'] < $sLng) $sLng = $val['longitude'];
				if (!empty($val['longitude']) && $val['longitude'] > $lLng) $lLng = $val['longitude'];

			} else
			if ($val['nodes']) {

				foreach((array)$val['nodes'] as $nKey => $nVal) {
		
					if ($nVal[0] && $nVal[1]) {
		
						if (!empty($nVal[0]) && $nVal[0] < $sLat) $sLat = $nVal[0];
						if (!empty($nVal[0]) && $nVal[0] > $lLat) $lLat = $nVal[0];
						if (!empty($nVal[1]) && $nVal[1] < $sLng) $sLng = $nVal[1];
						if (!empty($nVal[1]) && $nVal[1] > $lLng) $lLng = $nVal[1];
		
					}

				}
		
			}

		}

		if ($sLat==999) $sLat = 51;
		if ($sLng==999) $sLng = 2.6;

		if ($lLat==-999) $lLat = 53;
		if ($lLng==-999) $lLng = 8.7;

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

}
























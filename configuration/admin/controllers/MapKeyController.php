<?php

/*

keuze google map(s)
	vierkant op basis coordinaten
	handmatig getekend
	een of andere google string
		coordinaten
		lagen
		zoom level

per soort: voorkomen
	punt
	polygoon
	per unit (punt/polygoon) metadata
		wanneer
		hoeveel
		url
	in kmz-file / csv
	één kmz-file per soort
	kmz files
		uploadnaam
		nieuwe eigen onveranderlijke unieke naam geven
		date of adding
		user comment (opt)

browsing
	lijst van soorten, download/delete/add kmz file
	lijst van soorten met kmz
	lijst van soorten zonder kmz

zoeken
	kies kaart
	draw polygon
	click something
	results:
		txt: welke soorten
		map: incidences + polygons

weergave
	gebied -> welke soorten
	soort -> welke gebieden
	soorten -> welke gebieden
	download map as png
		lokaal opslaan
		display only
		meta:
			"naam" (whatever)
			datum van opslag
			koppeling welke kmz-files
			user comment (opt)
		lijst van afbeeldingen
		lijst van soorten -> afbeeldingem

auto-switch
	detect mechanism
	als offline, list of pics
	als online, map + avial list of pics

*/

include_once ('Controller.php');

class MapKeyController extends Controller
{
    
    public $usedModels = array(
		'map',
		'map_name'
	);
    
    public $usedHelpers = array();

    public $controllerPublicName = 'Map key';

	public $cssToLoad = array('map.css');

	public $jsToLoad = array(
		'all' => 
			array(
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
    
        $this->checkAuthorisation();
        
        $this->setPageName( _('Index'));

        $this->printPage();
    
    }

	private function getSnapShots($id=null)
	{

		$d = array('project_id' => $this->getCurrentProjectId());

		if (isset($id)) $d[] = array('id' => $id);

		$m = $this->models->Map->_get(
			array(
				'id' => $d
			)
		);

		foreach ((array)$m as $key => $val) {

			$mn = $this->models->MapName->_get(
				array(
					'id' => array(
						'project_id' => $this->getCurrentProjectId(),
						'map_id' => $val['id']
					)
				)
			);
			
			// DO SOMETHING!

		}
		
		return isset($m) ? (isset($id) ? $m[0] : $m ) : null;

	}

	public function snapshotsAction()
	{

        $this->checkAuthorisation();
        
        $this->setPageName( _('Snapshots'));
		
		$this->smarty->assign('snapshots',$this->getSnapShots());

        $this->printPage();
	
	}



    public function snapshotAction()
    {

        $this->checkAuthorisation();

        $this->setBreadcrumbIncludeReferer(
            array(
                'name' => _('Snapshots'), 
                'url' => $this->baseUrl . $this->appName . '/views/' . $this->controllerBaseName . '/snapshots.php'
            )
        );
		
		if ($this->rHasId() && $this->rHasVal('rnd') && !$this->isFormResubmit()) {

			$s = $this->models->Map->save(
				array_merge(
					array('project_id' => $this->getCurrentProjectId()),
					$this->requestData
				)
			);

			if ($s) {

				$ss = $this->requestData;
	
				if (!$this->rHasId()) $ss['id'] = $this->models->Map->getNewId();

		        $this->setPageName(sprintf(_('Editing snapshot "%s"'),$ss['name']));
	
			    $this->addMessage(sprintf(_('Snapshot "%s" saved'),$ss['name']));

			} else {

			    $this->addError( _('Error saving snapshot'));

			}

		} else
		if ($this->rHasId()) {
		
			$ss = $this->getSnapShots($this->requestData['id']);

			$this->setPageName(sprintf(_('Editing snapshot "%s"'),$ss['name']));

		} else {
    	
			$this->setPageName( _('New snapshot'));

        }

		if(isset($ss)) {

			$this->smarty->assign('middelLat',($ss['coordinate1_lat']+$ss['coordinate2_lat']) / 2);
			$this->smarty->assign('middelLng',($ss['coordinate1_lng']+$ss['coordinate2_lng']) / 2);
			$this->smarty->assign('initZoom',$ss['zoom']);

			$this->smarty->assign('snapshot',$ss);

		} else {

			$this->smarty->assign('middelLat',52.22);
			$this->smarty->assign('middelLng',4.53);
			$this->smarty->assign('initZoom',7);

		}

        $this->printPage();
    
    }


    public function snapviewAction()
    {

        $this->checkAuthorisation();

        $this->setBreadcrumbIncludeReferer(
            array(
                'name' => _('Snapshots'), 
                'url' => $this->baseUrl . $this->appName . '/views/' . $this->controllerBaseName . '/snapshots.php'
            )
        );
		
		if ($this->rHasId()) {
		
			$ss = $this->getSnapShots($this->requestData['id']);

	        $this->setPageName(sprintf(_('Viewing snapshot "%s"'),$ss['name']));
	
		} else {
    	
			$s = $this->models->Map->save(
				array_merge(
					array('project_id' => $this->getCurrentProjectId()),
					$this->requestData
				)
			);
			
			$ss = $this->requestData;

			if ($s) { 

				$ss['id'] = $this->models->Map->getNewId();

		        $this->setPageName(sprintf(_('Editing snapshot "%s"'),$ss['name']));

			} else {

			    $this->setPageName( _('New snapshot'));

			}

        }

		if(isset($ss)) {

			$this->smarty->assign('middelLat',($ss['coordinate1_lat']+$ss['coordinate2_lat']) / 2);
			$this->smarty->assign('middelLng',($ss['coordinate1_lng']+$ss['coordinate2_lng']) / 2);

			$this->smarty->assign('snapshot',$ss);

		}

        $this->printPage();
    
    }

}
























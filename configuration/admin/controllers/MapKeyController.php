<?php

/*

	make default central point configurable
			$this->smarty->assign('middelLat',24.886436490787712);
			$this->smarty->assign('middelLng',-70.2685546875);
			$this->smarty->assign('initZoom',5);

*/


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
		'map_name',
		'occurrence_taxon'
	);
    
    public $usedHelpers = array('csv_parser_helper');

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
		
		$this->smarty->assign('isOnline',$this->checkRemoteServerAccessibility());

        $this->printPage();
    
    }

	private function checkRemoteServerAccessibility()
	{
	
		$f = @fopen('http://maps.google.com/maps/api/js?sensor=false', 'r');

		if (!$f) return false;

		fclose($f);

		return true;
		
	}

	private function getMapViews($id=null)
	{

		$d = array('project_id' => $this->getCurrentProjectId());

		if (isset($id)) $d['id'] = $id;

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

	public function mapViewsAction()
	{

        $this->checkAuthorisation();
        
        $this->setPageName( _('Map views'));
		
		$this->smarty->assign('mapViews',$this->getMapViews());

        $this->printPage();
	
	}

    public function mapViewEditAction()
    {

        $this->checkAuthorisation();

        $this->setBreadcrumbIncludeReferer(
            array(
                'name' => _('Map views'), 
                'url' => $this->baseUrl . $this->appName . '/views/' . $this->controllerBaseName . '/map_views.php'
            )
        );
		
		if ($this->rHasVal('rnd') && !$this->isFormResubmit()) {

			$s = $this->models->Map->save(
				array_merge(
					array('project_id' => $this->getCurrentProjectId()),
					$this->requestData
				)
			);

			if ($s) {

				$mv = $this->requestData;
	
				if (!$this->rHasId()) $mv['id'] = $this->models->Map->getNewId();

		        $this->setPageName(sprintf(_('Editing map view "%s"'),$mv['name']));
	
			    $this->addMessage(sprintf(_('Map view "%s" saved'),$mv['name']));

			} else {

			    $this->addError( _('Error saving map view'));

			}

		} else
		if ($this->rHasId()) {
		
			$mv = $this->getMapViews($this->requestData['id']);

			$this->setPageName(sprintf(_('Editing map view "%s"'),$mv['name']));

		} else {
    	
			$this->setPageName( _('New map view'));

        }

		if(isset($mv)) {

			$this->smarty->assign('middelLat',($mv['coordinate1_lat']+$mv['coordinate2_lat']) / 2);
			$this->smarty->assign('middelLng',($mv['coordinate1_lng']+$mv['coordinate2_lng']) / 2);
			$this->smarty->assign('initZoom',$mv['zoom']);

			$this->smarty->assign('mapView',$mv);

		} else {

			$this->smarty->assign('middelLat',52.22);
			$this->smarty->assign('middelLng',4.53);
			$this->smarty->assign('initZoom',7);

		}

		$this->smarty->assign('isOnline',$this->checkRemoteServerAccessibility());

        $this->printPage();
    
    }

    public function mapViewAction()
    {

        $this->checkAuthorisation();

        $this->setBreadcrumbIncludeReferer(
            array(
                'name' => _('Map views'), 
                'url' => $this->baseUrl . $this->appName . '/views/' . $this->controllerBaseName . '/map_views.php'
            )
        );
		
		if ($this->rHasId()) {

			$mv = $this->getMapViews($this->requestData['id']);

	        $this->setPageName(sprintf(_('Viewing map view "%s"'),$mv['name']));
	
        } else {
	
			$this->redirect('map_views.php');
	
		}

		if(isset($mv)) {

			$this->smarty->assign('middelLat',($mv['coordinate1_lat']+$mv['coordinate2_lat']) / 2);
			$this->smarty->assign('middelLng',($mv['coordinate1_lng']+$mv['coordinate2_lng']) / 2);

			$this->smarty->assign('mapView',$mv);

		}

		$this->smarty->assign('isOnline',$this->checkRemoteServerAccessibility());

        $this->printPage();
    
    }

    public function speciesAction()
    {

        $this->checkAuthorisation();

		$this->setPageName(_('Species occurrences'));

		$this->getTaxonTree();

		foreach((array)$this->treeList as $key => $val) {

			$ot = $this->models->OccurrenceTaxon->_get(
				array(
					'id' => array(
						'project_id' => $this->getCurrentProjectId(),
						'taxon_id' => $val['id'],
					),
					'columns' => 'id,type,AsText(coordinate) as coordinate,AsText(boundary) as boundary'
				)
			);
			
			if ($ot) {

				$val['occurrences'] = $ot;
				$taxa[] = $val;

			}

		}	

		if(isset($taxa)) $this->smarty->assign('taxa',$taxa);

		$this->printPage();
		
    }

	private function getSpeciesOccurrence($id)
	{

		$ot = $this->models->OccurrenceTaxon->_get(
			array(
				'id' => array(
					'id' => $id,
					'project_id' => $this->getCurrentProjectId(),
				)
			)
		);

		$this->getTaxonTree();

		if ($ot) $ot[0]['taxon'] = $this->treeList[$ot[0]['taxon_id']];

		return $ot[0];
	
	}

	private function getPolygonCentre($p)
	{

		if (count((array)$p)==0) return null;

		$x = $y = 0;

		foreach((array)$p as $key => $val) {
			
			$x += $val[0];
			$y += $val[1];
	
		}
	
		return array($x/count($p),$y/count($p));
	
	}

	public function speciesShowAction()
	{

        $this->checkAuthorisation();

		$isOnline = $this->checkRemoteServerAccessibility();

		if ($this->rHasId() && $isOnline) {
		
			$so = $this->getSpeciesOccurrence($this->requestData['id']);

        }

		$this->setPageName(sprintf(_('Species occurrences of "%s"'),$so['taxon']['taxon']));

		if(isset($so) && $so['type']=='marker') {

			$this->smarty->assign('middelLat',$so['latitude']);
			$this->smarty->assign('middelLng',$so['longitude']);

			$this->smarty->assign('marker',$so);

		} else
		if(isset($so) && $so['type']=='polygon') {

			$so['nodes'] = json_decode($so['boundary_nodes']);

			$middle = $this->getPolygonCentre($so['nodes']);

			$this->smarty->assign('middelLat',$middle[0]);
			$this->smarty->assign('middelLng',$middle[1]);

			$this->smarty->assign('polygon',$so);

		} else {

			$this->smarty->assign('middelLat',24.886436490787712);
			$this->smarty->assign('middelLng',-70.2685546875);

		}

		$this->smarty->assign('initZoom',5);

		$this->smarty->assign('isOnline',$isOnline);

        $this->printPage();

	
	}


	private function sanitizeDotsAndCommas($val)
	{
	
		if (strpos($val,',')!==false && strpos($val,'.')!==false) {

			$val = str_replace(',','',$val);

		} else
		if (strpos($val,',')!==false && strpos($val,'.')===false) {
	
			$val = str_replace(',','.',$val);

		}
		if (substr_count($val,'.')>1) {

			$x = strrpos($val,'.');

			$val = str_replace('.',substr($val,0,$x-1)).substr($val,$x);

		}

		return $val;
	
	}


    public function fileAction()
    {

		$this->checkAuthorisation();

		$this->setPageName(_('Occurrence file upload'));

		if ($this->requestDataFiles && !$this->isFormResubmit()) {

			switch ($this->requestData["delimiter"]) {
				case 'comma' :
					$this->helpers->CsvParserHelper->setFieldDelimiter(',');
					break;
				case 'semi-colon' :
					$this->helpers->CsvParserHelper->setFieldDelimiter(';');
					break;
				case 'tab' :
					$this->helpers->CsvParserHelper->setFieldDelimiter("\t");
					break;
			}
			
			$this->helpers->CsvParserHelper->parseFile($this->requestDataFiles[0]["tmp_name"]);
			
			$this->addError($this->helpers->CsvParserHelper->getErrors());
			
			if (!$this->getErrors()) {

				$this->getTaxonTree();
				
				$r = $this->helpers->CsvParserHelper->getResults();
				
				$line = 0;
				
				foreach((array)$r as $key => $val) {
				
					$line++;
				
					if (is_numeric($val[0])) {
					
						$taxonId = $val[0];
						
						if (isset($this->treeList[$taxonId])) {

							if (trim(strtolower($this->treeList[$taxonId]['taxon']))==trim(strtolower($val[1]))) {
							
								if ($val[4]=='') {
								// marker
								
									if (!empty($val[2]) && !empty($val[3])) {
									
										$val[2] = $this->sanitizeDotsAndCommas($val[2]);
										$val[3] = $this->sanitizeDotsAndCommas($val[3]);
										
										$ot = $this->models->OccurrenceTaxon->save(
											array(
												'id' => null,
												'project_id' => $this->getCurrentProjectId(),
												'taxon_id' => $taxonId,
												'type' => 'marker',
												'coordinate' => '#Point('.$val[2].','.$val[3].')',
												'latitude' => $val[2],
												'longitude' => $val[3]
											)
										);

										if ($ot===true) {
										
											$this->addMessage(
												sprintf(
													_('Row %s: saved marker for "%s".'),
													$line,
													$val[1]
												)
											);
										
										} else {

											$this->addError(
												sprintf(
													_('Row %s: unable to save marker for "%s". Duplicate?'),
													$line,
													$val[1]
												)
											);

										}
									
									} else {
									
										$this->addError(
											sprintf(
												_('Row %s: marker for "%s" misses %s.'),
												$line,
												$val[1],
												(empty($val[2]) ? _('latitude') : _('longitude') )
											)
										);
									
									}
								
								} else {
								// polygon
								
									$continue=true;
									$i=0;
									$geoStr = '';
									$nodes = array();
									
									while($continue && $i<=9999) {
									
										$cLat = isset($val[4+$i]) ? $val[4+$i] : null;
										$cLon = isset($val[5+$i]) ? $val[5+$i] : null;
										
										if ($cLat=='') {
										
											$continue = false;
										
										} else {
										
											if ($cLon=='') {
											
												$this->addError(
													sprintf(
														_('Row %s: polygon node for "%s" misses longitude'),
														$line,
														$val[1]
													)
												);
												
											} else {
												
												$cLat = $this->sanitizeDotsAndCommas($cLat);
												$cLon = $this->sanitizeDotsAndCommas($cLon);
												
												$geoStr .= $cLat.' '.$cLon.',';
												$nodes[] = array($cLat,$cLon);
											
											}
										
										}
										
										$i=$i+2;
									
									}
									
									if ($geoStr) {
									
										$ot = $this->models->OccurrenceTaxon->save(
											array(
												'id' => null,
												'project_id' => $this->getCurrentProjectId(),
												'taxon_id' => $taxonId,
												'type' => 'polygon',
												'boundary' => "#GeomFromText('POLYGON((".rtrim($geoStr,',')."))')",
												'boundary_nodes' => json_encode($nodes)
											)
										);
										
										if ($ot===true) {
										
											$this->addMessage(
												sprintf(
													_('Row %s: saved polygon for "%s".'),
													$line,
													$val[1]
												)
											);
										
										} else {

											$this->addError(
												sprintf(
													_('Row %s: unable to save polygon for "%s". Duplicate?'),
													$line,
													$val[1]
												)
											);
										
										}										
										
										unset($geoStr);
										unset($nodes);

									}
								
								}
							
							} else {
							
								$this->addError(
									sprintf(
										_('Row %s: mismatch for taxa %s: "%s" (file) <=> "%s" (database)'),
										$line,
										$taxonId,
										$val[1],
										$this->treeList[$taxonId]['taxon']
									)
								);

							}
							
						} else {
						
							$this->addError(sprintf(_('Row %s: unknown ID %s'),$line,$taxonId));
						
						}
					
					} else {
					
						// first col should be ID, if not an integer: skip line
					
					}
				
				}
			
			}

		}
		
		$this->printPage();

	}


    public function downloadCSVAction()
    {

        $this->checkAuthorisation();

		$this->getTaxonTree();

		$this->smarty->assign('taxa',$this->treeList);

		header("Cache-Control: public");
		header("Content-Description: File Transfer");
		header("Content-Disposition: attachment; filename=taxa.csv");
		header("Content-Type: text/csv");
		header("Content-Transfer-Encoding: binary");

		$this->printPage();
		
    }


    public function testAction()
    {

        $this->checkAuthorisation();


		if ($this->rHasId()) {
		
			$ss = $this->getSnapShots($this->requestData['id']);

	        $this->setPageName(sprintf(_('Viewing map view "%s"'),$ss['name']));

        }

		if(isset($ss)) {

//			$this->smarty->assign('middelLat',($ss['coordinate1_lat']+$ss['coordinate2_lat']) / 2);
//			$this->smarty->assign('middelLng',($ss['coordinate1_lng']+$ss['coordinate2_lng']) / 2);

		} else {

			$this->smarty->assign('middelLat',24.886436490787712);
			$this->smarty->assign('middelLng',-70.2685546875);
			$this->smarty->assign('initZoom',5);

		}

		$this->smarty->assign('isOnline',$this->checkRemoteServerAccessibility());

        $this->printPage();
    
    }


}
























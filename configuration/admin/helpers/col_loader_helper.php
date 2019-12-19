<?php
/*

    make timeout fail into an error (might be pointless, as the calling function (speciescontroller) seems to have it's own timeout)
    (but how? a timeout triggers an error of type FATAL which cannot be caught with try/catch)

    dit helpt ook niet:
        Warning: file_get_contents(http://www.catalogueoflife.org/annual-checklist/2010/webservice?id=7024544&response=full):
        failed to open stream: HTTP request failed!
        maybe add:        <?php
        $ctx = stream_context_create(array(
            'http' => array(
                'timeout' => 1
                )
            )
        );
        file_get_contents("http://example.com/", 0, $ctx);
        ?>
        


*/

class ColLoaderHelper
{
    
    private $_errors;
    private $_speciesName;
    private $_speciesId;
    private $_result = false;
    private $_level = 0;
    private $_timeout;
    private $_numberOfChildLevels = 0; // 0 = all
    private $_includedIds = array();
    private $_conciseResults = true;//false;
    private $_includeResultsTimer = false;
    private $_timeTaken = false;
    private $_startTime = false;

    //see: http://webservice.catalogueoflife.org/
    const SPECIES_URL = 'http://www.catalogueoflife.org/annual-checklist/2010/webservice?name=%s';
    const ID_URL = 'http://www.catalogueoflife.org/annual-checklist/2010/webservice?id=%s&response=full';
    const TIMEOUT = 3000; // secs
    
    // data in JSON-format returned by CoL webservice doesn't parse in PHP's json_decode()

    /**
     * Constructor, calls parent's constructor and all initialisation functions
     *
     * @access     public
     */
    public function __construct ()
    {
    
        $this->setTimeout();

        $this->setResultStyle();

        $this->setTimerInclusion();

    }


    public function setResultStyle($concise = false)
    {

        $this->_conciseResults = ($concise == 'concise');

    }

    public function setTimerInclusion($timerInclusion = false)
    {
    
        $this->_includeResultsTimer = $timerInclusion;

    }

    public function setTaxonName($name = false)
    {
    
        if (!$name) {
        
            $this->addError(_('No species name given.'));
        
        } else {
        
            $this->_speciesName = $name;
    
        }

    }
    
    public function setTaxonId($id = false)
    {
    
        if (!$id) {
        
            $this->addError(_('No species ID given.'));
        
        } else {
        
            $this->_speciesId = $id;
    
        }

    }
    
    public function setTimeout($timeout = false)
    {
    
        if (!$timeout) {
        
            $this->_timeout = self::TIMEOUT;
        
        } else {
        
            $this->_timeout = $timeout;
    
        }

    }
    
    public function setNumberOfChildLevels($num)
    {

        $this->_numberOfChildLevels = $num;

    }
    
    /*public function getTaxon()
    {

        set_time_limit($this->_timeout);

        if (!$this->getErrors()) {
        
            if (!$this->_speciesId) {

                // get basic info for taxon, including id, based on name
                $raw = file_get_contents(sprintf(self::SPECIES_URL,urlencode($this->_speciesName)));
    
                $p = xml_parser_create();
    
                $s = xml_parse_into_struct($p, $raw, $data, $dataIndex);
                
                unset($raw);
    
                xml_parser_free($p);

                if ($s===1) {
    
                    if ($data[$dataIndex['RESULTS'][0]]["attributes"]["NUMBER_OF_RESULTS_RETURNED"] > 0) {
        
                        // get Catalogue Of Life ID for taxon
                        $this->_speciesId = $data[$dataIndex['ID'][0]]['value'];

                        unset($data);
                        unset($dataIndex);

                    } else {
    
                        $this->addError(_('Found no basic data for: ').$this->_speciesName);
    
                    }

                } else {
    
                    $this->addError(_('Unable to parse basic data'));
    
                }

            }
    
            if (!$this->_speciesId) {

                $this->addError(_('Unable to resolve taxon\'s Catalogue Of Life ID'));

            } else {

                // retrieve detail data for taxon, including progeny (recursive)
                $this->_result = $this->getTaxonDetail($this->_speciesId,true);
                
            }

        }

    }*/

    public function getTaxon()
    {

        set_time_limit($this->_timeout);

        if (!$this->getErrors()) {
        
            if (!$this->_speciesId) {

                // get basic info for taxon, including id, based on name
                $url = sprintf(self::SPECIES_URL,urlencode($this->_speciesName)).'&format=php';
                $data = @unserialize(file_get_contents($url));
                if ($data && is_array($data)) {
    
                    if ($data['number_of_results_returned'] > 0) {
         
                        // get Catalogue Of Life ID for taxon
                        $this->_speciesId = $data['results'][0]['id'];
                        unset($data);

                    } else {
    
                        $this->addError(_('Found no basic data for: ').$this->_speciesName);
    
                    }

                } else {
    
                    $this->addError(_('Unable to parse basic data'));
    
                }

            }
    
            if (!$this->_speciesId) {

                $this->addError(_('Unable to resolve taxon\'s Catalogue Of Life ID'));

            } else {

                // retrieve detail data for taxon, including progeny (recursive)
                $this->_result = $this->getTaxonDetail($this->_speciesId,true);
                
            }

        }

    }
    
    
    public function getResult()
    {

        //var_dump($this->_result);

        $this->_result['child_levels'] = $this->_level;

        return $this->_result;

    }


    public function getErrors()
    {
        
        if ($this->_errors) {

            return $this->_errors;

        } else {
        
            return false;

        }
    
    }

    private function addError ($e)
    {
        
        $this->_errors[] = $e;
    
    }

    /*private function getTaxonDetail($id,$includeParents=false)
    {

        if ($this->_includeResultsTimer) $this->timerStart();

        // get comprehensive info for id
        $raw = file_get_contents(sprintf(self::ID_URL,$id));

        // WORKAROUND: there are HTML-tags in CoL's output without any <![CDATA[...]>
        $raw = str_replace(array('<b>','</b>','<i>','</i>','<u>','</u>'),'',$raw);
    
        $p = xml_parser_create();
    
        $s = xml_parse_into_struct($p, $raw, $dataFull, $dataFullIndex);
        
        unset($raw);

        xml_parser_free($p);                        
    
        if ($s===1) {
    
            $start = $dataFullIndex['CLASSIFICATION'][0];
    
            $end = $dataFullIndex['CLASSIFICATION'][count((array)$dataFullIndex['CLASSIFICATION'])-1];
    
            $start_children = $dataFullIndex['CHILD_TAXA'][0];
    
            $end_children = $dataFullIndex['CHILD_TAXA'][count((array)$dataFullIndex['CHILD_TAXA'])-1];

            // parents
            if ($includeParents) {

                for ($i=$start;$i<=$end;$i++) {
                
                    $val = $dataFull[$i];

                    if (isset($val['tag']) && isset($val['value'])) {
    
                        if ($val['tag']=='ID') $t['id'] = $val['value'];
                        if ($val['tag']=='NAME') $t['name'] = $val['value'];
                        if ($val['tag']=='RANK') $t['rank'] = $val['value'];
                        if (!$this->_conciseResults) {
                            if ($val['tag']=='NAME_HTML') $t['name_html'] = $val['value'];
                            if ($val['tag']=='URL') $t['url'] = $val['value'];
                        }

                    }    
    
                    if (isset($val['tag']) && 
                        isset($val['type']) && 
                        $val['tag']=='TAXON' && 
                        $val['type']=='close'
                        ) {

                        if (!in_array($t['id'],$this->_includedIds)) $result['parent_taxa'][] = $t;

                        $this->_includedIds[] = $t['id'];

                        unset($t);
    
                    }

                }

            }

            // taxon
            $t['id'] = $dataFull[$dataFullIndex['ID'][0]]['value'];
            $t['name'] = $dataFull[$dataFullIndex['NAME'][0]]['value'];    
            $t['rank'] = $dataFull[$dataFullIndex['RANK'][0]]['value'];
            if (!$this->_conciseResults) {
                $t['name_html'] = $dataFull[$dataFullIndex['NAME_HTML'][0]]['value'];
                $t['url'] = $dataFull[$dataFullIndex['URL'][0]]['value'];
            }
    
            $result['taxon'] = $t;

            // children
            for ($i=$start_children;$i<=$end_children;$i++) {

                $val = $dataFull[$i];

                if (isset($val['tag']) && isset($val['value'])) {

                    if ($val['tag']=='ID') $t['id'] = $val['value'];
                    if ($val['tag']=='NAME') $t['name'] = $val['value'];
                    if ($val['tag']=='RANK') $t['rank'] = $val['value'];
                    if (!$this->_conciseResults) {
                        if ($val['tag']=='NAME_HTML') $t['name_html'] = $val['value'];
                        if ($val['tag']=='URL') $t['url'] = $val['value'];
                    }

                }    

                if (isset($val['tag']) && 
                    isset($val['type']) && 
                    $val['tag']=='TAXON' && 
                    $val['type']=='close'
                    ) {

                    if (!in_array($t['id'],$this->_includedIds)) $children[] = $t;

                    $this->_includedIds[] = $t['id'];

                    unset($t);

                }

            }


            // preserving memory
            unset($dataFull);
            unset($dataFullIndex);
            unset($start);
            unset($end);
            unset($start_children);
            unset($end_children);

            if ($this->_includeResultsTimer) $result['time_taken'] = $this->timerEnd();

            if ($this->_numberOfChildLevels !=0 && $this->_level >= $this->_numberOfChildLevels) {

                return $result;

            } else {

                $this->_level++;

                if (isset($children)) {

                    foreach((array)$children as $key => $val) {

                        $result['child_taxa'][$key] = $this->getTaxonDetail($val['id']);    

                    }    

                }
                
                return $result;

            }

        } else {
    
            $this->addError(_('Unable to parse comprehensive data'));

            return false;
    
        }

    }*/

    
    private function getTaxonDetail($id,$includeParents=false)
    {

        if ($this->_includeResultsTimer) $this->timerStart();

        // get comprehensive info for id
        $url = sprintf(self::ID_URL,$id).'&format=php';
        $dataFull = @unserialize(file_get_contents($url));

        if ($dataFull && is_array($dataFull)) {
    
            // parents
            if ($includeParents) {
                foreach ($dataFull['results'][0]['classification'] as $t) {
                    if ($this->_conciseResults) {
                        unset($t['name_html']);
                        unset($t['url']);
                    }

                    if (!in_array($t['id'],$this->_includedIds)) $result['parent_taxa'][] = $t;
                    $this->_includedIds[] = $t['id'];
                    unset($t);
                }
            }

            // taxon
            $t['id'] = $dataFull['results'][0]['id'];
            $t['name'] = $dataFull['results'][0]['name'];    
            $t['rank'] = $dataFull['results'][0]['rank'];
            if (!$this->_conciseResults) {
                $t['name_html'] = $dataFull['results'][0]['name_html'];
                $t['url'] = $dataFull['results'][0]['url'];
            }
    
            $result['taxon'] = $t;

			if (isset($dataFull['results'][0]['child_taxa'])) {
				// children
				foreach ($dataFull['results'][0]['child_taxa'] as $t) {
	
					if ($this->_conciseResults) {
						unset($t['name_html']);
						unset($t['url']);
					}
	
					if (!in_array($t['id'],$this->_includedIds)) $children[] = $t;
					$this->_includedIds[] = $t['id'];
					unset($t);
	 
				}
			}

            // preserving memory
            unset($dataFull);
 
            if ($this->_includeResultsTimer) $result['time_taken'] = $this->timerEnd();

            if ($this->_numberOfChildLevels !=0 && $this->_level >= $this->_numberOfChildLevels) {

                return $result;

            } else {

                $this->_level++;

                if (isset($children)) {

                    foreach((array)$children as $key => $val) {

                        $result['child_taxa'][$key] = $this->getTaxonDetail($val['id']);    

                    }    

                }
                
                return $result;

            }

        } else {
    
            $this->addError(_('Unable to parse comprehensive data'));

            return false;
    
        }

    }
    
    private function timerStart()
    {

        $this->_timeTaken = false;
        $mtime = microtime();
        $mtime = explode(' ', $mtime);
        $mtime = $mtime[1] + $mtime[0];
        $this->_startTime = $mtime;

    }

    private function timerEnd()
    {

        if (!$this->_startTime) return;    
        $mtime = microtime();
        $mtime = explode(" ", $mtime);
        $mtime = $mtime[1] + $mtime[0];
        $endtime = $mtime;
        $this->_timeTaken = ($endtime - $this->_startTime);
        $this->_startTime = false;
        return $this->_timeTaken;

     }

}



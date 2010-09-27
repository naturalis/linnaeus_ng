<?php
/*

	make timeout fail into an error (might be pointless, as the calling function (speciescontroller) seems to have it's own timeout)

	dit helpt ook niet:
		Warning: file_get_contents(http://www.catalogueoflife.org/annual-checklist/2010/webservice?id=7024544&response=full):
		failed to open stream: HTTP request failed!

*/

class ColLoaderHelper
{
    
    private $_errors;
	private $_speciesName;
	private $_result = false;
	private $_level = 0;
	private $_timeout;
	private $_numberOfChildLevels = 99;
	private $_includedIds = array();
	
	//see: http://webservice.catalogueoflife.org/
	const SPECIES_URL = 'http://www.catalogueoflife.org/annual-checklist/2010/webservice?name=%s';
	const ID_URL = 'http://www.catalogueoflife.org/annual-checklist/2010/webservice?id=%s&response=full';
	const TIMEOUT = 3000; // secs
	
	// data in JSON-format returned by CoL webservice doesn't parse in PHP's json_decode()

    /**
     * Constructor, calls parent's constructor and all initialisation functions
     *
     * @access 	public
     */
    public function __construct ($name=false,$timeout=false)
    {
	
		if ($name) $this->setTaxon($name);

		$this->setTimeout($timeout);

    }

	public function setTaxon($name = false)
	{
	
		if (!$name) {
		
			$this->addError(_('No species name given.'));
		
		} else {
		
			$this->_speciesName = $name;
	
		}

	}
	
	public function setTimeout($timeout)
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
	
	public function getTaxon()
	{

		set_time_limit($this->_timeout);

		if (!$this->getErrors()) {

			// get basic info for taxon, including id, based on name
			$raw = file_get_contents(sprintf(self::SPECIES_URL,urlencode($this->_speciesName)));

			$p = xml_parser_create();

			$s = xml_parse_into_struct($p, $raw, $data, $dataIndex);
			
			unset($raw);

			xml_parser_free($p);

			if ($s===1) {

				if ($data[$dataIndex['RESULTS'][0]]["attributes"]["NUMBER_OF_RESULTS_RETURNED"] > 0) {
	
					// get Catalogue Of Life ID for taxon
					$colId = $data[$dataIndex['ID'][0]]['value'];
					
					unset($data);
					unset($dataIndex);
	
					if (!$colId) {
	
						$this->addError(_('Unable to resolve taxon\'s Catalogue Of Life ID'));
	
					} else {

						// retrieve detail data for taxon, including progeny (recursive)
						$this->_result = $this->getTaxonDetail($colId,true);
						
					}

				} else {

					$this->addError(_('Found no data for: ').$this->_speciesName);

				}

			} else {

				$this->addError(_('Unable to parse basic data'));

			}	

		}

	}

	public function getResult()
	{

		$this->_result['levels'] = $this->_level;

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

	private function getTaxonDetail($id,$includeParents=false)
	{

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

			if ($includeParents) {

				for ($i=$start;$i<=$end;$i++) {
				
					$val = $dataFull[$i];

					if (isset($val['tag']) && isset($val['value'])) {
	
						if ($val['tag']=='ID') $t['id'] = $val['value'];
						if ($val['tag']=='NAME') $t['name'] = $val['value'];
						if ($val['tag']=='RANK') $t['rank'] = $val['value'];
						if ($val['tag']=='NAME_HTML') $t['name_html'] = $val['value'];
						if ($val['tag']=='URL') $t['url'] = $val['value'];
	
					}	
	
					if (isset($val['tag']) && 
						isset($val['type']) && 
						$val['tag']=='TAXON' && 
						$val['type']=='close'
						) {
	
						$result['parent_taxa'][] = $t;
	
						$this->_includedIds[] = $t['id'];

						unset($t);
	
					}

				}

			}

			for ($i=$start_children;$i<=$end_children;$i++) {

				$val = $dataFull[$i];

				if (isset($val['tag']) && isset($val['value'])) {

					if ($val['tag']=='ID') $t['id'] = $val['value'];
					if ($val['tag']=='NAME') $t['name'] = $val['value'];
					if ($val['tag']=='RANK') $t['rank'] = $val['value'];
					if ($val['tag']=='NAME_HTML') $t['name_html'] = $val['value'];
					if ($val['tag']=='URL') $t['url'] = $val['value'];

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

			$t['id'] = $dataFull[$dataFullIndex['ID'][0]]['value'];
			$t['name'] = $dataFull[$dataFullIndex['NAME'][0]]['value'];	
			$t['rank'] = $dataFull[$dataFullIndex['RANK'][0]]['value'];
			$t['name_html'] = $dataFull[$dataFullIndex['NAME_HTML'][0]]['value'];
			$t['url'] = $dataFull[$dataFullIndex['URL'][0]]['value'];
	
			$result['taxon'] = $t;

			// preserving memory
			unset($dataFull);
			unset($dataFullIndex);
			unset($start);
			unset($end);
			unset($start_children);
			unset($end_children);

			if ($this->_level >= $this->_numberOfChildLevels) {

				return $result;

			} else {

				if (isset($children)) {

					$this->_level++;

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

}



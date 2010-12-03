<?php

class CsvParserHelper
{

    private $_file;
    private $_results;
    private $_errors;
    private $_delimiter = ',';
    private $_enclosure = '"';
    private $_lineMax = false;
    private $_fieldMax = false;
    private $_dropAllWhites = true;
    private $_trimFields = true;
	private $_maxLineLength = 1000;
	private $_oldADLEsetting;

	private $_allowedDelimiters = array(',',';',"\t");
	private $_allowedEnclosures = array('"',"'",false);

    public function __construct ()
    {
        
		$this->_oldADLEsetting = ini_get('auto_detect_line_endings');

		ini_set('auto_detect_line_endings',true);
   
    }

    public function __destruct ()
    {
        
		ini_set('auto_detect_line_endings',$this->_oldADLEsetting);
    
    }

    public function parseFile($file)
    {

        $this->_file = $file;
        
        if ($this->testIfCSV()) {

            $this->readData();

            $this->processData();

        } else {

            $this->addError(_('File does not seem to be a CSV-file.'));

        }

    }

    public function setFieldEnclosure ($enclosure=false)
    {

		if (in_array($enclosure,$this->_allowedEnclosures)) $this->_enclosure = $enclosure;

    }

    public function setFieldDelimiter ($delimiter=false)
    {

		if (in_array($delimiter,$this->_allowedDelimiters)) $this->_delimiter = $delimiter;

    }


    public function setFieldMax ($num)
    {
        
        $this->_fieldMax = $num;

    }

    public function setDropAllWhites ($mode)
    {
        
        $this->_dropAllWhites = $mode;

    }

    public function setTrimFields ($mode)
    {
        
        $this->_trimFields = $mode;

    }

    public function getErrors ()
    {
        
        return $this->_errors;

    }

    public function getResults ()
    {
        
        return $this->_results;

    }

    private function testIfCSV ()
    {

        $handle = @fopen($this->_file, "r");

        if ($handle) {

            $b = fgets($handle, 1000);
            fclose($handle);
        
            $is_text = true;
        
            for($i=0;$i<strlen($b);$i++) {

                $v = substr($b,$i,1);

                if(ord($v) == 0) { 

                    $is_text = false;
                    break;

                }
            }

            return $is_text;
        }

    }


    private function readData()
    {

        if (($handle = fopen($this->_file, 'r')) !== false) {

			// fgetcsv does not allow for enclosure being absent
			if ($this->_enclosure==false) {

				while (($data = fgets($handle,$this->_maxLineLength)) !== false) {
				
					/*
						wish we had str_getcsv, but the specs say stick to PHPv5.2
						anyway, since there is no enclosure we can safely assume *every* delimiter is 
						an actual delimiter and not part of the data, so... bombs away!
						
						ps: great, even when $this->_enclosure is defined, its actual presence in the
						csv-file is totally optional, making this entire piece of code totally unnecessary. drat.
					*/
					$this->_results[] = explode($this->_delimiter,$data);
					
					if ($this->_lineMax && count((array)$this->_results) >= $this->_lineMax) {
					
						break;
					
					}
				
				}

			} else {

				while (($data = fgetcsv($handle,$this->_maxLineLength,$this->_delimiter,$this->_enclosure)) !== false) {
				
					$this->_results[] = $data;
					
					if ($this->_lineMax && count((array)$this->_results) >= $this->_lineMax) {
					
						break;
					
					}
				
				}

			}
			
			fclose($handle);
            
        } else {

            $this->addError(_('Could not read data from file.'));

        }

    }

    private function processData()
    {

        if ($this->_fieldMax) {

            foreach((array)$this->_results as $key => $val) {

                $this->_results[$key] = array_slice($val,0,$this->_fieldMax);

            }

        }

        if ($this->_dropAllWhites) {

            foreach((array)$this->_results as $key => $val) {

                $allWhite = true;

                foreach((array)$val as $resultskey => $result) {
            
                    if (strlen(trim($result))>0) {

                        $allWhite = false;

                    }
					
					if ($this->_trimFields) {

						$val[$resultskey]=trim($result);

					}

                }
                
                if (!$allWhite) {

                    $d[] = $val;

                }

            }
            
            $this->_results = $d;

        }

    }

    private function addError ($e)
    {
        
        $this->_errors[] = $e;
    
    }


}



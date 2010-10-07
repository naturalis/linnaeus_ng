<?php

class CsvParserHelper
{

    private $_file;
    private $_results;
    private $_errors;
    private $_separator = ',';
    private $_delimiter = '"';
    private $_lineMax = false;
    private $_fieldMax = false;
    private $_dropAllWhites = true;

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

    public function setFieldMax ($num)
    {
        
        $this->_fieldMax = $num;

    }

    public function setDropAllWhites ($mode)
    {
        
        $this->_dropAllWhites = $mode;

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

            while (($data = fgetcsv($handle, 1000, $this->_separator,$this->_delimiter)) !== false) {

                $this->_results[] = $data;
                
                if ($this->_lineMax && count((array)$this->_results) >= $this->_lineMax) {

                    break;

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

                }
                
                if     (!$allWhite) {

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



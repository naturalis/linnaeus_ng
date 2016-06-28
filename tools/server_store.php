<?php

	class LinnaeusServerStore
	{
        private $_fp;
        private $_curlResult;
        private $_csvFile;
        private $_csvHeader;

        private $_post = 'post';
        private $_data;
        private $_success = false;

        public function setCsvFile ($p) {
            $this->_csvFile = $p;
        }

	    private function openFp () {
            if (!$this->_fp) {
                $this->_fp = fopen($this->_csvFile, 'a') or
                    die("FATAL ERROR: cannot write to " . $this->_csvFile . "\n\n");
            }
        }

        private function closeFp () {
            if ($this->_fp) {
                fclose($this->_fp);
            }
        }

        public function run () {
            $this->openFp();
            $this->fetchData();
            $this->writeToCsv();
            $this->closeFp();
            $this->printResult();
        }

        private function fetchData () {
            if (isset($_POST[$this->_post]) && !empty($_POST[$this->_post])) {
                $this->_data = json_decode($_POST[$this->_post], true);
            }
        }

        private function writeToCsv () {
            if (!empty($this->_data)) {
                foreach ($this->_data as $row) {
                    // Write header?
                    if (!$this->_csvHeader) {
                        fputcsv($this->_fp, array_keys($row));
                        $this->_csvHeader = true;
                    }
                    fputcsv($this->_fp, array_values($row));
                }
                $this->_success = true;
            }
        }

        private function printResult() {
            header('Content-Type: application/json');
            die(json_encode(array('result' => $this->_success ? 'success' : 'henk')));
        }

	}

	$lss = new LinnaeusServerStore();
	$lss->setCsvFile(dirname(__FILE__) . '/output/linnaeus_push.csv');
	$lss->run();


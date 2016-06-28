<?php

	class LinnaeusServerCsv
	{
	    private $_mysqli;
	    private $_fileName = 'linnaeus_servers.csv';
	    private $_csvHeader = true;
	    private $_fp;

	    public function __construct() {
	        $this->connectDb();
        }

        private function connectDb () {
            $this->_mysqli = new mysqli('localhost', 'root', 'root', 'test');
            if ($this->_mysqli->connect_errno) {
                die('Cannot connect: ' . $this->mysqli->connect_errno);
            }
        }

        public function run () {
            $this->setHeaders();
            $this->writeData();
            die();
        }

	    private function setHeaders () {
            $now = gmdate("D, d M Y H:i:s");
            header("Expires: Tue, 01 Jul 1999 06:00:00 GMT");
            header("Cache-Control: max-age=0, no-cache, must-revalidate, proxy-revalidate");
            header("Last-Modified: {$now} GMT");
            header("Content-Type: application/force-download");
            header("Content-Type: application/octet-stream");
            header("Content-Type: application/download");
            header("Content-Disposition: attachment;filename={$this->_fileName}");
            header("Content-Transfer-Encoding: binary");
        }

		private function openFp () {
            if (!$this->_fp) {
                $this->_fp = fopen('php://output', 'w');
            }
        }

        private function closeFp () {
            if ($this->_fp) {
                fclose($this->_fp);
            }
        }

        private function writeData () {
            // Get today's records only
            $q = 'select * from `lng_csv` where `check_date` > concat(curdate(), " 00:00:00")';
            $r = $this->_mysqli->query($q);
            $i = 0;

            if ($r->num_rows > 0) {
                $this->openFp();
                while ($row = $r->fetch_assoc()) {
                    unset($row['id']);
                    if ($i == 0 && $this->_csvHeader) {
                        fputcsv($this->_fp, array_keys($row));
                    }
                    fputcsv($this->_fp, array_values($row));
                    $i++;
                }
                $this->closeFp();
            }
        }

	}

	$lss = new LinnaeusServerCsv();
	$lss->run();


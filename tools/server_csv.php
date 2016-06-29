<?php

	class LinnaeusServerCsv
	{
	    private $_mysqli;

	    private $_fileName = 'linnaeus_servers';
	    private $_csvHeader = true;
	    private $_fp;

        public static $_post = 'post';
        private $_data;
        private $_success = false;
        private $_curlResult;


	    public function __construct() {
	        $this->connectDb();
        }

		public function storeData () {
            $this->fetchData();
            $this->writeToDb();
            $this->printResult();
        }

	    public function printCsv () {
	        $this->setFileName();
            $this->setHeaders();
            $this->writeData();
            die();
        }

        private function connectDb () {
            $this->_mysqli = new mysqli('localhost', 'root', 'root', 'test');
            if ($this->_mysqli->connect_errno) {
                die('Cannot connect: ' . $this->mysqli->connect_errno);
            }
        }

        private function setFileName () {
            $this->_fileName .= '_' . date("Y-m-d_H-i-s") . '.csv';
        }

	    private function setHeaders () {
            $now = date("D, d M Y H:i:s");
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

        private function fetchData () {
            if (isset($_POST[$this::$_post]) && !empty($_POST[$this::$_post])) {
                $this->_data = json_decode($_POST[$this::$_post], true);
            }
        }

        private function writeToDb () {
            if (!empty($this->_data)) {
                foreach ($this->_data as $row) {
                    $q = 'insert into `lng_csv` (' . implode(', ', array_keys($row)) . ') values (' .
                        substr(str_repeat('?,', count($row)), 0, -1) . ')';
                    $stmt = $this->_mysqli->prepare($q);
                    $stmt->bind_param(
                        str_repeat('s', count($row)),
                        $row['project'],
                        $row['project_is_published'],
                        $row['user_name'],
                        $row['first_name'],
                        $row['last_name'],
                        $row['role'],
                        $row['email_address'],
                        $row['user_is_active'],
                        $row['last_login'],
                        $row['project_last_selected'],
                        $row['password_last_changed'],
                        $row['git_branch'],
                        $row['git_hash'],
                        $row['git_latest_hash'],
                        $row['code_up_to_date'],
                        $row['server_ip'],
                        $row['server_name'],
                        $row['check_date']
                    );
                    $stmt->execute();
                }
                $this->_success = true;
            }

        }

        private function printResult() {
            header('Content-Type: application/json');
            die(json_encode(array('result' => $this->_success ? 'success' : 'fail')));
        }

	}



	$lss = new LinnaeusServerCsv();
    if (isset($_POST[$lss::$_post]) && !empty($_POST[$lss::$_post])) {
        $lss->storeData();
    } else {
        $lss->printCsv();
    }



/*

-- --------------------------------------------------------

--
-- Table structure for table `lng_csv`
--

CREATE TABLE `lng_csv` (
  `id` int(11) NOT NULL,
  `project` varchar(100) DEFAULT NULL,
  `project_is_published` varchar(3) DEFAULT NULL,
  `user_name` varchar(50) DEFAULT NULL,
  `first_name` varchar(25) DEFAULT NULL,
  `last_name` varchar(25) DEFAULT NULL,
  `role` varchar(20) DEFAULT NULL,
  `email_address` varchar(50) DEFAULT NULL,
  `user_is_active` varchar(3) DEFAULT NULL,
  `last_login` datetime DEFAULT NULL,
  `project_last_selected` datetime DEFAULT NULL,
  `password_last_changed` datetime DEFAULT NULL,
  `git_branch` varchar(50) DEFAULT NULL,
  `git_hash` varchar(50) DEFAULT NULL,
  `git_latest_hash` varchar(50) DEFAULT NULL,
  `code_up_to_date` varchar(3) DEFAULT NULL,
  `server_ip` varchar(15) DEFAULT NULL,
  `server_name` varchar(100) DEFAULT NULL,
  `check_date` datetime DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Indexes for table `lng_csv`
--
ALTER TABLE `lng_csv`
  ADD PRIMARY KEY (`id`),
  ADD KEY `check_date` (`check_date`);

--
-- AUTO_INCREMENT for table `lng_csv`
--
ALTER TABLE `lng_csv`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

*/

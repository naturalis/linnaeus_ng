<?php

	class LinnaeusServerStore
	{
	    private $_mysqli;
        private $_curlResult;

        private $_post = 'post';
        private $_data;
        private $_success = false;

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
            $this->fetchData();
            $this->writeToDb();
            $this->printResult();
        }

        private function fetchData () {
            if (isset($_POST[$this->_post]) && !empty($_POST[$this->_post])) {
                $this->_data = json_decode($_POST[$this->_post], true);
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

	$lss = new LinnaeusServerStore();
	$lss->run();


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


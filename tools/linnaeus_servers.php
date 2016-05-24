<?php

	class LinnaeusServerInfo
	{
        private $_linnaeusUser;
        private $_linnaeusPassword;
        private $_linnaeusWebservice = 'linnaeus_ng/admin/views/webservices/im.php';
        private $_linnaeusUrl;
        private $_server;
        private $_linnaeusProtocol = 'http';

        private $_fp;
        private $_curlResult;
        private $_csvFile = 'linnaeus_servers.csv';
        private $_csvHeader = false;

        private $_linnaeusServers = array();
	    private $_ipRanges = array();

        private $_gitUrl = 'https://api.github.com/repos/naturalis/linnaeus_ng/git/refs/heads/%s';
        private $_gitUser = false;
        private $_gitPassword = false;

	    public function setLinnaeusUser ($p) {
            $this->_linnaeusUser = $p;
        }

		public function setLinnaeusPassword ($p) {
            $this->_linnaeusPassword = $p;
        }

        public function setLinnaeusProtocol ($p) {
            $this->_linnaeusProtocol = $p;
        }

        public function setIpRanges ($p) {
            $this->_ipRanges = (array)$p;
        }

        public function addIpRange ($p) {
            $this->_ipRanges[] = $p;
        }

	    public function setLinnaeusServers ($p) {
            $this->_linnaeusServers = (array)$p;
        }

        public function addLinnaeusServer ($p) {
            $this->_linnaeusServers[] = $p;
        }

        public function setCsvPath ($p) {
            $this->_csvPath = $p;
        }

        public function setCsvFile ($p) {
            $this->_csvFile = $p;
        }

        public function setGitUser ($p) {
            $this->_gitUser = $p;
        }

	    public function setGitPassword ($p) {
            $this->_gitPassword = $p;
        }

	    private function openFp () {
            if (!$this->_fp) {
                $this->_fp = fopen($this->_csvFile, 'w') or
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

            if (!empty($this->_linnaeusServers)) {
                foreach (array_unique($this->_linnaeusServers) as $server) {
                    $this->setLinnaeusUrl(array('server' => $server));
                    $this->exportData();
                }
            }

            if (!empty($this->_ipRanges)) {
                foreach ($this->_ipRanges as $range) {
                    echo "Parsing server range $range\n";
                    for ($ip = 1; $ip <= 255; $ip++) {
                        $this->setLinnaeusUrl(array(
                            'server' => $range,
                            'ip' => $ip
                        ));
                        $this->exportData();
                    }
                }
            }

            $this->closeFp();
            echo "\nData exported to csv file " . $this->_csvFile . "\n\n";
        }

        private function exportData () {
            $this->setCurlResult(array(
                'user' => $this->_linnaeusUser,
                'password' => $this->_linnaeusPassword,
                'url' => $this->_linnaeusUrl
            ));
            if ($this->_curlResult) {
                echo "Exporting data from Linnaeus server " . $this->_server . "\n";
                $this->writeToCsv();
            } else {
                echo "Skipped $this->_server: not a Linnaeus server\n";
            }
        }

        private function setLinnaeusUrl ($p) {
            $server = isset($p['server']) ? $p['server'] : false;
            $ip = isset($p['ip']) ? $p['ip'] : null;

            if (!$server) return false;

            $this->_server = $ip ? str_replace('*', $ip, $server) : $server;
            $this->_linnaeusUrl = $this->_linnaeusProtocol . '://' . $this->_server .
                '/' . $this->_linnaeusWebservice;
        }

        private function setCurlResult ($p) {
            $user = isset($p['user']) ? $p['user'] : false;
            $password = isset($p['password']) ? $p['password'] : false;
            $url = isset($p['url']) ? $p['url'] : false;
            $timeout = isset($p['timeout']) ? $p['timeout'] : 2;

            if (!$user || !$password || !$url) return false;

            $this->_curlResult = false;
            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_RETURNTRANSFER => 1,
                CURLOPT_URL => $url,
                CURLOPT_USERPWD => "$user:$password",
                CURLOPT_CONNECTTIMEOUT => $timeout,
                CURLOPT_USERAGENT => 'Linnaeus'
            ));
            $this->_curlResult = curl_exec($curl);
            curl_close($curl);
        }

        private function getLastestGitHash ($branch) {
            if (!$this->_gitUser || !$this->_gitPassword) {
                return null;
            }
            $this->setCurlResult(array(
                'user' => $this->_gitUser,
                'password' => $this->_gitPassword,
                'url' => sprintf($this->_gitUrl, $branch),
                'timeout' => 5
            ));
            $data = json_decode($this->_curlResult);
            if (isset($data->object->sha)) {
                return $data->object->sha;
            }
            return null;
        }

        private function writeToCsv () {
            $r = json_decode($this->_curlResult, true);
            if ($r && isset($r['results'][0]['git_branch'])) {
                $lastestHash = $this->getLastestGitHash($r['results'][0]['git_branch']);
                $data = $r['results'];
                foreach ($data as $row) {
                    $row['lastest_git_hash'] = $lastestHash;
                    $row['code_up_to_date'] =
                        ($row['git_hash'] == $lastestHash) ? 1 : 0;
                    $row['server_ip'] = $this->_server;
                    $row['server_name'] = gethostbyaddr($this->_server);
                    $row['check_date'] = date("Y-m-d H:m:s");
                    // Write header?
                    if (!$this->_csvHeader) {
                        fputcsv($this->_fp, array_keys($row));
                        $this->_csvHeader = true;
                    }
                    fputcsv($this->_fp, array_values($row));
                }
            }
        }
	}

	$ls = new LinnaeusServerInfo();
	$ls->setLinnaeusUser('sysadmin');
	$ls->setLinnaeusPassword('sysadmin');
	$ls->setLinnaeusServers(array(
        '145.136.240.186',
        '145.136.240.185',
        '145.136.240.187'
	));
	$ls->addLinnaeusServer('145.136.240.189');
	// Alternatively it may be easier to do:
	// $ls->addIpRange('http://145.136.240.*');
	$ls->setGitUser('ruud-altenburg');
	$ls->setGitPassword('password');
	$ls->setCsvFile(dirname(__FILE__) . '/output/linnaeus_servers.csv');
	$ls->run();


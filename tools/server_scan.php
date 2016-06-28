<?php

	class LinnaeusServerScan
	{
	    private $_linnaeusWebservice = 'linnaeus_ng/admin/views/webservices/scan_servers.php';
        private $_linnaeusWebserviceKey = 'gNXhIb4LDKrA7MQmNo7wpV';
        private $_linnaeusUrl;
        private $_server;
        private $_linnaeusProtocol = 'http';

        private $_fp;
        private $_curlResult;
        private $_csvFile = 'linnaeus_servers.csv';
        private $_csvHeader = false;

        private $_linnaeusServers = array();
	    private $_ipRanges = array();

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
                '/' . $this->_linnaeusWebservice . '?key=' . $this->_linnaeusWebserviceKey;
        }

        private function setCurlResult ($p) {
            $user = isset($p['user']) ? $p['user'] : false;
            $password = isset($p['password']) ? $p['password'] : false;
            $url = isset($p['url']) ? $p['url'] : false;
            $timeout = isset($p['timeout']) ? $p['timeout'] : 2;

            if (!$url) return false;

            $this->_curlResult = false;
            $curl = curl_init();
            $options = array(
                CURLOPT_RETURNTRANSFER => 1,
                CURLOPT_URL => $url,
                CURLOPT_CONNECTTIMEOUT => $timeout,
                CURLOPT_USERAGENT => 'Linnaeus'
            );
            if ($user && $password) {
                $options[CURLOPT_USERPWD] = "$user:$password";
            }
            curl_setopt_array($curl, $options);
            $this->_curlResult = curl_exec($curl);
            curl_close($curl);
        }

        private function writeToCsv () {
            $r = json_decode($this->_curlResult, true);
            if ($r && isset($r['results'][0]['git_branch'])) {
                $data = $r['results'];
                foreach ($data as $row) {
                    $row['server_ip'] = $this->_server;
                    $row['server_name'] = gethostbyaddr($this->_server);
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

	$ls = new LinnaeusServerScan();
	$ls->setLinnaeusServers(array(
        '145.136.240.186',
        '145.136.240.185',
        '145.136.240.187'
	));
	$ls->addLinnaeusServer('145.136.240.192');
	// Alternatively it may be easier to do:
	// $ls->addIpRange('http://145.136.240.*');
	$ls->setCsvFile(dirname(__FILE__) . '/output/linnaeus_servers.csv');
	$ls->run();


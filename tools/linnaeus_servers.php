<?php

	class LinnaeusServerInfo
	{
        private $_linnaeusUser = 'sysadmin';
        private $_linnaeusPassword;
        private $_linnaeusWebservice = 'linnaeus_ng/admin/views/webservices/im.php';
        private $_linnaeusUrl;
        private $_linnaeusProtocol = 'http';

        private $_curlResult;
        private $_outputFile = 'output/linnaeus_servers.csv';

	    private $_ipRanges = array('145.136.240.*');

        private $_gitUrl = 'https://api.github.com/repos/naturalis/linnaeus_ng/git/refs/heads/im';
        private $_gitUser;
        private $_gitPassword;


        // curl -u "ruud-altenburg:ydlad>S2" https://api.github.com/repos/naturalis/linnaeus_ng/git/refs/heads/im


	    public function setLinnaeusUser ($p) {
            $this->_linnaeusUser = array($p);
        }

		public function setLinnaeusPassword ($p) {
            $this->_linnaeusPassword = array($p);
        }

        public function setLinnaeusProtocol ($p) {
            $this->_linnaeusProtocol = $p;
        }

        public function setIpRange ($p) {
            $this->_ipRange = array($p);
        }

        public function addIpRange ($p) {
            $this->_ipRange[] = $p;
        }

        public function setOutputFile ($p) {
            $this->_outputFile = $p;
        }

        public function setGitUser ($p) {
            $this->_gitUser = $p;
        }

	    public function setGitPassword ($p) {
            $this->_gitPassword = $p;
        }

        public function run () {
            foreach ($this->_ipRanges as $server) {
                for ($ip = 1; $ip <= 255; $ip++) {
                    $this->setLinnaeusUrl(array(
                            'server' => $server,
                            'ip' => $ip
                        ));
                    $this->setCurlResult(array(
                        'user' => $this->_linnaeusUser,
                        'password' => $this->_linnaeusPassword,
                        'url' => $this->_linnaeusUrl
                    ));
                    echo "$this->_linnaeusUrl\n" .
                    print_r($this->_curlResult);
                    echo "\n\n";
                }
            }
        }

        private function setLinnaeusUrl ($p) {
            $server = $p['server'] ? $p['server'] : false;
            $ip = $p['ip'] ? $p['ip'] : false;
            if (!$server || !$ip) return false;

            $this->_linnaeusUrl = $this->_linnaeusCurl = $this->_linnaeusProtocol . '://' .
                str_replace('*', $ip, $server) . '/' . $this->_linnaeusWebservice;
        }

        private function setCurlResult ($p) {
            $user = $p['user'] ? $p['user'] : false;
            $password = $p['password'] ? $p['password'] : false;
            $url = $p['url'] ? $p['url'] : false;
            if (!$user || !$password || !$url) return false;

            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_RETURNTRANSFER => 1,
                CURLOPT_URL => $url,
                CURLOPT_USERPWD => "$user:$password"
            ));
            $this->_curlResult = curl_exec($curl);
            curl_close($curl);
        }




	}

	$ls = new LinnaeusServerInfo();
	$ls->run();
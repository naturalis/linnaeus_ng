<?php

    class LinnaeusDataPush
    {
		private $config;
	    private $mysqli;
	    private $tablePrefix;
	    private $data = array();
	    private $pushUrl;
        private $timeout = 15;
        private $pushResult;
        private $gitRepo;
        private $gitBranch;
        private $gitHash;
        private $gitLatestHash;
        private $server;
        private $showPushedData = false;

        public function __construct () {
            $this->getConfig();
            $this->connectDb();
            $this->getGitInfo();
            $this->getServerInfo();
        }

		public function run () {
            $this->bootstrap();
		    $this->getProjectsWithUsers();
            $this->setData();
            $this->pushData();
            $this->printResult();
		}

		public function setPushUrl ($url)
		{
            $this->pushUrl = $url;
		}

    	public function setTimeout ($timeout)
		{
            $this->timeout = $timeout;
		}

		public function showPushedData ()
		{
            $this->showPushedData = true;
		}

		private function bootstrap ()
		{
            if (empty($this->pushUrl)) {
                die("Push url not set");
            }
		    if (empty($this->gitBranch)) {
                die("Git branch not set");
            }
			if (empty($this->gitHash)) {
                die("Git hash not set");
            }
			if (empty($this->gitLatestHash)) {
                die("Git latest hash not set");
            }
		}

		private function getGitInfo ()
		{
            $c = $this->config->getGeneralSettings();
            $path = isset($c['applicationFileRoot']) ? $c['applicationFileRoot'] :
                '/var/www/linnaeusng';

            // First cd to Linnaeus root!
            exec('cd ' . str_replace(" ", "\\ ", $path));

            exec('git rev-parse --abbrev-ref HEAD', $branch) or die("Git branch not set\n");
		    $this->gitBranch = $branch[0];

            exec('git rev-parse HEAD', $hash) or die("Git hash not set\n");
		    $this->gitHash = $hash[0];

		    exec('git rev-parse origin/' . $this->gitBranch, $latestHash);
		    $this->gitLatestHash = $latestHash[0];
		}

		private function getServerInfo ()
		{
            exec('facter --json', $server) or
                die("Cannot retrieve server info\n");
            $this->server = json_decode(implode("\n", $server));
		}

		private function pushData ()
		{
    		$post = http_build_query(array('lng_data' => json_encode($this->data)));

    		$ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $this->pushUrl);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HEADER, false);
            curl_setopt($ch, CURLOPT_POST, true);
			curl_setopt($ch, CURLOPT_HTTPHEADER, array('Expect:'));
            curl_setopt($ch, CURLOPT_POSTFIELDS, $post);

    		if ($this->timeout) {
    		    curl_setopt($ch, CURLOPT_TIMEOUT, $this->timeout);
    		}

    		$result = curl_exec($ch);
    		curl_close($ch);

    		$output = json_decode($result);
    		$this->pushResult = !is_null($output) ? $output : $result;
		}

		private function setData ()
		{
        	foreach ($this->data as $i => $row) {
                $this->data[$i]['git_branch'] = $this->gitBranch;
                $this->data[$i]['git_hash'] = $this->gitHash;
                $this->data[$i]['git_latest_hash'] = $this->gitLatestHash;
    	        $this->data[$i]['project_is_published'] =
                    ($this->data[$i]['project_is_published'] == 1) ? 'yes' : 'no';
                $this->data[$i]['user_is_active'] =
                    ($this->data[$i]['user_is_active'] == 1) ? 'yes' : 'no';
                $this->data[$i]['code_up_to_date'] =
                    ($this->data[$i]['git_hash'] == $this->data[$i]['git_latest_hash']) ? 'yes' : 'no';
                $this->data[$i]['server_ip'] = $this->setServerIp();
                $this->data[$i]['server_name'] = $this->setServerName();
                $this->data[$i]['check_date'] = date("Y-m-d H:m:s");
        	}
		}

		private function getConfig ()
		{
            require_once dirname(__FILE__) . '/../../configuration/admin/configuration.php';
            $this->config = new configuration();
		}

		private function setServerIp ()
		{
            // Test server; production does not have public address
            if (isset($this->server->ec2_public_ipv4)) {
                return $this->server->ec2_public_ipv4;
            }
		    // Production server
            if (isset($this->server->ec2_local_ipv4)) {
                return $this->server->ec2_local_ipv4;
            }
            return '** Check cron script! **';
		}

		private function setServerName ()
		{
            return isset($this->server->hostname) ? $this->server->hostname :
                '** Check cron script! **';
		}

		private function printResult ()
		{
		    if (isset($this->pushResult->result)) {
		        $message = $this->pushResult->result;
		    } else if (!empty($this->pushResult)) {
                $message = is_array($this->pushResult) ? json_encode($this->pushResult) : $this->pushResult;
		    } else {
                $message = 'Could not connect to ' . $this->pushUrl . '!';
		    }

		    if (isset($message) && $this->showPushedData) {
                echo $message . "; data pushed:\n";
                print_r($this->data);
                die("\n");
		    }
		    die($message . "\n");
		}

        private function getProjectsWithUsers ()
        {
            $query = '
                select
                    t1.sys_name as project,
                    t1.published as project_is_published,
                    t3.username as user_name,
                    t3.first_name,
                    t3.last_name,
                    t4.role as role,
                    t3.email_address,
                    t3.active as user_is_active,
                    t3.last_login,
                    t2.last_project_select as project_last_selected,
                    t3.last_password_change as password_last_changed
                from
                    ' . $this->tablePrefix . 'projects as t1
                left join
                    ' . $this->tablePrefix . 'projects_roles_users as t2 on t1.id = t2.project_id
                left join
                    ' . $this->tablePrefix . 'users as t3 on t2.user_id = t3.id
                left join
                    ' . $this->tablePrefix . 'roles as t4 on t2.role_id = t4.id
                order by
                    t1.sys_name, t3.username';

    		$r = $this->mysqli->query($query);

    		if ($r->mysqli_num_rows() == 0) {
    		    die("Nothing to report: no projects/users created yet!\n");
    		}

			while ($row = $r->fetch_assoc()) {
				$this->data[] = $row;
			}
        }

    	private function connectDb()
		{
			$c = $this->config->getDatabaseSettings();
			$this->tablePrefix = $c['tablePrefix'];

		    $this->mysqli = new mysqli($c['host'], $c['user'], $c['password'], $c['database']);

			if ($this->mysqli->connect_error) {
				throw new Exception($this->mysqli->connect_error . "\n");
			}

			$this->mysqli->query('SET NAMES ' . $c['characterSet']);
			$this->mysqli->query('SET CHARACTER SET ' . $c['characterSet']);
		}
    }


    $ldp = new LinnaeusDataPush();
    $ldp->setPushUrl('http://linnaeus.naturalis.nl/admin/server_csv.php');
    //$ldp->showPushedData();
    $ldp->run();

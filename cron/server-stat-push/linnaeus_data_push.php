<?php

    class LinnaeusDataPush
    {

	    private $config;
	    private $mysqli;
	    private $data = array();
	    private $pushUrl;
        private $timeout;
        private $pushResult;

        public function __construct () {
            $this->getConfig();
            $this->connectDb();
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

		private function bootstrap ()
		{
            if (empty($this->pushUrl)) {
                throw new Exception("Push url not set\n");
            }
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
		    exec('git rev-parse --abbrev-ref HEAD', $branch);
            exec('git rev-parse HEAD', $hash);
            exec('git rev-parse origin/' . $branch[0], $latestHash);

        	foreach ($this->data as $i => $row) {
                $this->data[$i]['git_branch'] = $branch[0];
                $this->data[$i]['git_hash'] = $hash[0];
                $this->data[$i]['git_latest_hash'] = $latestHash[0];
    	        $this->data[$i]['project_is_published'] =
                    ($this->data[$i]['project_is_published'] == 1) ? 'yes' : 'no';
                $this->data[$i]['user_is_active'] =
                    ($this->data[$i]['user_is_active'] == 1) ? 'yes' : 'no';
                $this->data[$i]['code_up_to_date'] =
                    ($this->data[$i]['git_hash'] == $this->data[$i]['git_latest_hash']) ? 'yes' : 'no';
                $this->data[$i]['server_ip'] = $_SERVER['SERVER_ADDR'];
                $this->data[$i]['server_name'] = gethostbyaddr($this->data[$i]['server_ip']);
                $this->data[$i]['check_date'] = date("Y-m-d H:m:s");
        	}
		}

		private function getConfig ()
		{
            require_once dirname(__FILE__) . '/../../configuration/admin/configuration.php';
            $this->config = new configuration();
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
                    ' . $this->mysqli->prefix . 'projects as t1
                left join
                    ' . $this->mysqli->prefix . 'projects_roles_users as t2 on t1.id = t2.project_id
                left join
                    ' . $this->mysqli->prefix . 'users as t3 on t2.user_id = t3.id
                left join
                    ' . $this->mysqli->prefix . 'roles as t4 on t2.role_id = t4.id
                order by
                    t1.sys_name, t3.username';

    		$r = $this->mysqli->query($query);

			while ($row = $r->fetch_assoc()) {
				$this->data[] = $row;
			}
        }

    	private function connectDb()
		{
			$data = $this->config->getDatabaseSettings();

		    try {
				$this->mysqli = new stdClass();
				$this->mysqli->user = $data['user'];
				$this->mysqli->password = $data['password'];
				$this->mysqli->host = $data['host'];
				$this->mysqli->database = $data['database'];
				$this->mysqli->prefix = $data['tablePrefix'];
				$this->mysqli->character_set = $data['characterSet'];
			} catch (Exception $e) {
			    echo $e->getMessage($e) . "\n";
			}

		    $this->mysqli = new mysqli(
				$this->mysqli->host,
				$this->mysqli->user,
				$this->mysqli->password,
				$this->mysqli->database
			);

			if ($this->mysqli->connect_error) {
				throw new Exception($this->mysqli->connect_error . "\n");
			}

			$this->mysqli->query('SET NAMES ' . $this->mysqli->character_set);
			$this->mysqli->query('SET CHARACTER SET ' . $this->mysqli->character_set);
		}
    }


    $ldp = new LinnaeusDataPush();
    $ldp->setPushUrl('http://linnaeus.naturalis.nl/admin/server_csv.php');
    $ldp->run();

<?php

class DB {

	static function getInstance()
	{
		$config=new configuration;
        $databaseSettings = $config->getDatabaseSettings();

        $databaseConnection = mysqli_connect($databaseSettings['host'],$databaseSettings['user'], $databaseSettings['password']);
        if (!$databaseConnection)
		{
			$this->log('Failed to connect to database ' . $this->databaseSettings['host'] .
                ' with user ' . $this->databaseSettings['user'],2);
			return false;
		}

        mysqli_select_db($databaseConnection, $databaseSettings['database']) or
            $this->log('Failed to select database '.$databaseSettings['database'],2);

        if ($databaseSettings['characterSet'])
		{
            mysqli_query($databaseConnection,
                'SET NAMES ' . $databaseSettings['characterSet']);
            mysqli_query($databaseConnection,
                'SET CHARACTER SET ' . $databaseSettings['characterSet']);
        }

        return $databaseConnection;
	}
	
}

$global_DB_connection = DB::getInstance();

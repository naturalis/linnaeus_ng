<?php

	class ExternalService {

		private $sett;
		private $conn;
		private $params;

		private $results;
		private $output;
		private $query;
		private $label;

		const PID = 401;
		const TRAIT_ID = 1;

		public function setDatabaseSettings( $dbSettings )
		{
			// [ host, user, password, database, tablePrefix, characterSet ]
			$this->sett = (object)$dbSettings;
		}

		public function setParameters( $params )
		{
			$this->params = (object)$params;
		}

		public function main()
		{
			$this->dbConnect();
			$this->getNSRID();
			$this->dbDisconnect();
		}

		public function getOutput( $jsonEncode=true )		
		{
			return $this->output;
		}

		private function  dbConnect()
		{
			$this->conn = new mysqli( $this->sett->host,$this->sett->user,$this->sett->password,$this->sett->database );		
			$this->conn->set_charset( $this->sett->characterSet );
		}

		private function dbDisconnect()
		{
			$this->conn->close();
		}

		private function getNSRID()
		{

			if (!isset($this->params->taxon_id)) return;

			$query="
				select
					string_value
				from 
					" . $this->sett->tablePrefix . "traits_taxon_freevalues
				where
					project_id =  " . $this::PID . "
					and trait_id =  " . $this::TRAIT_ID . "
					and taxon_id =  " . $this->params->taxon_id . "
				";

			if ($result = $this->conn->query($query))
			{
			    $row = (object)$result->fetch_assoc();
			}

			$this->output = isset($row->string_value) ? $row->string_value : null;
		}

	}



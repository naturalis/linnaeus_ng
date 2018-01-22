<?php

	include_once("/var/www/linnaeusng/configuration/admin/constants.php");
	include_once("/var/www/linnaeusng/configuration/admin/configuration.php");
	
	class taxonParentageUpdater
	{
		private $connector;
		private $mysqli;
		private $executionTimeOut=600; //sec
		private $treetop;
		private $tmp=array();
		private $number_updated=0;
		private $number_failed=0;

		public function __construct()
		{
		}
		
		public function __destruct()
		{
		}

		public function go()
		{
			try
			{
				$this->printHeader();
				$this->checkEssentials();
				$this->connectDatabase();
				$this->updateParentage();
				$this->printStats();
				$this->cleanUp();
			} 
			catch (Exception $e)
			{
				$this->handleException($e);
			}
		}
		
		public function setConnectData( $data )
		{
			try
			{
				$this->connector = new stdClass();
				$this->connector->user = $data['user'];
				$this->connector->password = $data['password'];
				$this->connector->host = $data['host'];
				$this->connector->database = $data['database'];
				$this->connector->prefix = $data['tablePrefix'];
				$this->connector->project_id = $data['project_id'];
			} 
			catch (Exception $e)
			{
				$this->handleException( $e );
			}
		}
		
		private function feedback( $m )
		{
			echo $m,"\n";
		}

		private function handleException( $e )
		{
			$this->feedback( "\nERROR" );
			$this->feedback( $e->getMessage() );
			$this->feedback( "abnormal termination" );
		}

		private function printHeader()
		{
			$this->feedback( "running taxon parentage updater" );
			$this->feedback(  date(DATE_RFC2822) );
		}

		private function checkEssentials()
		{
			if ( empty($this->connector->user) ) {
                $b[] = "missing database user";
            }
			if ( empty($this->connector->host) ) {
                $b[] = "missing database host";
            }
			if ( empty($this->connector->database) ) {
                $b[] = "missing database name";
            }
			if ( empty($this->connector->project_id) ) {
                $b[] = "missing project id";
            }

			if ( !empty( $b ) ) {
                throw new Exception(implode("\n", $b));
            }
		}

		private function connectDatabase()
		{
			$this->mysqli = @new mysqli(
				$this->connector->host,
				$this->connector->user,
				$this->connector->password,
				$this->connector->database
			);
			
			if ($this->mysqli->connect_error)
			{
				throw new Exception( $this->mysqli->connect_error );
			}
			else
			{
				$this->feedback( "connected " . $this->connector->database . "@" . $this->connector->host );
			}
		}

		private function padId($id)
		{
			return sprintf('%05s',$id);
		}
		
		private function treeGetTop()
		{
			/*
				get the top taxon = no parent
				"_r.id < 10" added as there might be orphans, which are ususally low-level ranks 
			*/
			$query="
				select
					_a.id,
					_a.taxon,
					_r.rank
				from
					".$this->connector->prefix."taxa _a
						
				left join ".$this->connector->prefix."projects_ranks _p
					on _a.project_id=_p.project_id
					and _a.rank_id=_p.id
	
				left join ".$this->connector->prefix."ranks _r
					on _p.rank_id=_r.id
	
				where 
					_a.project_id = ".$this->connector->project_id."
					and _a.parent_id is null
					and _r.id < 10
			";
			
			$result=$this->mysqli->query( $query );

			$rows=array();
			
			while($row=$result->fetch_assoc())
			{
				if ( !empty($row['id']) ) {
                    $rows[] = $row;
                }
			}

			$result->close();
			
			$this->treetop=null;
		
			if (count((array)$rows)==1)
			{
				$this->treetop=$rows[0]['id'];
			}

			if (count((array)$rows)>1)
			{
				throw new Exception( 'Detected multiple higher taxa without a parent. Unable to determine which is the treetop.' );
			}
		}

		private function getProgeny( $p=null )
		{
			$parent = isset( $p['parent'] ) ? $p['parent'] : $this->treetop;
			$level = isset( $p['level'] ) ? $p['level'] : 0;
			$family = isset( $p['family'] ) ? $p['family'] : array();
			
			$family[] = $this->padId( $parent );
			
			$query="
				select
					id,
					parent_id,
					taxon,
					".$level." as level

				from
					".$this->connector->prefix."taxa
						
				where 
					project_id = ".$this->connector->project_id."
					and parent_id = ".$parent."
			";
			
			$result=$this->mysqli->query( $query );

			while( $row=$result->fetch_assoc() )
			{
				$row['parentage']=$family;
				$this->tmp[]=$row;
				$this->getProgeny( array('parent'=>$row['id'],'level'=>$level+1,'family'=>$family) );
			}
		}
	
		private function updateParentage()
		{
			set_time_limit($this->executionTimeOut);

			$this->treeGetTop();
	
			if ( empty($this->treetop) )
			{
				throw new Exception( 'Found no treetop.' );
			}

			$this->getProgeny();

			$query="delete from ".$this->connector->prefix."taxon_quick_parentage where project_id = ".$this->connector->project_id;

			$this->mysqli->query( $query );

			foreach((array)$this->tmp as $key=>$val)
			{
				$query="
					insert into ".$this->connector->prefix."taxon_quick_parentage 
						(id,project_id,taxon_id,parentage)
					values
						(null,".$this->connector->project_id.",".$val['id'].",'".implode(' ',$val['parentage'])."')";
				
				if ( $this->mysqli->query( $query ) ) {
                    $this->number_updated++;
                }
				else
				{
					$this->number_failed++;
                }
			}
		}

		private function printStats()
		{
			$this->feedback( sprintf("wrote parentage for %s taxa",$this->number_updated ) );
			$this->feedback( sprintf("failed %s ",$this->number_failed ) );
		}
		
		private function cleanUp()
		{
			$this->feedback( "closing database connection" );
			$this->mysqli->close();
			$this->feedback( "finished" . "\n" );
		}
		
	}

	$c=new configuration;
	$conn=$c->getDatabaseSettings();
	$conn['project_id']=1;

	$b = new taxonParentageUpdater;
	$b->setConnectData( $conn );
	$b->go();




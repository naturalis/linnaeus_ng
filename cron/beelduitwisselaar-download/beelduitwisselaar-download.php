<?php

	/*

	requires CURL
	
	cron as root
	script+keyfile in /usr/sbin/

	* / 5 * * * * /usr/bin/php /usr/sbin/beelduitwisselaar-download/beelduitwisselaar-download.php >> /usr/bin/php /usr/sbin/beelduitwisselaar-download/beelduitwisselaar-download.log


	development:	http://162.13.81.40/webservice/newimages
	productie:		http://95.138.190.198/webservice/newimages

	drop table beelduitwisselaar_batches;
	create table beelduitwisselaar_batches (
		`id` int(11) not null primary key auto_increment,
		`project_id` int(11) not null,
		`created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
		`number_in_feed` int(5) not null default 0,
		`number_downloaded` int(5) not null default 0,
		`number_moved` int(5) not null default 0,
		`number_saved` int(5) not null default 0
	);

	parse json per image:
		[filename] => mug.jpg
		[url] => http://162.13.81.40/sites/default/files/mug.jpg
		[nsrId] => 0DA12C0D482E
		[datePhoto] => 2015-03-13 00:00:00
		[locationPhoto] => Slootje, Lange voorhoud, Den Haag, Zuid Holland
		[description] => dit id eem opmerkiing
		[dateCreated] => 2015-03-13 14:26:47
		[photographer] => fotograaf1 fotograaf1
		[photographerContact] => roy.kleukers@naturalis.nl
		[copyrightPhoto] => CC BY-NC-SA (Naamsvermelding - Niet Commercieel - Gelijk Delen)
		[validator] => expert1

	*/
	
	
	class beeldbankDownloader
	{
		private $isTestRun=false;
		private $printParameters=false;

		private $tmpDir;
		private $connector;
		private $mysqli;
		private $webserviceUrl;
		private $newImages=array();
		private $existingImage;
		private $taxonId;
		private $existingTaxon;

		private $fetchLimit=0;
		private $fetchFromDate='1970-01-01 00:00:01';
		private $fetchFromDateOverride=null;
		private $metaDataLanguage=24; // default dutch

		private $scpShellCommand="scp -i %s %s %s@%s:\"'%s%s'\"";
		private $scpRemoteUser='imageupload';
		private $scpRemoteUserPubKeyFile;
		private $scpRemoteAddress='134.213.60.149';
		private $scpRemoteBasePath='/var/www/imageserver/original/';
		private $scpRemoteFolder='';

		private $number_in_feed=0;
		private $number_downloaded=0;
		private $number_moved=0;
		private $number_saved=0;


		public function __construct()
		{
			$this->tmpDir=sys_get_temp_dir();
		}
		
		public function __destruct()
		{
		}

		public function go()
		{
			try
			{
				$this->printHeader();
				$this->printRunParameters();
				$this->checkEssentials();
				$this->connectDatabase();
				$this->setFetchFromDate();
				$this->parametrizeWebserviceUrl();
				$this->readImageFeed();
				$this->preProcessImages();
				$this->downloadImages();
				$this->moveImages();
				$this->saveImageData();
				$this->writeToNSRLog();
				$this->writeToLogTable();
				$this->printStats();
				$this->cleanUp();
				//print_r( $this->newImages );
			} 
			catch (Exception $e)
			{
				$this->handleException($e);
			}
		}
		
		public function setPrintParameters( $state )
		{
			if ( is_bool($state) )
				$this->printParameters = $state;
		}

		public function setIsTestRun( $state )
		{
			if ( is_bool($state) )
				$this->isTestRun = $state;
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
				$this->connector->prefix = $data['prefix'];
				$this->connector->project_id = $data['project_id'];
			} 
			catch (Exception $e)
			{
				$this->handleException( $e );
			}
		}

		public function setWebserviceUrl( $url )
		{
			$this->webserviceUrl = $url;
		}

		public function setFetchLimit( $limit )
		{
			$this->fetchLimit = $limit;
		}

		public function setMetaDataLanguage( $code )
		{
			$this->metaDataLanguage = $code;
		}

		public function setScpRemoteUserPubKeyFile( $filename )
		{
			$this->scpRemoteUserPubKeyFile = $filename;
		}

		public function setScpRemoteUser( $user )
		{
			$this->scpRemoteUser = $user;
		}

		public function setScpRemoteAddress( $address )
		{
			$this->scpRemoteAddress = $address;
		}

		public function setScpRemoteBasePath( $path )
		{
			$this->scpRemoteBasePath = rtrim( $path, '/') . '/';
		}

		public function setScpRemoteFolder( $folder )
		{
			$this->scpRemoteFolder = trim( $folder, '/') . '/';
		}

		public function setFetchFromDateOverride( $date )
		{
			$this->fetchFromDateOverride = $date;
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
			$this->feedback( "running beeluitwisselaar images to soortenregister" );
			$this->feedback(  date(DATE_RFC2822) );
		}

		private function printRunParameters()
		{
			if (!$this->printParameters) return;

			$this->feedback( "running with parameters:" );

			$this->feedback( sprintf("  test run: %s",$this->isTestRun) );
			$this->feedback( sprintf("  connection: %s",implode(",",(array)$this->connector)) );
			$this->feedback( sprintf("  webservice url: %s",$this->webserviceUrl ) );
			$this->feedback( sprintf("  fetch limit: %s",$this->fetchLimit) );
			$this->feedback( sprintf("  fetch from date override: %s",$this->fetchFromDateOverride) );
			$this->feedback( sprintf("  scp remote user: %s",$this->scpRemoteUser) );
			$this->feedback( sprintf("  scp remote user public key file: %s",$this->scpRemoteUserPubKeyFile) );
			$this->feedback( sprintf("  scp remote address: %s",$this->scpRemoteAddress) );
			$this->feedback( sprintf("  scp remote base path: %s",$this->scpRemoteBasePath) );
			$this->feedback( sprintf("  scp remote folder: %s",$this->scpRemoteFolder) );
			$this->feedback( sprintf("  meta-data language code: %s",$this->metaDataLanguage) );
		}

		private function checkEssentials()
		{
			if ( $this->isTestRun ) $this->feedback( "performing TEST RUN: no downloads or database writes will take place" );

			if ( empty($this->connector->user) ) $b[]="missing database user";
			if ( empty($this->connector->host) ) $b[]="missing database host";
			if ( empty($this->connector->database) ) $b[]="missing database name";
			if ( empty($this->connector->project_id) ) $b[]="missing project id";
			if ( empty($this->webserviceUrl) ) $b[]="missing webservice url";
			if ( empty($this->scpRemoteUser) ) $b[]="missing remote scp-user";
			if ( empty($this->scpRemoteUserPubKeyFile) ) $b[]="missing remote scp-user public key file";
			if ( empty($this->scpRemoteAddress) ) $b[]="missing remote address";
			if ( empty($this->scpRemoteBasePath) ) $b[]="missing remote base path";
			
			if ( !file_exists($this->scpRemoteUserPubKeyFile) ) $b[]="public key file not found";

			if ( !empty( $b ) ) throw new Exception( implode("\n",$b) );
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

		private function setFetchFromDate()
		{
			if ( !is_null($this->fetchFromDateOverride) )
			{
				$this->fetchFromDate=$this->fetchFromDateOverride;
				$this->feedback( sprintf("fetch from timestamp: %s (manual override)", $this->fetchFromDate) );
			}
			else
			{
				if ( $result=$this->mysqli->query( sprintf("
					select 
						subtime(max(created),'0 1:0:0') as fetch_from,
						max(created) as latest_created 
					from 
						".$this->connector->prefix."beelduitwisselaar_batches 
					where 
						project_id = %s
				", $this->mysqli->escape_string($this->connector->project_id))))
				{
					while ( $row=$result->fetch_array() )
					{
						if ( isset( $row['latest_created'] ) )
						{
							$this->fetchFromDate=$row['fetch_from'];
							$this->feedback( "last fetch timestamp: " . $row['latest_created'] );
						}
						else
						{
							$this->feedback( "never fetched" );
						}
					}
					$result->close();
					
					$this->feedback( "fetch from timestamp: " . $this->fetchFromDate );
				}
				else
				{
					throw new Exception( $this->mysqli->error );
				}
			}
		}

		private function parametrizeWebserviceUrl()
		{
			$this->webserviceUrl=
				str_replace(
					array( "%DATE%","%LIMIT%" ) , 
					array(
						str_replace( array( ":", "-", " " ) ,"" , $this->fetchFromDate ),
						$this->fetchLimit
					),
					$this->webserviceUrl
				 );
				 
			if (filter_var($this->webserviceUrl, FILTER_VALIDATE_URL)===false)
			{
				throw new Exception( "not a valid url: " . $this->webserviceUrl );
			}
		}

		private function readImageFeed()
		{
			$this->feedback( "fetching from " . $this->webserviceUrl );
			
			$raw = @file_get_contents( $this->webserviceUrl );
			
			if ( !empty($raw) )
			{
				$json=json_decode( $raw );

				if ($json)
				{
					//print_r( $json );
					foreach((array)$json->images as $val )
					{
						array_push($this->newImages,$val);
					}
					
					$this->number_in_feed=count((array)$this->newImages);
					$this->feedback( sprintf("read image feed (%s images)", $this->number_in_feed ) );
				}
				else
				{
					throw new Exception( "got illegal JSON" );
				}
			}
			else
			{
				throw new Exception( "got no data from url" );
			}
		}

		private function resolveImageByName( $name )
		{
			$this->existingImage=null;

			$result=$this->mysqli->query(
				sprintf(
					"select * from  media_taxon  where  file_name = '%s'" ,
					$this->mysqli->escape_string( $name )
				)
			);
			
			$row=$result->fetch_assoc();

			$result->close();

			$this->existingImage=$row;
		}

		private function resolveTaxonById( $taxon_id )
		{
			$this->existingTaxon=null;
			$result=$this->mysqli->query("
				select
					_b.id,_b.taxon
				from 
					".$this->connector->prefix."nsr_ids _a
				right join ".$this->connector->prefix."taxa _b
					on _a.project_id=_b.project_id
					and _a.lng_id=_b.id
				where 
					_a.nsr_id like '%".$this->mysqli->escape_string( str_pad($taxon_id,12,'0',STR_PAD_LEFT) )."' 
					and _a.project_id = ".$this->mysqli->escape_string( $this->connector->project_id )."
					and _a.item_type = 'taxon'
			");

			$row=$result->fetch_assoc();
			$result->close();
			$this->existingTaxon=$row;
		}

		private function preProcessImages()
		{
			foreach((array)$this->newImages as $key=>$val )
			{
				$this->newImages[$key]->_status=null;
				$this->newImages[$key]->_taxon=null;

				$this->resolveImageByName( $this->scpRemoteFolder . $val->filename );
				
				if ( !empty( $this->existingImage ) )
				{
					$this->newImages[$key]->_status="file already exists (in database)";
					continue;
				}

				$this->resolveTaxonById( $val->nsrId );
				
				if ( empty( $this->existingTaxon ) )
				{
					$this->newImages[$key]->_status="unknown taxon id";
					continue;
				}
				
				$this->newImages[$key]->_taxon=$this->existingTaxon;
			}
		}

		private function remoteFileExists( $url )
		{
			$ch = curl_init();
			curl_setopt( $ch, CURLOPT_URL, str_replace( " ", "%20", $url ) );
			// don't download content
			curl_setopt( $ch, CURLOPT_NOBODY, 1 );
			curl_setopt( $ch, CURLOPT_FAILONERROR, 1 );
			curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );
			if( curl_exec( $ch )!==FALSE )
			{
				return true;
			}
			else
			{
				return false;
			}
		}

		private function remoteFileMime( $url )
		{
			$ch = curl_init( $url );
			curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
			curl_exec( $ch );
			return curl_getinfo( $ch, CURLINFO_CONTENT_TYPE );
		}

		private function downloadImages()
		{
			foreach((array)$this->newImages as $key=>$val )
			{
				if ( empty($val->_status) && !empty($val->_taxon) )
				{
					
					if ( empty($val->url) )
					{
						$this->newImages[$key]->_status="no image url";
						continue;
					}

					if ( !$this->remoteFileExists( $val->url ) )
					{
						$this->newImages[$key]->_status=sprintf( "image does not exist at url '%s'", $val->url );
						continue;
					}
					
					$bin = @file_get_contents( str_replace( " ", "%20", $val->url ) );
					
					if ( empty( $bin ) )
					{
						$this->newImages[$key]->_status="unable to download image";
						continue;
					}
					else
					{
						if ( $this->isTestRun )
						{
							$this->feedback( sprintf("TEST RUN: would download %s", $val->url )  );
							$this->number_downloaded++;
							continue;
						}
						else
						{
							$tmpfile=tempnam( $this->tmpDir, "nsr"  );
							$bytes=file_put_contents( $tmpfile, $bin );
							chmod( $tmpfile, 0666 ); // permissions of this file + umask of remote user = file permissions (0666 - 0022 = 0640)
							$mime=$this->remoteFileMime( $val->url );
	
							if ( $bytes!==false ) 
							{
								$this->newImages[$key]->_tmp_file=$tmpfile;
								$this->newImages[$key]->_saved_bytes=$bytes;
								$this->newImages[$key]->_mime=$mime;
								$this->feedback( sprintf("downloaded %s", $val->filename )  );
								$this->number_downloaded++;
								continue;
							}
							else
							{
								$this->newImages[$key]->_status="unable to save temp image";
								continue;
							}
						}
					}
				}
			}
		}

		private function moveImages()
		{
			foreach((array)$this->newImages as $key=>$val )
			{
				if ( empty($val->_status) && !empty($val->_taxon) && !empty($val->_tmp_file) )
				{
					if ( $this->isTestRun )
					{
						$this->feedback( sprintf("TEST RUN: would move %s", $val->filename )  );
						$this->number_moved++;
						continue;
					}

					// will overwrite! but we've handled that already.
					$out=shell_exec( sprintf($this->scpShellCommand,
						$this->scpRemoteUserPubKeyFile,
						$val->_tmp_file,
						$this->scpRemoteUser,
						$this->scpRemoteAddress,
						$this->scpRemoteBasePath . $this->scpRemoteFolder,
						$val->filename) . " 2>&1 " );
					
					if ( !empty($out) )
					{
						$this->newImages[$key]->_status="while moving: " . trim($out);
						continue;				
					}
					
					$this->feedback( sprintf("moved %s", $val->filename )  );
					$this->number_moved++;
				}
			}
		}

		private function saveImageMetaField( $p )
		{
			$media_id=isset($p['media_id']) ? $p['media_id'] : null;
			$sys_label=isset($p['sys_label']) ? $p['sys_label'] : null;
			$meta_data=isset($p['meta_data']) ? $p['meta_data'] : null;
			$meta_date=isset($p['meta_date']) ? $p['meta_date'] : null;
			
			$query=
				sprintf("
					insert into ".$this->connector->prefix."media_meta
						(project_id,language_id,media_id,sys_label,meta_data,meta_date,created)
					values
						(%s,".$this->metaDataLanguage.",%s,%s,%s,%s,now())",
						$this->mysqli->escape_string( $this->connector->project_id ),
						$this->mysqli->escape_string( $media_id ),
					"'".$this->mysqli->escape_string( $sys_label )."'",
						!empty($meta_data) ? "'".$this->mysqli->escape_string( $meta_data )."'" : "null",
						!empty($meta_date) ? "'".$this->mysqli->escape_string( $meta_date )."'" : "null"
				);

			if ( $result=$this->mysqli->query( $query ) )
			{
				return;
			}
			else
			{
				return "unable to save ".$sys_label." in media_meta table";
			}
		}

		private function saveImageData()
		{
			foreach((array)$this->newImages as $key=>$val )
			{
				if ( empty($val->_status) && !empty($val->_taxon) )
				{
					if ( $this->isTestRun )
					{
						$this->feedback( sprintf("TEST RUN: would save record for %s with data:", $val->filename )  );

						$this->feedback( '  taxon: ' . $val->_taxon['id'] . ' ('. $val->nsrId .')' );
						$this->feedback( '  beeldbankOmschrijving: ' . $val->description );
						$this->feedback( '  vervaardigingsDatum: ' . $val->datePhoto );
						$this->feedback( '  beeldbankDatumAanmaak: ' . $val->dateCreated );
						$this->feedback( '  beeldbankLokatie: ' . $val->locationPhoto );
						$this->feedback( '  beeldbankFotograaf: ' . $val->photographer );
						$this->feedback( '  beeldbankAdresMaker: ' . $val->photographerContact );
						$this->feedback( '  beeldbankLicentie: ' . $val->copyrightPhoto );
						$this->feedback( '  beeldbankValidator: ' . $val->validator );

						$this->number_saved++;
						continue;
					}

					$query=
						sprintf("
							insert into ".$this->connector->prefix."media_taxon
								(project_id,taxon_id,file_name,thumb_name,original_name,mime_type,file_size,overview_image,created)
							values
								(%s,%s,%s,'',%s,%s,%s,0,now())",
								$this->mysqli->escape_string( $this->connector->project_id ),
								$this->mysqli->escape_string( $val->_taxon['id'] ),
							"'".$this->mysqli->escape_string( $this->scpRemoteFolder . $val->filename )."'",
							"'".$this->mysqli->escape_string( $val->url )."'",
							"'".$this->mysqli->escape_string( $val->_mime )."'",
								$this->mysqli->escape_string( $val->_saved_bytes )
						);

					$this->feedback( sprintf("saving record for %s", $this->scpRemoteFolder . $val->filename )  );

					if ($result=$this->mysqli->query( $query ))
					{
						$this->newImages[$key]->_id = $this->mysqli->insert_id;
						$this->numberOfSavedImages++;
					}
					else
					{
						$this->newImages[$key]->_status = "unable to save record in media_taxon table";
						continue;
					}
					
					if ( !empty($val->description) ) 
					{
						$query=
							sprintf("
								insert into ".$this->connector->prefix."media_descriptions_taxon
									(project_id,language_id,media_id,description,created)
								values
									(%s,".$this->metaDataLanguage.",%s,%s,now())",
									$this->mysqli->escape_string( $this->connector->project_id ),
									$this->newImages[$key]->_id,
								"'".$this->mysqli->escape_string( $val->description )."'"
							);
							
						if ($result=$this->mysqli->query( $query ))
						{
							$r=$this->saveImageMetaField( array(
								'media_id'=>$this->newImages[$key]->_id,
								'sys_label'=>'beeldbankOmschrijving',
								'meta_data'=>$val->description
							));

							if ( !is_null($r) ) $this->newImages[$key]->_messages[]=$r;
						}
						else
						{
							$this->newImages[$key]->_messages[] = "unable to save description in media_descriptions_taxon table";
						}
					
					}

					if ( !empty($val->datePhoto) ) 
					{
						$r=$this->saveImageMetaField( array(
							'media_id'=>$this->newImages[$key]->_id,
							'sys_label'=>'vervaardigingsDatum',
							'meta_date'=>$val->datePhoto
						));
	
						if ( !is_null($r) ) $this->newImages[$key]->_messages[]=$r;
					}

					if ( !empty($val->dateCreated) ) 
					{
						$r=$this->saveImageMetaField( array(
							'media_id'=>$this->newImages[$key]->_id,
							'sys_label'=>'beeldbankDatumAanmaak',
							'meta_date'=>$val->dateCreated
						));
	
						if ( !is_null($r) ) $this->newImages[$key]->_messages[]=$r;
					}

					if ( !empty($val->locationPhoto) ) 
					{
						$r=$this->saveImageMetaField( array(
							'media_id'=>$this->newImages[$key]->_id,
							'sys_label'=>'beeldbankLokatie',
							'meta_data'=>$val->locationPhoto
						));
	
						if ( !is_null($r) ) $this->newImages[$key]->_messages[]=$r;
					}

					if ( !empty($val->photographer) ) 
					{
						$r=$this->saveImageMetaField( array(
							'media_id'=>$this->newImages[$key]->_id,
							'sys_label'=>'beeldbankFotograaf',
							'meta_data'=>$val->photographer
						));
	
						if ( !is_null($r) ) $this->newImages[$key]->_messages[]=$r;
					}

					if ( !empty($val->photographerContact) ) 
					{
						$r=$this->saveImageMetaField( array(
							'media_id'=>$this->newImages[$key]->_id,
							'sys_label'=>'beeldbankAdresMaker',
							'meta_data'=>$val->photographerContact
						));
	
						if ( !is_null($r) ) $this->newImages[$key]->_messages[]=$r;
					}

					if ( !empty($val->copyrightPhoto) ) 
					{
						$r=$this->saveImageMetaField( array(
							'media_id'=>$this->newImages[$key]->_id,
							'sys_label'=>'beeldbankLicentie',
							'meta_data'=>$val->copyrightPhoto
						));
	
						if ( !is_null($r) ) $this->newImages[$key]->_messages[]=$r;
					}

					if ( !empty($val->validator) ) 
					{
						$r=$this->saveImageMetaField( array(
							'media_id'=>$this->newImages[$key]->_id,
							'sys_label'=>'beeldbankValidator',
							'meta_data'=>$val->validator
						));
	
						if ( !is_null($r) ) $this->newImages[$key]->_messages[]=$r;
					}
					
					//$this->newImages[$key]->_status="saved"; // _status reserved for errors
					$this->number_saved++;
				}
			}
		}

		private function writeToNSRLog()
		{
			if ( $this->isTestRun )
			{
				$this->feedback( "TEST RUN: skipping writing to NSR activity log"  );
				return;
			}			
			
			foreach((array)$this->newImages as $key=>$val )
			{
				if ( $val->_status=="saved" )
				{
					$d=array( "image"=>$val->filename, "taxon"=>$val->_taxon["taxon"] );
					
					if ( !empty($val->description) ) $d['description']=$val->description;
					if ( !empty($val->datePhoto) )  $d['datePhoto']=$val->datePhoto;
					if ( !empty($val->dateCreated) )  $d['dateCreated']=$val->dateCreated;
					if ( !empty($val->locationPhoto) )  $d['locationPhoto']=$val->locationPhoto;
					if ( !empty($val->photographer) )  $d['photographer']=$val->photographer;
					if ( !empty($val->photographerContact) )  $d['photographerContact']=$val->photographerContact;
					if ( !empty($val->copyrightPhoto) )  $d['license']=$val->copyrightPhoto;
					if ( !empty($val->validator) )  $d['validator']=$val->validator;
					
					$query=
						sprintf("
							insert into ".$this->connector->prefix."activity_log
								(project_id,user,controller,view,note,data_after,created,last_change)
							values
								(%s,%s,%s,%s,%s,%s,now(),now())",
								$this->mysqli->escape_string( $this->connector->project_id ),
								"'server [automated process]'",
								"'".dirname(__FILE__)."'",
								"'".basename(__FILE__)."'",
								"'added new image from beelduitwisselaar'",
								"'".serialize( $d )."'"
						);
						
					$this->mysqli->query( $query );
				}
			}
		}

		private function writeToLogTable()
		{
			if ( $this->isTestRun )
			{
				$this->feedback( "TEST RUN: skipping writing to download batch log"  );
				return;
			}			

			$query=
				sprintf("
					insert into ".$this->connector->prefix."beelduitwisselaar_batches
						(project_id,created,number_in_feed,number_downloaded,number_moved,number_saved)
					values
						(%s,now(),%s,%s,%s,%s)",
						$this->mysqli->escape_string( $this->connector->project_id ),
						$this->mysqli->escape_string( $this->number_in_feed ),
						$this->mysqli->escape_string( $this->number_downloaded ),
						$this->mysqli->escape_string( $this->number_moved ),
						$this->mysqli->escape_string( $this->number_saved )
				);
			$result=$this->mysqli->query( $query );
		}

		private function printStats()
		{
			$b=array();
			foreach((array)$this->newImages as $key=>$val )
			{
				if ( !empty($val->_status) )
				{
					$b[]="* ". $this->scpRemoteFolder . $val->filename . ": " . $val->_status;
				}
			}
			if ( !empty($b) )
			{
				$this->feedback( "errors:" );
				$this->feedback( implode("\n",$b) );
			}

			$this->feedback( "results:" );
			$this->feedback( sprintf("images in feed: %s ",$this->number_in_feed ) );
			$this->feedback( sprintf("images downloaded: %s ",$this->number_downloaded ) );
			$this->feedback( sprintf("images moved to remote server: %s ",$this->number_moved ) );
			$this->feedback( sprintf("images saved to database: %s ",$this->number_saved ) );
		}
		
		private function cleanUp()
		{
			$this->feedback( "closing database connection" );
			$this->mysqli->close();

			$this->feedback( "deleting temp files" );
			foreach((array)$this->newImages as $key=>$val )
			{
				if ( isset( $val->_tmp_file ) && file_exists( $val->_tmp_file ) )
				{
					if ( unlink( $val->_tmp_file ) )
					{
						//$this->feedback( sprintf("deleted %s",$val->_tmp_file) );
					}
					else
					{
						$this->feedback( sprintf("could mot delete %s",$val->_tmp_file) );
					}
				}
			}

			$this->feedback( "finished" . "\n" );
		}
		
	}

	// PRODUCTION
	$conn=array( 'host'=>'localhost', 'user'=>'linnaeus_user', 'password'=>'joieg2973SDF', 'database'=>'linnaeusng', 'prefix'=>'', 'project_id'=>1 );
	$webSeviceUrl='http://95.138.190.198/webservice/newimages?date=%DATE%&limit=%LIMIT%';

	// TEST
	$conn=array( 'host'=>'localhost', 'user'=>'linnaeus_user', 'password'=>'afew2344SAWE', 'database'=>'linnaeusng', 'prefix'=>'', 'project_id'=>1 );
	//$webSeviceUrl='http://162.13.81.40/webservice/newimages?date=%DATE%&limit=%LIMIT%' ;

	// DEVELOPMENT
	//$conn=array( 'host'=>'localhost', 'user'=>'nsr', 'password'=>'nsr', 'database'=>'soortenregister', 'prefix'=>'', 'project_id'=>1 );
	//$webSeviceUrl='http://162.13.81.40/webservice/newimages?date=%DATE%&limit=%LIMIT%' ;


	$b = new beeldbankDownloader;

	$b->setPrintParameters( false );
	$b->setIsTestRun( false );
	//$b->setFetchFromDateOverride( '2015-05-30 23:59:59' );
	$b->setConnectData( $conn );
	$b->setWebserviceUrl( $webSeviceUrl );
	$b->setFetchLimit( 9999 );
	$b->setScpRemoteUser( 'imageupload' );
	$b->setScpRemoteUserPubKeyFile( '/usr/sbin/beelduitwisselaar-download/imageupload.key' );
	$b->setScpRemoteAddress( '134.213.60.149' );
	$b->setScpRemoteBasePath( '/var/www/imageserver/original/' );
	$b->setScpRemoteFolder( 'beelduitwisselaartest' );
	$b->setMetaDataLanguage( 24 );  // dutch
	$b->go();




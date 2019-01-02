<?php

	class taxonXmlExporter
	{
		private $connector;
		private $mysqli;
		private $executionTimeOut=3600; //sec
		private $languageId;
		private $imageBaseUrl;
		private $fileNameBase="export--";
		private $fileName;
		private $fileNameDate;
		private $exportFolder;
		private $taxa;
		private $limit;
		private $maxBatchSize=5000;

		private $ranksToExport;
		private $ranksToExportStyle='equals';

		private $validNameId=1;
		private $idsToSuppressInClassification=[];
		private $xmlRootelement='root';

		private $includeDescriptions=true;
		private $includeNames=true;
		private $includeImages=true;
		private $includeClassification=true;
		private $prettify=true;

		private $filecounter=0;
		private $number_written=0;
		
		private $filelist;
	
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
				$this->connector->character_set = $data['characterSet'];
			} 
			catch (Exception $e)
			{
				$this->handleException( $e );
			}
		}
		
		public function setLanguageId( $id )
		{
			$this->languageId = $id;
		}

		public function setLimit( $limit )
		{
			$this->limit = $limit;
		}

		public function setMaxBatchSize( $size )
		{
			$this->maxBatchSize = (int)$size;
		}

		public function setFileNameBase( $fileNameBase )
		{
			$this->fileNameBase = (string)$fileNameBase;
		}

		public function setExportFolder( $folder )
		{
			$this->exportFolder = $folder;
		}

		public function setRanksToExport( $p )
		{
			/*
				ranks: 74 or [74,75,76]
				style: equal | lower | and_lower | higher | and_higher (whn ranks is an array, style always defaults to equal)
			*/

			if ( isset($p['ranks']) )
			{
				$this->ranksToExport = $p['ranks'];

				if ( is_array($this->ranksToExport) )
				{
					array_walk($this->ranksToExport,function(&$a,$i) { $a = (int)$a; } );
					$this->ranksToExport=array_unique($this->ranksToExport);
					if (count((array)$this->ranksToExport)==1) {
                        $this->ranksToExport = $this->ranksToExport[0];
                    }
				}
				else
				{
					$this->ranksToExport = (int)$this->ranksToExport;
				}
			}

			if ( isset($p['style']) )
			{
				switch ($p['style']) {
					case "lower" :
						$this->ranksToExportStyle = ">";
						break;
					case "and_lower" :
						$this->ranksToExportStyle = ">=";
						break;
					case "higher" :
						$this->ranksToExportStyle = "<";
						break;
					case "and_higher" :
						$this->ranksToExportStyle = "<=";
						break;
					case "equal" :
					case "equals" :
						$this->ranksToExportStyle = "=";
						break;
				}
			}

		}
	
		public function setValidNameTypeId( $id )
		{
			$this->validNameId=(int)$id;
		}
	
		public function setIdsToSuppressInClassification( $ids )
		{
			$this->idsToSuppressInClassification=(array)$ids;
		}
	
		public function setXmlRootelementName( $name )
		{
			$this->xmlRootelement=(string)$name;
		}

		public function setImageBaseUrl( $url )
		{
			$this->imageBaseUrl=(string)$url;
		}
		
		public function getFilelist()
		{
			return $this->filelist;
		}
	
		public function run()
		{
			try
			{
				$this->printHeader();
				$this->checkEssentials();
				$this->connectDatabase();
				$this->setTaxa();
				$this->writeData();
				$this->printStats();
				$this->cleanUp();
			} 
			catch (Exception $e)
			{
				$this->handleException($e);
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
			$this->feedback( "running exporter for NBA import" );
			$this->feedback(  date(DATE_RFC2822) );
		}

		private function generateOutFile()
		{
			if ( $this->filecounter==0 )
			{
				$this->fileNameDate = date('Y-m-d_Hi');
			}
			
			$this->filename = $this->fileNameBase . "--" . $this->fileNameDate . "--" . sprintf( '%02s', $this->filecounter++ ) . '.xml';

			$this->feedback( sprintf("writing to %s", $this->filename ) );
			
			$this->filelist[]=$this->filename;
		}

		private function checkEssentials()
		{
			if ( empty($this->connector->user) ) {
                $b[] = "missing database user";
            }
			if ( empty($this->connector->host) ) {
                $b[] = 'missing database host';
            }
			if ( empty($this->connector->database) ) {
                $b[] = "missing database name";
            }
			if ( empty($this->connector->project_id) ) {
                $b[] = "missing project id";
            }
			if ( is_null($this->languageId) ) {
                $b[] = "missing language id";
            }
			if ( is_null($this->exportFolder) ) {
                $b[] = "missing export folder";
            }
			if ( !is_writable($this->exportFolder) ) {
                $b[] = "export folder not writable";
            }
			if ( is_null($this->fileNameBase) ) {
                $b[] = "missing export filename";
            }

			if ( !empty( $b ) ) {
                throw new \Exception(implode("\n", $b));
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

				$this->mysqli->query('SET NAMES ' . $this->connector->character_set );
				$this->mysqli->query('SET CHARACTER SET ' . $this->connector->character_set );
			}
		}
		
		private function startXmlDocument()
		{
			$this->xmlWriter = new XMLWriter();
			$this->xmlWriter->openMemory();
			$this->xmlWriter->startDocument('1.0', 'UTF-8');
		}
		
		private function setTaxa()
		{
			$this->feedback( "fetching taxa");
			
			$ranks=null;
			
			if ( !is_null($this->ranksToExport) )
			{
				if ( is_array($this->ranksToExport) )
				{
					$ranks = "and _f.rank_id in (" . implode(",",$this->ranksToExport) .")";
				}
				else
				{
					$ranks = "and _f.rank_id " . $this->ranksToExportStyle . $this->ranksToExport;
				}
			}
		
			$query="
				select
					_t.taxon as name,
					replace(_r.rank,' ','_') as rank,
					_t.id,
					trim(LEADING '0' FROM replace(_rr.nsr_id,'tn.nlsr.concept/','')) as nsr_id,
					trim(LEADING '0' FROM replace(_pp.nsr_id,'tn.nlsr.concept/','')) as nsr_id_parent,
					concat('http://nederlandsesoorten.nl/nsr/concept/',replace(_rr.nsr_id,'tn.nlsr.concept/','')) as url,
					concat(_h.index_label,' ',_h.label) as status_status,
					_l2.label as status_reference_title,
					_e1.name as status_expert_name,
					_e2.name as status_organisation_name,
					_q.parentage as classification

				from
					".$this->connector->prefix."taxa _t

				left join ".$this->connector->prefix."projects_ranks _f
					on _t.rank_id=_f.id
					and _t.project_id=_f.project_id
					
				left join ".$this->connector->prefix."ranks _r
					on _f.rank_id=_r.id
					
				left join ".$this->connector->prefix."nsr_ids _rr
					on _t.id=_rr.lng_id
					and _rr.item_type='taxon'
					and _t.project_id=_rr.project_id
					
				left join ".$this->connector->prefix."nsr_ids _pp
					on _t.parent_id=_pp.lng_id
					and _pp.item_type='taxon'
					and _t.project_id=_pp.project_id
					
				left join ".$this->connector->prefix."presence_taxa _g
					on _t.id=_g.taxon_id
					and _t.project_id=_g.project_id
					
				left join ".$this->connector->prefix."presence_labels _h
					on _g.presence_id = _h.presence_id 
					and _g.project_id=_h.project_id 
					and _h.language_id=" . $this->languageId . "

				left join ".$this->connector->prefix."literature2 _l2
					on _g.reference_id = _l2.id 
					and _g.project_id=_l2.project_id

				left join ".$this->connector->prefix."actors _e1
					on _g.actor_id = _e1.id 
					and _g.project_id=_e1.project_id

				left join ".$this->connector->prefix."actors _e2
					on _g.actor_org_id = _e2.id 

					and _g.project_id=_e2.project_id

				left join ".$this->connector->prefix."taxon_quick_parentage _q
					on _t.id=_q.taxon_id
					and _t.project_id=_q.project_id

				left join ".$this->connector->prefix."trash_can _trash
					on _t.project_id = _trash.project_id
					and _t.id =  _trash.lng_id
					and _trash.item_type='taxon'
	
				where
					_t.project_id = " . $this->connector->project_id . "
					and ifnull(_trash.is_deleted,0)=0
					" . ( $ranks ? $ranks : "" ) ."
				" . ( !empty( $this->limit ) ? "limit " . $this->limit : "" ) . "
			";


			
			$result=$this->mysqli->query( $query );

			if ( $result )
			{
				while( $row=$result->fetch_assoc() )
				{
					$this->taxa[]=$row;
				}
			}
		}

		private function getDescriptions( $id )
		{
			$query="
				select
					_x2.title,_x1.content as text,
					if (_x2.title='Summary','English',_x3.language) as language

				from
					".$this->connector->prefix."content_taxa _x1
	
				left join ".$this->connector->prefix."pages_taxa_titles _x2
					on _x1.project_id=_x2.project_id
					and  _x1.page_id=_x2.page_id
	
				left join ".$this->connector->prefix."languages _x3
					on _x1.language_id=_x3.id
	
				where
					_x1.project_id =".$this->connector->project_id."
					and _x1.taxon_id = ".$id
			;

			
			$d=array();

			$result=$this->mysqli->query( $query );
			
			if ( $result )
			{
				while( $row=$result->fetch_assoc() )
				{
					$d[]=$row;
				}
			}
			
			return $d;
		}

		private function getNames( $id )
		{
			$query="
				select
					_a.name as fullname,
					_a.uninomial,
					_a.specific_epithet,
					_a.infra_specific_epithet,
					_a.authorship,
					_a.name_author,
					_a.authorship_year,
					_a.reference,
					_a.expert,
					_a.organisation,
					_b.nametype,
					_e.name as expert_name,
					_f.name as organisation_name,
					_g.label as reference_title,
					_g.author as reference_author,
					_g.date as reference_date,
					_lan.language
	
				from ".$this->connector->prefix."names _a
	
				left join ".$this->connector->prefix."name_types _b 
					on _a.type_id=_b.id 
					and _a.project_id = _b.project_id
	
				left join ".$this->connector->prefix."actors _e
					on _a.expert_id = _e.id 
					and _a.project_id=_e.project_id
	
				left join ".$this->connector->prefix."actors _f
					on _a.organisation_id = _f.id 
					and _a.project_id=_f.project_id
	
				left join ".$this->connector->prefix."literature2 _g
					on _a.reference_id = _g.id 
					and _a.project_id=_g.project_id
	
				left join ".$this->connector->prefix."languages _lan
					on _a.language_id=_lan.id
	
				where
					_a.project_id = ".$this->connector->project_id."
					and _a.taxon_id = ". $id
			;

			
			$d=array();

			$result=$this->mysqli->query( $query );
			
			if ( $result )
			{
				while( $row=$result->fetch_assoc() )
				{
					$d[]=$row;
				}
			}
			
			return $d;
		}
		
		private function getImages( $id )
		{
			$query="		
				select
					_m.file_name as file_name,
					_m.mime_type as mime_type,
					_c.meta_data as photographer_name,
					date_format(_meta1.meta_date,'%e %M %Y') as date_taken,
					_meta2.meta_data as short_description,
					_meta3.meta_data as geography,
					_meta5.meta_data as copyright,
					_meta7.meta_data as maker_adress,
					if (upper(substring(_meta10.meta_data,1,2))='CC',_meta10.meta_data,if(_c.meta_data is null,'','All rights reserved')) as licence,
					if (upper(substring(_meta10.meta_data,1,2))='CC','Copyright',if(_c.meta_data is null,'','Copyright')) as licence_type
				
				from  ".$this->connector->prefix."media_taxon _m
				
				left join ".$this->connector->prefix."media_meta _c
					on _m.project_id=_c.project_id
					and _m.id = _c.media_id
					and _c.sys_label = 'beeldbankFotograaf'
					and _c.language_id=".$this->languageId."
			
				left join ".$this->connector->prefix."media_meta _meta1
					on _m.id=_meta1.media_id
					and _m.project_id=_meta1.project_id
					and _meta1.sys_label='beeldbankDatumVervaardiging'
					and _meta1.language_id=".$this->languageId."
	
				left join ".$this->connector->prefix."media_meta _meta2
					on _m.id=_meta2.media_id
					and _m.project_id=_meta2.project_id
					and _meta2.sys_label='beeldbankOmschrijving'
					and _meta2.language_id=".$this->languageId."
				
				left join ".$this->connector->prefix."media_meta _meta3
					on _m.id=_meta3.media_id
					and _m.project_id=_meta3.project_id
					and _meta3.sys_label='beeldbankLokatie'
					and _meta3.language_id=".$this->languageId."
				
				left join ".$this->connector->prefix."media_meta _meta5
					on _m.id=_meta5.media_id
					and _m.project_id=_meta5.project_id
					and _meta5.sys_label='beeldbankCopyright'
					and _meta5.language_id=".$this->languageId."
	
				left join ".$this->connector->prefix."media_meta _meta7
					on _m.id=_meta7.media_id
					and _m.project_id=_meta7.project_id
					and _meta7.sys_label='beeldbankAdresMaker'
					and _meta7.language_id=".$this->languageId."

				left join ".$this->connector->prefix."media_meta _meta10
					on _m.id=_meta10.media_id
					and _m.project_id=_meta10.project_id
					and _meta10.sys_label='beeldbankLicentie'

				where
					_m.project_id = ".$this->connector->project_id."
					and _m.taxon_id = ".$id
			;

			
			$d=array();

			$result=$this->mysqli->query( $query );
			
			if ( $result )
			{
				while( $row=$result->fetch_assoc() )
				{
					$row['url'] = $this->imageBaseUrl . rawurlencode($row['file_name']);
					$d[]=$row;
				}
			}
				
			return $d;
		}
		
		private function getClassification( $pId )
		{
			$query="		
				select
					_t.id,
					ifnull(_names.uninomial,_t.taxon) as name,
					_r.rank
	
				from
					".$this->connector->prefix."taxa _t
	
				left join ".$this->connector->prefix."projects_ranks _f
					on _t.rank_id=_f.id
					and _t.project_id=_f.project_id
	
				left join ".$this->connector->prefix."names _names
					on _t.project_id=_f.project_id
					and _t.id=_names.taxon_id
					and _names.type_id=".$this->validNameId."
	
				left join ".$this->connector->prefix."ranks _r
					on _f.rank_id=_r.id
	
				where _t.project_id = ".$this->connector->project_id." and _t.id=".$pId
			;

			
			$result=$this->mysqli->query( $query );
			
			if ( $result )
			{
				$row=$result->fetch_assoc();
				if ( $row )
				{
					return $row;
				}
			}
		}

		private function arrayToXml($data, &$simpleXmlObject)
		{
			foreach($data as $key => $value)
			{
				if(is_array($value))
				{
					if(!is_numeric($key))
					{
						if (strpos($key,'__')!==false)
						{
							$key=substr($key,0,strpos($key,'__'));
						}
						$subnode = $simpleXmlObject->addChild("$key");
						$this->arrayToXml($value, $subnode);
					}
					else
					{
						$subnode = $simpleXmlObject->addChild("item$key");
						$this->arrayToXml($value, $subnode);
					}
				}
				else
				{
					$simpleXmlObject->addChild("$key",htmlspecialchars("$value"));
				}
			}
		}		

		private function cleanImageLicence($a)
		{
			if (strpos($a,'CC')==0)
			{
				return preg_replace_callback('/^(((CC[ 0]+)(.*))(\())(.*)/', function($matches) { return trim($matches[2]); }, $a);
			}
			return $a;
		}

        /**
         * @throws Exception
         */
        private function writeData()
		{
			$this->feedback( "writing data" );
			
			set_time_limit( $this->executionTimeOut );
			
			if ( empty($this->taxa) )
			{
				throw new Exception( 'Found no taxa.' );
			}
			
			$this->generateOutFile();

			$this->startXmlDocument();
			
			$batch=0;
			
			foreach((array)$this->taxa as $key=>$val)
			{
			
				if ( $this->includeDescriptions )
				{
					$pages=$this->getDescriptions( $val['id'] );
					$j=0;
					$description=array();
					foreach((array)$pages as $page) $description['page__'.($j++)]=$page;
				}

				if ( $this->includeNames )
				{
					$n=$this->getNames( $val['id'] );
					$k=0;
					$names=array();
					foreach((array)$n as $vdsdvsdfs) $names['name__'.($k++)]=$vdsdvsdfs;
				}

				if ( $this->includeImages )
				{
					$c=$this->getImages( $val['id'] );
					$l=0;
					$images=array();
					foreach((array)$c as $buytjyuy) 
					{
						$buytjyuy['licence']=$this->cleanImageLicence($buytjyuy['licence']);
						$images['image__'.($l++)]=$buytjyuy;
					}
				}

				$val['status']=
					array(
						'status' => $val['status_status'],
						'reference_title' => $val['status_reference_title'],
						'expert_name' => $val['status_expert_name'],
						'organisation_name' => $val['status_organisation_name']
					);

				$val['description']=@$description;
				$val['names']=@$names;
				$val['classification']=@explode(' ',$val['classification']);
				$val['images']=@$images;

				if ( $this->includeClassification )
				{
					$class=array();
					$m=0;	
					foreach($val['classification'] as $pId)
					{
						if (in_array($pId,$this->idsToSuppressInClassification)) continue;
						
						if (isset($lookuplist[$pId]))
						{
							$t=$lookuplist[$pId];
						}
						else
						{
							$t=$this->getClassification( $pId );
							$lookuplist[$t['id']]=array('name'=>$t['name'],'rank'=>$t['rank']);
							//$this->addError($pId." not in classification lookup list!?");
	
						}
						$class['taxon__'.($m++)]=@array('name'=>$t['name'],'rank'=>$t['rank']);
					}
					
					$val['classification']=$class;
					
				}
				else
				{
					unset($val['classification']);
				}

                unset($val['id'], $val['status_status'], $val['status_reference_title'], $val['status_expert_name'], $val['status_organisation_name']);

                unset($this->taxa[$key]);

				$xml= '<taxon></taxon>';
		
				$simpleXmlObject = new SimpleXMLElement($xml);
		
				$this->arrayToXml($val,$simpleXmlObject);
				
				if ($this->prettify)
				{
					$dom = new DOMDocument('1.0');
					$dom->preserveWhiteSpace = false;
					$dom->formatOutput = true;
					$dom->loadXML($simpleXmlObject->asXML());
					$out=$dom->saveXML();
				}
				else
				{
					$out=$simpleXmlObject->asXML();
				}
	
				if ($batch==0)
				{
					$this->xmlWriter->writeRaw ( '<'.$this->xmlRootelement.' exportdate="'.date('c').'">' . "\n" . '<taxa>' );	
				}
			
				$this->xmlWriter->writeRaw ( str_replace('<?xml version="1.0"?>' , '' , $out ) );
				$this->number_written++;
			
				unset($val);
				
				if ($key%1000==0)
				{
					file_put_contents( $this->exportFolder . $this->filename , $this->xmlWriter->flush(true), FILE_APPEND);
				}
			
				if (++$batch==$this->maxBatchSize)
				{
					$this->xmlWriter->writeRaw ( '</taxa>' . "\n" . '</'.$this->xmlRootelement.'>' );	
					file_put_contents( $this->exportFolder . $this->filename , $this->xmlWriter->flush(true), FILE_APPEND);
					$this->startXmlDocument();
					$this->generateOutFile();
					$batch=0;
				}
			}
			
			if ( $batch>0 )
			{
				$this->xmlWriter->writeRaw ( '</taxa>' . "\n" . '</'.$this->xmlRootelement.'>' );	
				file_put_contents( $this->exportFolder . $this->filename , $this->xmlWriter->flush(true), FILE_APPEND);
			}

		}

		private function printStats()
		{
			$this->feedback( sprintf("wrote %s taxa (%s files in %s)",$this->number_written, $this->filecounter, $this->exportFolder ) );
		}
		
		private function cleanUp()
		{
			$this->feedback( "closing database connection" );
			$this->mysqli->close();
			$this->feedback( "finished" . "\n" );
		}
		
	}

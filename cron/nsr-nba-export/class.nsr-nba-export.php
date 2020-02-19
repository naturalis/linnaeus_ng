<?php

	class taxonExporter
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

		private $ranksToExport;
		private $ranksToExportStyle='equals';

		private $validNameId=1;
		private $idsToSuppressInClassification=[];

		private $includeDescriptions=true;
		private $includeNames=true;
		private $includeImages=true;
		private $includeClassification=true;

		private $authorCache=[];

		private $batch=0;
		private $batchSize=10000;
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

		public function setbatchSize( $size )
		{
			$this->batchSize = (int)$size;
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

				$this->processing = true;

				while($this->processing)
				{
					$this->setTaxa();
					$this->writeData();					
					$this->batch++;
					$this->number_written += count($this->taxa);
					$this->processing=count($this->taxa)>0;
				}

				$this->printStats();
				$this->cleanUp();
			} 
			catch (Exception $e)
			{
				$this->handleException($e);
			}
		}
		
		private function setTaxa()
		{
			$this->feedback( sprintf("fetching batch %s",$this->batch) );

			$this->taxa=[];
			
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
					distinct
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
					order by _t.id
				    limit " . $this->batchSize ." offset " . ($this->batchSize * $this->batch)
			;
			
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
					_x1.id,
					_x2.title,_x1.content as text,
					if (_x2.title='Summary','English',_x3.language) as language,
					_x1.last_change

				from
					".$this->connector->prefix."content_taxa _x1
	
				left join ".$this->connector->prefix."pages_taxa_titles _x2
					on _x1.project_id=_x2.project_id
					and  _x1.page_id=_x2.page_id
	
				left join ".$this->connector->prefix."languages _x3
					on _x1.language_id=_x3.id
	
				where
					_x1.project_id = ".$this->connector->project_id . "
					and _x1.taxon_id = " . $id
				;
			
			$d=array();

			$result=$this->mysqli->query( $query );
			
			if ( $result )
			{
				while( $row=$result->fetch_assoc() )
				{
					$d[]=$row;
				}

				$result->free();
			}

			if (empty($this->authorCache))
			{
				$query="
					select
						rdf.subject_id,
						_act.name
					from ".
						$this->connector->prefix."rdf 
					left join ".$this->connector->prefix."actors _act
						on _act.id = rdf.object_id
					where 
						rdf.subject_type = 'passport' 
						and rdf.predicate = 'hasAuthor'
					";

				$result=$this->mysqli->query( $query );
				
				if ( $result )
				{
					while( $row=$result->fetch_assoc() )
					{
						$this->authorCache[$row["subject_id"]][]=$row;
					}

					$result->free();
				}
			}

			$e=array();

			foreach ($d as $key => $val)
			{
				$val["authors"]=isset($this->authorCache[$val["id"]]) ? $this->authorCache[$val["id"]] : [];
				unset($val["id"]);
				$e[]=$val;
			}

			return $e;
		}

		private function getNames( $id )
		{
			$query="
				select
					distinct
						_a.taxon_id,
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
	
				from ".
					$this->connector->prefix."names _a
	
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
					_a.project_id = ".$this->connector->project_id . "
					and _a.taxon_id = " . $id
			;
					
			$d=[];

			$result=$this->mysqli->query( $query );

			if ( $result )
			{
				while( $row=$result->fetch_assoc() )
				{
					$d[]=$row;
				}

				$result->free();
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
				
				from  ".
					$this->connector->prefix."media_taxon _m
				
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
					_m.project_id = ".$this->connector->project_id . "
  					and _m.taxon_id = " . $id
			;

			
			$d=[];

			$result=$this->mysqli->query( $query );
			
			if ( $result )
			{
				while( $row=$result->fetch_assoc() )
				{
					$row['url'] = $this->imageBaseUrl . rawurlencode($row['file_name']);
					$d[]=$row;
				}

				$result->free();
			}

			return $d;
		}

		private function getClassification( $id )
		{
			$query="		
				select
					_t.id,
					ifnull(_names.uninomial,_t.taxon) as name,
					_r.rank

				from
					".$this->connector->prefix."taxa _t

				left join ".$this->connector->prefix."projects_ranks _f
					on _t.rank_id = _f.id
					and _t.project_id = _f.project_id

				left join ".$this->connector->prefix."names _names
					on _t.project_id = _names.project_id
					and _t.id = _names.taxon_id
					and _names.type_id = ".$this->validNameId."

				left join ".$this->connector->prefix."ranks _r
					on _f.rank_id = _r.id

				where
					_t.project_id = ".$this->connector->project_id . "
					and _t.id = " . $id
			;

			$d=[];

			$result=$this->mysqli->query( $query );
			
			if ( $result )
			{
				$d=$result->fetch_assoc();
				$result->free();
			}

			return $d;
		}

		private function cleanImageLicence($a)
		{
			if (strpos($a,'CC')==0)
			{
				return preg_replace_callback('/^(((CC[ 0]+)(.*))(\())(.*)/', function($matches) { return trim($matches[2]); }, $a);
			}
			return $a;
		}

        private function writeData()
		{
			if ( empty($this->taxa) )
			{
				return;
			}

			set_time_limit( $this->executionTimeOut );
			
			$this->generateOutFile();

			$batch=0;

			if ( $this->includeDescriptions )
			{
				foreach((array)$this->taxa as $key=>$val)
				{
					$pages=$this->getDescriptions( $val['id'] );
					$description=array();
					foreach((array)$pages as $page) $description[]=$page;
					$this->taxa[$key]['description']=@$description;

				}
			}

			if ( $this->includeNames )
			{
				foreach((array)$this->taxa as $key=>$val)
				{
					$n=$this->getNames( $val['id'] );
					$names=array();
					foreach((array)$n as $vdsdvsdfs) $names[]=$vdsdvsdfs;
					$this->taxa[$key]['names']=@$names;
				}
			}

			if ( $this->includeImages )
			{
				foreach((array)$this->taxa as $key=>$val)
				{
					$c=$this->getImages( $val['id'] );
					$images=array();
					foreach((array)$c as $buytjyuy) 
					{
						$buytjyuy['licence']=$this->cleanImageLicence($buytjyuy['licence']);
						$images[]=$buytjyuy;
					}
					$this->taxa[$key]['images']=@$images;
				}
			}

			foreach((array)$this->taxa as $key=>$val)
			{
				if ( $this->includeClassification )
				{
					$class=array();
					$m=0;	
					$val['classification']=@explode(' ',$val['classification']);
					foreach($val['classification'] as $id)
					{
						if (in_array($id,$this->idsToSuppressInClassification)) continue;
						$t=$this->getClassification( $id );
						$class[	]=@array('name'=>$t['name'],'rank'=>$t['rank']);
					}
					
					$this->taxa[$key]['classification']=$class;
					
				}
				else
				{
					unset($val['classification']);
				}
			}

			$this->feedback( sprintf("writing %s", $this->filename) );

			foreach((array)$this->taxa as $key=>$val)
			{
				$val['status']=
					array(
						'status' => $val['status_status'],
						'reference_title' => $val['status_reference_title'],
						'expert_name' => $val['status_expert_name'],
						'organisation_name' => $val['status_organisation_name']
					);

				unset(
					$val['id'], 
					$val['status_status'], 
					$val['status_reference_title'], 
					$val['status_expert_name'],
					$val['status_organisation_name']
				);

				file_put_contents( $this->exportFolder . $this->filename , json_encode($val) . "\n", FILE_APPEND);
			}
		}

		private function printStats()
		{
			$this->feedback( sprintf("wrote %s taxa (%s files in %s)",$this->number_written, $this->batch, $this->exportFolder ) );
		}
		
		private function cleanUp()
		{
			$this->feedback( "closing database connection" );
			$this->mysqli->close();
			$this->feedback( "finished" . "\n" );
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

		private function generateOutFile( $extension='jsonl')
		{
			if ( $this->batch==0 )
			{
				$this->fileNameDate = date('Y-m-d_Hi');
			}
			
			$this->filename = 
				$this->fileNameBase . "--" . 
				$this->fileNameDate . "--" . 
				sprintf( '%02s', $this->batch ) . 
				'.' . ltrim($extension,'.');
			
			$this->filelist[]=$this->filename;
		}

		private function checkEssentials()
		{
			if ( empty($this->connector->user) )
			{
                $b[] = "missing database user";
            }
			if ( empty($this->connector->host) )
			{
                $b[] = 'missing database host';
            }
			if ( empty($this->connector->database) )
			{
                $b[] = "missing database name";
            }
			if ( empty($this->connector->project_id) )
			{
                $b[] = "missing project id";
            }
			if ( is_null($this->languageId) )
			{
                $b[] = "missing language id";
            }
			if ( is_null($this->exportFolder) )
			{
                $b[] = "missing export folder";
            }
			if ( !is_writable($this->exportFolder) )
			{
                $b[] = "export folder not writable";
            }
			if ( is_null($this->fileNameBase) )
			{
                $b[] = "missing export filename";
            }

			if ( !empty( $b ) )
			{
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
	}

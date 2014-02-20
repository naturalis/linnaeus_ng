<?php


	function getProgeny($parent,$level,$family)
	{
		$d=$this->models->Taxon->freeQuery("
			select id,parent_id,taxon,".$level." as level from %PRE%taxa where project_id =".$this->getCurrentProjectId()." and parent_id=".$parent
		);

		$family[]=$parent;

		foreach((array)$d as $val) {
			$val['parentage']=$family;
			$this->tmp[]=$val;

			$this->getProgeny($val['id'],$level+1,$family);
		}
		
	}



	function generateQuickParentageTable()
	{
		$d=$this->models->Taxon->freeQuery("select id from %PRE%taxa where project_id = ". $this->getCurrentProjectId() ." and parent_id is null and taxon='Leven'");
		if (empty($d))
			die('no top!?');
			
		$this->tmp=array();
		$this->getProgeny($d[0]['id'],0,array());


		$this->models->Taxon->freeQuery("delete from %PRE%taxon_quick_parentage where project_id = ". $this->getCurrentProjectId());

		foreach((array)$this->tmp as $val) {
			$this->models->Taxon->freeQuery("
				insert into %PRE%taxon_quick_parentage (project_id,taxon_id,parentage,created)
				values (".$this->getCurrentProjectId().",".$val['id'].",'".implode(' ',$val['parentage'])."',now())"
			);
		}


		die('done!');

			
	}

	

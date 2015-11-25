<?php
include_once (dirname(__FILE__) . "/AbstractModel.php");

final class MatrixKeyAppModel extends AbstractModel
{
	
	private $remainingCountClauses;

    public function __construct ()
    {
        parent::__construct();

        $this->connectToDatabase() or die(_('Failed to connect to database '.
            $this->databaseSettings['database'].
        	' with user ' . $this->databaseSettings['user'] . '. ' .
            mysqli_connect_error() . '. Correct the getDatabaseSettings() settings
        	in configuration/admin/config.php.'));

     }

    public function __destruct ()
    {
        if ($this->databaseConnection)
		{
            $this->disconnectFromDatabase();
        }
        parent::__destruct();
    }


	public function getDetail( $params )
	{

project_id	$this->getCurrentProjectId()
taxon_id	$data['id']
	
		$t=$this->models->Taxon->freeQuery("
			select t.id,trim(replace(t.taxon,'%VAR%','')) as name_sci, c.commonname as name_nl, 
				p.id as group_id, p.taxon as groupname_sci, pc.commonname as groupname_nl, 'taxon' as type 
			from %PRE%taxa t
			left join %PRE%commonnames c 
				on c.taxon_id = t.id 
				and c.project_id = ".$this->getCurrentProjectId()."
			left join %PRE%taxa p 
				on t.parent_id = p.id 
				and p.project_id = ".$this->getCurrentProjectId()."
			left join %PRE%commonnames pc 
				on pc.taxon_id = p.id 
				and pc.project_id = ".$this->getCurrentProjectId()."
			where t.id = ".$data['id']."
			and t.project_id = ".$this->getCurrentProjectId()
		);
		$res=$t[0];


		$t=$this->models->Taxon->freeQuery("
			select _b.title,_a.content, _c.page 
			from %PRE%content_taxa _a
			left join %PRE%pages_taxa_titles _b 
				on _a.page_id = _b.page_id 
				and _b.project_id = ".$this->getCurrentProjectId()."
			left join %PRE%pages_taxa _c 
				on _a.page_id = _c.id 
				and _c.project_id = ".$this->getCurrentProjectId()."
			where _a.taxon_id = ".$data['id']."
			and _a.project_id = ".$this->getCurrentProjectId()
		);
		$res['content']=$t;


		$t=$this->models->Taxon->freeQuery("
			select _a.value as file_name,_b.value as copyright, '1' as overview_image 
			from %PRE%nbc_extras _a
			left join %PRE%nbc_extras _b 
				on _b.ref_type = 'taxon' 
				and _b.ref_id=_a.ref_id 
				and _b.name='photographer' 
				and _b.project_id = ".$this->getCurrentProjectId()."
			where _a.ref_id = ".$data['id']." 
				and _a.ref_type='taxon' 
				and _a.name='url_image'
			and _a.project_id = ".$this->getCurrentProjectId()
		);
		$res['img_main']=$t;


		$t=$this->models->Taxon->freeQuery("
			select file_name from %PRE%media_taxon where taxon_id = ".$data['id']." and project_id = ".$this->getCurrentProjectId()
		);
		$res['img_other']=$t;


		$t=$this->models->Taxon->freeQuery("
			select 'taxon' as type, _b.id as id, _b.taxon as taxon,_c.commonname as label, _n.value as img 
			from %PRE%taxa_relations _a 
			left join %PRE%taxa _b 
				on _b.id = _a.relation_id 
				and _b.project_id = ".$this->getCurrentProjectId()."
			left join %PRE%commonnames _c 
				on _c.taxon_id = _b.id 
				and _c.project_id = ".$this->getCurrentProjectId()."
			left join %PRE%nbc_extras _n 
				on _b.id = _n.ref_id 
				and _n.ref_type='taxon' 
				and _n.name='url_thumbnail' 
				and _n.project_id = ".$this->getCurrentProjectId()."
			where _a.ref_type='taxon' and _a.taxon_id = ".$data['id']." 
			and _a.project_id = ".$this->getCurrentProjectId()."
			union all
			select 'variation' as type, _e.id as id,  _f.taxon as taxon, _e.label as label, _n.value as img 
			from %PRE%taxa_relations _d 
			left join %PRE%taxa_variations _e 
				on _e.id = _d.relation_id 
				and _e.project_id = ".$this->getCurrentProjectId()."
			left join %PRE%taxa _f 
				on _f.id = _d.taxon_id 
				and _f.project_id = ".$this->getCurrentProjectId()."
			left join %PRE%nbc_extras _n 
				on _d.taxon_id = _n.ref_id 
				and _n.ref_type='taxon' 
				and _n.name='url_thumbnail' 
				and _n.project_id = ".$this->getCurrentProjectId()."
			where _d.ref_type='variation'  
			and _d.taxon_id =".$data['id']." 
			and _d.project_id = ".$this->getCurrentProjectId()
		);
		$res['similar']=$t;

		$t=$this->models->Taxon->_get(array('id'=>array('project_id' => $this->getCurrentProjectId(),'parent_id'=>$data['id'])));
		foreach((array)$t as $key => $val)
		{
			$t[$key]['label']=$this->getCommonname($val['id']);
			$t[$key]['img']=$this->getNbcExtras(array('id'=>$val['id'],'name'=>'url_thumbnail'));
		}
		if ($t)
		{
			usort($t,function($a,$b) {return ($a['label']>$b['label']?1:-1);});
			$res['children']=$t;
		}

		return $res;
					
	}
	

    public function getGuiMenuOrder( $params )
    {
		$project_id = isset($params['project_id']) ? $params['project_id'] : null;
		$language_id = isset($params['language_id']) ? $params['language_id'] : null;
		$matrix_id = isset($params['matrix_id']) ? $params['matrix_id'] : null;
		
		if ( is_null($project_id)  ||  is_null($language_id)   ||  is_null($matrix_id) )
			return;
		
		$query="
			select 
					_a.ref_id as id,'character' as type,_a.show_order as show_order,
					if(locate('|',_b.label)=0,_b.label,substring(_b.label,1,locate('|',_b.label)-1)) as label,
				if(locate('|',_b.label)=0,_b.label,substring(_b.label,locate('|',_b.label)+1)) as description
			from %TABLE% _a
			left join %PRE%characteristics_labels _b on _b.characteristic_id = _a.ref_id and _b.language_id = ".$language_id."
			where 
				_a.project_id = ".$project_id."
				and _a.matrix_id = ".$mmatrix_idId."
				and _a.ref_type='char'
			union all
			select 
				_a.ref_id as id,'c_group' as type,_a.show_order as show_order, _c.label as label, null as description from %TABLE% _a
			left join %PRE%chargroups_labels _c on _c.chargroup_id = _a.ref_id and _c.language_id = ".$language_id."
			where
				_a.project_id = ".$project_id."
				and _a.matrix_id = ".$matrix_id."
				and _a.ref_type='group'
			order by show_order,label"
		;

		return $this->freeQuery( $query );
	}
















}





















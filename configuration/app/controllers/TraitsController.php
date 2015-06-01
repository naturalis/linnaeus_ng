<?php

/*
http://localhost/linnaeus_ng/app/views/traits/get.php?taxon=172145&group=1&language=24&project=1
http://localhost/linnaeus_ng/app/views/traits/get.php?taxon=172145&group=1&language=24&project=1
http://localhost/linnaeus_ng/app/views/traits/get.php?taxon=172145&group=1&language=24&project=1
*/

include_once ('Controller.php');

class TraitsController extends Controller
{

    public $usedModels = array(
		'traits_settings',
		'traits_groups',
		'traits_types',
		'traits_project_types',
		'traits_traits',
		'text_translations',
		'traits_values',
		'traits_taxon_values',
		'literature2',
		'literature2_authors'
    );
   
    public $controllerPublicName = 'Kenmerken';

    public $cacheFiles = array();
    
    public $cssToLoad = array();

	public $jsToLoad=array();

    public function __construct($p=null)
    {
        parent::__construct($p);
		$this->initialise();
    }

    public function __destruct ()
    {
        parent::__destruct();
    }

    private function initialise()
    {
    }


	private function formatDbDate($date,$format)
	{
		return is_null($date) ? null : date_format(date_create($date),$format);
	}

    public function getAction()
	{
		$project=$this->rGetVal('project');
		$language=$this->rGetVal('language');
		$taxon=$this->rGetVal('taxon');
		$group=$this->rGetVal('group');

		$data=array();

		if (empty($project)||empty($taxon)||empty($group)||empty($language))
		{
			die('error');
		}

		$r=$this->models->TraitsTaxonValues->freeQuery("
			select * from (
			
				select
					_b.trait_id,
					_c.sysname as trait_sysname,
					if(length(ifnull(_t1.translation,''))=0,_c.sysname,_t1.translation) as trait_name,
					_t2.translation as trait_code,
					_t3.translation as trait_description,
					_g.sysname as trait_type_sysname,
					(CASE 
						WHEN locate('string',_g.sysname)=1 THEN 
							if(length(ifnull(_t4.translation,''))=0,_b.string_value,_t4.translation)
						WHEN (locate('int',_g.sysname)=1 || locate('float',_g.sysname)=1) THEN
							_b.numerical_value
						WHEN locate('date',_g.sysname)=1 THEN
							_b.date
						ELSE null
					END) AS value_start,
					(CASE 
						WHEN (locate('int',_g.sysname)=1 || locate('float',_g.sysname)=1) THEN _b.numerical_value_end
						WHEN locate('date',_g.sysname)=1 THEN _b.date_end
						ELSE null
					END) AS value_end,

					_b.date as _date_value,
					_b.date_end as _date_value_end,
					_e.format as _date_format,
					
					_c.show_order as _show_order_1,
					_b.show_order as _show_order_2
			
				from
					%PRE%traits_taxon_values _a
				
				left join %PRE%traits_values _b
					on _a.project_id=_b.project_id
					and _a.value_id=_b.id
			
				left join %PRE%traits_traits _c
					on _a.project_id=_c.project_id
					and _b.trait_id=_c.id
			
				left join %PRE%text_translations _t1
					on _c.project_id=_t1.project_id
					and _c.name_tid=_t1.text_id
					and _t1.language_id=".$language."
			
				left join %PRE%text_translations _t2
					on _c.project_id=_t2.project_id
					and _c.code_tid=_t2.text_id
					and _t2.language_id=".$language."
			
				left join %PRE%text_translations _t3
					on _c.project_id=_t3.project_id
					and _c.description_tid=_t3.text_id
					and _t3.language_id=".$language."
			
				left join %PRE%text_translations _t4
					on _b.project_id=_t4.project_id
					and _b.string_label_tid=_t4.text_id
					and _t4.language_id=".$language."
			
				left join %PRE%traits_project_types _f
					on _c.project_id=_f.project_id
					and _c.project_type_id=_f.id
			
				left join  %PRE%traits_types _g
					on _f.type_id=_g.id

				left join 
					%PRE%traits_date_formats _e
					on _c.date_format_id=_e.id
							
				where
					_a.project_id=".$project."
					and _a.taxon_id=".$taxon."
					and _b.trait_id is not null
			
				union
			
				select
					_a.trait_id,
					_c.sysname as trait_sysname,
					if(length(ifnull(_t1.translation,''))=0,_c.sysname,_t1.translation) as trait_name,
					_t2.translation as trait_code,
					_t3.translation as trait_description,
					_g.sysname as trait_type_sysname,
					(CASE 
						WHEN locate('string',_g.sysname)=1 THEN string_value
						WHEN (locate('int',_g.sysname)=1 || locate('float',_g.sysname)=1) THEN numerical_value
						WHEN locate('date',_g.sysname)=1 THEN date_value
						ELSE null
					END) AS value_start,
					(CASE 
						WHEN (locate('int',_g.sysname)=1 || locate('float',_g.sysname)=1) THEN numerical_value_end
						WHEN locate('date',_g.sysname)=1 THEN date_value_end
						ELSE null
					END) AS value_end,

					date_value as _date_value,
					date_value_end as _date_value_end,
					_e.format as _date_format,
			
					_c.show_order as _show_order_1,
					null as _show_order_2
					
				from
					%PRE%traits_taxon_freevalues _a
				
				left join %PRE%traits_traits _c
					on _a.project_id=_c.project_id
					and _a.trait_id=_c.id
				
				left join %PRE%text_translations _t1
					on _c.project_id=_t1.project_id
					and _c.name_tid=_t1.text_id
					and _t1.language_id=".$language."
				
				left join %PRE%text_translations _t2
					on _c.project_id=_t2.project_id
					and _c.code_tid=_t2.text_id
					and _t2.language_id=".$language."
				
				left join %PRE%text_translations _t3
					on _c.project_id=_t3.project_id
					and _c.description_tid=_t3.text_id
					and _t3.language_id=".$language."
				
				left join %PRE%traits_project_types _f
					on _c.project_id=_f.project_id
					and _c.project_type_id=_f.id
				
				left join %PRE%traits_types _g
					on _f.type_id=_g.id

				left join 
					%PRE%traits_date_formats _e
					on _c.date_format_id=_e.id
				
				where
					_a.project_id=".$project."
					and _a.taxon_id=".$taxon."
			
			) as unionized
			order by _show_order_1,_show_order_2
		");
	
		$d=array();
		
		foreach((array)$r as $key=>$val)
		{
			$d[$val['trait_id']]['trait']=
				array(
					'id'=>$val['trait_id'],
					'sysname'=>$val['trait_sysname'],
					'name'=>$val['trait_name'],
					'code'=>$val['trait_code'],
					'description'=>$val['trait_description'],
					'type'=>$val['trait_type_sysname'],
				);
				
			if (!empty($val['_date_value']))
				$val['value_start']=$this->formatDbDate($val['_date_value'],$val['_date_format']);
			if (!empty($val['_date_value_end']))
				$val['value_end']=$this->formatDbDate($val['_date_value_end'],$val['_date_format']);

			$d[$val['trait_id']]['values'][]=
				array(
					'value_start'=>$val['value_start'],
					'value_end'=>$val['value_end'],
				);

		}

		foreach($d as $val)
		{
			$data[]=$val;
		}
		
		$references=$this->getReferences(array('taxon'=>$taxon,'group'=>$group,'project'=>$project));


		header('Content-type:text/plain');
		
		echo json_encode(array(
			'request'=>array(
				'project_id'=>$project,
				'language_id'=>$language,
				'taxon_id'=>$taxon,
				'group_id'=>$group,
			),
			'result'=>array(
				'data'=>$data,
				'references'=>$references
			)
		));

	}

    public function indexAction()
	{
	}

	private function getReferences($p)
	{
		$taxon=isset($p['taxon']) ? $p['taxon'] : null;
		$group=isset($p['group']) ? $p['group'] : null;
		$project=isset($p['project']) ? $p['project'] : null;

		if (empty($taxon)||empty($group)||empty($project))
			return;


		$l=$this->models->Literature2->freeQuery("
			select
				_a.*,
				_h.label as publishedin_label,
				_i.label as periodical_label

			from %PRE%literature2 _a

			right join %PRE%traits_taxon_references _ttr
				on _a.id = _ttr.reference_id 
				and _a.project_id=_ttr.project_id
				and _ttr.trait_group_id=".$group."

			left join  %PRE%literature2 _h
				on _a.publishedin_id = _h.id 
				and _a.project_id=_h.project_id

			left join %PRE%literature2 _i 
				on _a.periodical_id = _i.id 
				and _a.project_id=_i.project_id

			where
				_a.project_id=".$project." 
				and _ttr.taxon_id=".$taxon
		);
		
		foreach((array)$l as $key=>$val)
		{
			$l[$key]['authors']=$this->getReferenceAuthors(array('id'=>$val['id'],'project'=>$project));
			$l[$key]['periodical_ref']=$this->getReference(array('id'=>$val['periodical_id'],'project'=>$project));
			$l[$key]['publishedin_ref']=$this->getReference(array('id'=>$val['publishedin_id'],'project'=>$project));
		}
		
		if ( !empty($l) )
		{
			usort( $l, function($a,$b)
			{
				$aa=$bb='';
				
				foreach((array)$a['authors'] as $val)
					$aa.=$val['name'].' ';
	
				foreach((array)$b['authors'] as $val)
					$bb.=$val['name'].' ';
				
				$aa=!empty($aa) ? $aa : $a['author']; 
				$bb=!empty($bb) ? $bb : $b['author']; 
	
				return ( $aa>$bb ? 1 : ( $aa<$bb ? -1 : 0 ) );
			});
		}
		
		return $l;

	}


	private function getReferenceAuthors($p)
	{
		$id=isset($p['id']) ? $p['id'] : null;
		$project=isset($p['project']) ? $p['project'] : null;

		if (empty($id)||empty($project))
			return;
		
		$d=$this->models->Literature2Authors->freeQuery("
			select
				_a.actor_id, _b.name
	
			from %PRE%literature2_authors _a
	
			left join %PRE%actors _b
				on _a.actor_id = _b.id 
				and _a.project_id=_b.project_id
	
			where
				_a.project_id = ".$project."
				and _a.literature2_id =".$id."
			order by _b.name
		");
		
		return $d;
	}

	private function getReference($p)
	{
		$id=isset($p['id']) ? $p['id'] : null;
		$project=isset($p['project']) ? $p['project'] : null;

		if (empty($id)||empty($project))
			return;
		
		$l=$this->models->Literature2->freeQuery(
			"select
				_a.*,
				_h.label as publishedin_label,
				_i.label as periodical_label

			from %PRE%literature2 _a

			left join  %PRE%literature2 _h
				on _a.publishedin_id = _h.id 
				and _a.project_id=_h.project_id

			left join %PRE%literature2 _i 
				on _a.periodical_id = _i.id 
				and _a.project_id=_i.project_id

			where
				_a.project_id = ".$project." 
				and _a.id = ".$id
		);

		
		if ($l)
		{
			$authors=$this->models->Literature2Authors->freeQuery("
				select
					_a.actor_id, _b.name
	
				from %PRE%literature2_authors _a
	
				left join %PRE%actors _b
					on _a.actor_id = _b.id 
					and _a.project_id=_b.project_id
	
				where
					_a.project_id = ".$project."
					and _a.literature2_id =".$id."
				order by _b.name
			");
		
			$l[0]['authors']=$authors;
			
			return $l[0];
		}

	}

			


}




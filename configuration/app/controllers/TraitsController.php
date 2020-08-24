<?php /** @noinspection PhpMissingParentCallMagicInspection */
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
 	    if ($format=="Y")
		{
			$d=date_parse($date);
			if ($d['month']==0) $d['month']=1;
			if ($d['day']==0) $d['day']=1;
			$date=$d['year']."-".$d['month']."-".$d['day'];
		}
		return is_null($date) ? null : ltrim(date_format(date_create($date),$format),'0');
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

		$g=$this->models->TraitsModel->getTraitgroup(array(
			"language_id"=>$language,
			"project_id"=>$project,
			"group_id"=>$group
		));
		
		$r=$this->models->TraitsModel->getTraitsTaxonValues(array(
			"language_id"=>$language,
			"project_id"=>$project,
			"taxon_id"=>$taxon,
			"group_id"=>$group
		));
	
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
				'group'=>$g,
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

		$l=$this->models->TraitsModel->getReferences(array(
			"trait_group_id"=>$group,
			"project_id"=>$project,
			"taxon_id"=>$taxon
		));
		
		foreach((array)$l as $key=>$val)
		{
			$l[$key]['authors']=$this->getReferenceAuthors(array('id'=>$val['id'],'project'=>$project));
 			$l[$key]['periodical_ref']=$this->getReference(array('id'=>$val['periodical_id'],'project'=>$project));
			$l[$key]['publishedin_ref']=$this->getReference(array('id'=>$val['publishedin_id'],'project'=>$project));
            $l[$key]['formatted'] = $this->formatReference($l[$key]);
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
	
				if ( $aa>$bb ) return 1;
				if ( $aa<$bb ) return -1;

				if ( $a['date']>$b['date'] ) return 1;
				if ( $a['date']<$b['date'] ) return -1;

				if ( $a['label']>$b['label'] ) return 1;
				if ( $a['label']<$b['label'] ) return -1;

				return 0;
			});
		}
		
		return $l;

	}

	private function getReferenceAuthors($p)
	{
		$literature2_id=isset($p['id']) ? $p['id'] : null;
		$project_id=isset($p['project']) ? $p['project'] : null;

		if ( empty($literature2_id) || empty($project_id) )
			return;
		
		return $this->models->TraitsModel->getReferenceAuthors(array(
			"project_id"=>$project_id,
			"literature2_id"=>$literature2_id
		));
	}

	private function getReference($p)
	{
		$id=isset($p['id']) ? $p['id'] : null;
		$project=isset($p['project']) ? $p['project'] : null;

		if ( empty($id)||empty($project) )
			return;
		
		return $this->models->TraitsModel->getReference(array(
			"project_id"=>$project,
			"id"=>$id
		));
	}

}

<?php

/*
	mysql> select distinct subject_type from rdf;
	+--------------+
	| subject_type |
	+--------------+
	| passport     |
	+--------------+
	1 row in set (0.05 sec)
	
	mysql> select distinct predicate from rdf;
	+--------------------+
	| predicate          |
	+--------------------+
	| hasReference       |
	| hasAuthor          |
	| isNsrTabpageOf     |
	| hasPublisher       |
	| publisher          |
	| uitgever           |
	| bijdrager          |
	| hasRightsStatement |
	+--------------------+
	8 rows in set (0.07 sec)
	
	mysql> select distinct object_type from rdf;
	+-------------+
	| object_type |
	+-------------+
	| reference   |
	| actor       |
	| taxon       |
	+-------------+
	3 rows in set (0.04 sec)
*/

include_once ('Controller.php');

class RdfController extends Controller
{

    public $usedModels = array(
		'rdf','actors','literature2'
    );

    public $controllerPublicName = 'RDF';

    public $usedHelpers = array(
    );

	public $cssToLoad = array(
	);

	public $jsToLoad = array('all' => array(
	));
	
    public function __construct ()
    {
        parent::__construct();
    }

    public function __destruct ()
    {
        parent::__destruct();
    }

	public function getRdfValues($subjectId)
	{
		$rdf=$this->models->Rdf->_get(array(
			'id' => array(
				'project_id'=>$this->getCurrentProjectId(),
				'subject_id'=>$subjectId,
				'predicate !='=> ''
			),
			'columns'=>'id,project_id,subject_id,subject_type,predicate,object_id,object_type'
		));

		foreach((array)$rdf as $key=>$val) {

			switch ($val['object_type']) {
				case 'actor' :
					$data=$this->models->Actors->_get(array(
						'id' => array(
							'project_id'=>$this->getCurrentProjectId(),
							'id'=>$val['object_id']
						)
					));
					break;
				case 'taxon' :
					$data=$this->models->Taxon->_get(array(
						'id' => array(
							'project_id'=>$this->getCurrentProjectId(),
							'id'=>$val['object_id']
						)
					));
					break;
				case 'reference' :
					$data=$this->models->Literature2->_get(array(
						'id' => array(
							'project_id'=>$this->getCurrentProjectId(),
							'id'=>$val['object_id']
						)
					));
					break;
				default : $data=null;
			}
			
			$rdf[$key]['data']=$data[0];
			
		}

		return $rdf;
	}
	
	public function translatePredicate($predicate)
	{
		$predicateTranslations=array(
			24 => array(
				PREDICATE_VALID_NAME=>'geldige naam',
				PREDICATE_PREFERRED_NAME=>'%se naam', // language table should have an extra adjective-column
				PREDICATE_HOMONYM=>'homoniem',
				PREDICATE_BASIONYM=>'basioniem',
				PREDICATE_SYNONYM=>'synoniem',
				PREDICATE_SYNONYM_SL=>'synoniem',
				PREDICATE_MISSPELLED_NAME=>'fout gespelde naam',
				PREDICATE_INVALID_NAME=>'ongeldige naam',
				PREDICATE_ALTERNATIVE_NAME=>'alternatieve %se naam'
			)
		);

		return isset($predicateTranslations[$this->getDefaultProjectLanguage()][$predicate]) ?
			$predicateTranslations[$this->getDefaultProjectLanguage()][$predicate] : 
			$predicate;

	}
		
	public function deleteRdfValue($p)
	{
		$subject_id = isset($p['subject_id']) ? $p['subject_id'] : null;
		$subject_type = isset($p['subject_type']) ? $p['subject_type'] : null;
		$predicate = isset($p['predicate']) ? $p['predicate'] : null;
		$object_id = isset($p['object_id']) ? $p['object_id'] : null;
		$object_type = isset($p['object_type']) ? $p['object_type'] : null;

		if (empty($subject_id) && empty($object_id)) return;

		$r=$this->models->Rdf->freeQuery("
			delete 
				from %PRE%rdf
			where
				project_id = ".$this->getCurrentProjectId()."
				".(!empty($subject_id) ? " and subject_id = ".$subject_id : "" )."
				".(!empty($subject_type) ? " and subject_type = '".$subject_type."'" : "" )."
				".(!empty($predicate) ? " and predicate = '".$predicate."'" : "" )."
				".(!empty($object_id) ? " and object_id = ".$object_id : "" )."
				".(!empty($object_type) ? " and object_type = '".$object_type."'" : "" )."
		");

		return $r;
	}
	
	public function saveRdfValue($p)
	{
		$subject_id = isset($p['subject_id']) ? $p['subject_id'] : null;
		$subject_type = isset($p['subject_type']) ? $p['subject_type'] : null;
		$predicate = isset($p['predicate']) ? $p['predicate'] : null;
		$object_id = isset($p['object_id']) ? $p['object_id'] : null;
		$object_type = isset($p['object_type']) ? $p['object_type'] : null;

		/*
		subject_type | subject_id | predicate      | object_type | object_id
		-------------+------------+----------------+-------------+-----------
		passport     |      30607 | hasReference   | reference   |      2309
		*/

		if (
			empty($subject_id) ||
			empty($subject_type) ||
			empty($predicate) ||
			empty($object_id) ||
			empty($object_type)
		) return;

		$r=$this->models->Rdf->save(array(
			'id'=>null,
			'project_id'=>$this->getCurrentProjectId(),
			'subject_id'=>$subject_id,
			'subject_type'=>$subject_type,
			'predicate'=>$predicate,
			'object_id'=>$object_id,
			'object_type'=>$object_type,
		));

		return $r;
	}
	


}
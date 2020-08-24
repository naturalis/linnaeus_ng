<?php /** @noinspection PhpMissingParentCallMagicInspection */

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
		'rdf',
        'actors',
        'literature2'
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
					$data=$this->models->Taxa->_get(array(
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

	public function translatePredicate($predicate,$removelanguageparam=false)
	{

		$predicateTranslations=array(
			24 => array(
				PREDICATE_VALID_NAME=>'geldige naam',
				PREDICATE_PREFERRED_NAME=>'%se voorkeursnaam', // language table should have an extra adjective-column REFAC2015
				PREDICATE_HOMONYM=>'homoniem',
				PREDICATE_BASIONYM=>'basioniem',
				PREDICATE_SYNONYM=>'synoniem',
				PREDICATE_SYNONYM_SL=>'synoniem sensu lato',
				PREDICATE_MISSPELLED_NAME=>'foutieve spelling',
				PREDICATE_INVALID_NAME=>'ongeldige naam',
				PREDICATE_ALTERNATIVE_NAME=>'alternatieve %se naam',
			    PREDICATE_NOMEN_NUDEM => 'nomen nudum',
			    PREDICATE_MISIDENTIFICATION => 'foutieve identificatie'
			),
			26 => array(
				PREDICATE_VALID_NAME=>'valid name',
				PREDICATE_PREFERRED_NAME=>'preferred %s name', // language table should have an extra adjective-column REFAC2015
				PREDICATE_HOMONYM=>'homonym',
				PREDICATE_BASIONYM=>'basionym',
				PREDICATE_SYNONYM=>'synonym',
				PREDICATE_SYNONYM_SL=>'synoniem sensu lato',
				PREDICATE_MISSPELLED_NAME=>'misspelled name',
				PREDICATE_INVALID_NAME=>'invalid name',
			    PREDICATE_ALTERNATIVE_NAME=>'alternative %s name',
			    PREDICATE_NOMEN_NUDEM => 'nomen nudum',
			    PREDICATE_MISIDENTIFICATION => 'misidentification'
			)
		);

		$d=isset($predicateTranslations[$this->getDefaultProjectLanguage()][$predicate]) ?
			$predicateTranslations[$this->getDefaultProjectLanguage()][$predicate] :
			$predicate;

		if ($removelanguageparam && $this->getDefaultProjectLanguage()==24) $d=str_replace('%se ','',$d);
		if ($removelanguageparam && $this->getDefaultProjectLanguage()==26) $d=str_replace('%s ','',$d);

		return $d;
	}

	public function deleteRdfValue($p)
	{
		$subject_id = isset($p['subject_id']) ? $p['subject_id'] : null;
		$subject_type = isset($p['subject_type']) ? $p['subject_type'] : null;
		$predicate = isset($p['predicate']) ? $p['predicate'] : null;
		$object_id = isset($p['object_id']) ? $p['object_id'] : null;
		$object_type = isset($p['object_type']) ? $p['object_type'] : null;

		if (empty($subject_id) && empty($object_id)) return;
		
		$d['project_id']=$this->getCurrentProjectId();

		if (!empty($subject_id)) $d['subject_id']=$subject_id;
		if (!empty($subject_type)) $d['subject_type']=$subject_type;
		if (!empty($predicate)) $d['predicate']=$predicate;
		if (!empty($object_id)) $d['object_id']=$object_id;
		if (!empty($object_type)) $d['object_type']=$object_type;

		$before=$this->models->Rdf->_get( [ 'id' => $d ] );
		$r=$this->models->Rdf->delete( $d );
		
		$this->logChange( [ 'note'=>sprintf('Deleted RDF value (%s)',$predicate),'before'=>$before ] );

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

		$this->logChange( [ 'note'=>sprintf('Saved RDF value (%s)',$predicate),'after'=>$p ] );

		return $r;
	}



}
<?php 
include_once ('Controller.php');

class RdfController extends Controller
{

    public $usedModels = array(
		'rdf','actors','literature2',
    );

    public $controllerPublicName = 'RDF';
    public $modelNameOverride = 'Literature2Model';

    public $usedHelpers = array(
    );

	public $cssToLoad = array(
	);

	public $jsToLoad = array('all' => array(
	));
	
    public function __construct ( $p=null )
    {
        parent::__construct( $p );
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
				    /*
					$data=$this->models->Literature2->_get(array(
						'id' => array(
							'project_id'=>$this->getCurrentProjectId(),
							'id'=>$val['object_id']
						)
					));

					if (isset($data[0]['actor_id']))
					{
						$actor=$this->models->Actors->_get(array(
							'id' => array(
								'project_id'=>$this->getCurrentProjectId(),
								'id'=>$data[0]['actor_id']
							)
						));
						unset($actor[0]['created']);
						unset($actor[0]['last_change']);
						$data[0]['actor']=$actor[0];
					}
				    */

                    $data[0] = $this->models->Literature2Model->getReference([
                        'project_id' => $this->getCurrentProjectId(),
                        'language_id' => $this->getCurrentLanguageId(),
                        'literature2_id' => $val['object_id']
                    ]);

                    $data[0]['formatted'] = $this->formatReference($data[0]);

					break;
				default : $data=null;
			}

			unset($data[0]['created']);
			unset($data[0]['last_change']);
			
			$rdf[$key]['data']=$data[0];
			
		}

		return $rdf;
	}
	
	public function translatePredicate($predicate)
	{
		$predicateTranslations=array(
			24 => array(
				PREDICATE_VALID_NAME=>'geldige naam',
				PREDICATE_PREFERRED_NAME=>'%se naam', // REFAC2015: language table should have an extra adjective-column
				PREDICATE_HOMONYM=>'homoniem',
				PREDICATE_BASIONYM=>'basioniem',
				PREDICATE_SYNONYM=>'synoniem',
				PREDICATE_SYNONYM_SL=>'synoniem sensu lato',
				PREDICATE_MISSPELLED_NAME=>'fout gespelde naam',
				PREDICATE_INVALID_NAME=>'ongeldige naam',
				PREDICATE_ALTERNATIVE_NAME=>'alternatieve %se naam',
			    PREDICATE_NOMEN_NUDEM => 'nomen nudum',
			    PREDICATE_MISIDENTIFICATION => 'foutieve identificatie',
			),
			26 => array(
				PREDICATE_VALID_NAME=>'valid name',
				PREDICATE_PREFERRED_NAME=>'%s name', // REFAC2015: language table should have an extra adjective-column
				PREDICATE_HOMONYM=>'homonym',
				PREDICATE_BASIONYM=>'basionym',
				PREDICATE_SYNONYM=>'synonym',
				PREDICATE_SYNONYM_SL=>'synonym sensu lato',
				PREDICATE_MISSPELLED_NAME=>'misspelled name',
				PREDICATE_INVALID_NAME=>'invalid name',
				PREDICATE_ALTERNATIVE_NAME=>'alternative %s name',
			    PREDICATE_NOMEN_NUDEM => 'nomen nudum',
			    PREDICATE_MISIDENTIFICATION => 'misidentification'
			)
		);

		return isset($predicateTranslations[$this->getCurrentLanguageId()][$predicate]) ?
			$predicateTranslations[$this->getCurrentLanguageId()][$predicate] : 
			$predicate;

	}

}
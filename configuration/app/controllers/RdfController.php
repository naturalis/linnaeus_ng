<?php

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
				'subject_id'=>$subjectId
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
				PREDICATE_ALTERNATIVE_NAME=>'alternatieve naam'
			)
		);

		return isset($predicateTranslations[$this->getCurrentLanguageId()][$predicate]) ?
			$predicateTranslations[$this->getCurrentLanguageId()][$predicate] : 
			$predicate;

	}
		


}
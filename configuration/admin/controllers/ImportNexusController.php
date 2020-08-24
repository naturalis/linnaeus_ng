<?php 

/*

	NEXUS IMPORT

	while exporting from L2, choose the version with tabs, not the "standard"

	multiple states are assigned in the matrix as {023} for 0,2,3. i have no idea what
	happens when characters have more than 9 states. the import simply assumes
	it doesn't happen.

	also, in the header:
	FORMAT MISSING=?  GAP=- SYMBOLS= " 0 1 2 3";
	is ignored. the application uses ? as symbol for missing values (hardcoded).

	for cleaning up names before resolving (reducing "Genus Flickingeria" to "Flickingeria")
	program blindly assumes english ranks names. ranks are not checked to be valid when names are
	found. obviously, the taxa already need to exist to be found! the import does NOT create
	any taxa. if resolvement fails, the user is notified and the taxon is ignored.

	there is no check if there is another matrix with the same name (but import will work nonetheless)

*/


include_once ('ImportController.php');
class ImportNexusController extends Controller
{
	
	public $controllerPublicName="NexusImport ";
	public $controllerPublicNameMask="Matrix key";

	public function __construct ()
    {
        parent::__construct();
    }


    public function __destruct ()
    {
        parent::__destruct();
		$this->UserRights->setRequiredLevel( ID_ROLE_LEAD_EXPERT );
    }

    public function indexAction ()
    {
		define('NEXUS_START_TAG','#NEXUS');
		define('NEXUS_DEFAULT_CHARTYPE','text');


        $this->checkAuthorisation();

        $this->setPageName($this->translate('Nexus import'));

        if ($this->requestDataFiles) {

			$buffer=trim(file_get_contents($this->requestDataFiles[0]["tmp_name"]));

			if (substr($buffer,0,strlen(NEXUS_START_TAG))!==NEXUS_START_TAG) {

				$this->addError('Not a valid nexus-file (files lacks start-tag "'.NEXUS_START_TAG.'")');

			} else {

				$matrixname = ucwords(basename(strtolower($this->requestDataFiles[0]["name"]),'.nex'));

				$d=preg_split('/(BEGIN DATA;)/s',$buffer); // 0: header block, 1 data

				$data=trim($d[1]);
				$h=preg_split('/\n/',$data);
				$dimensions=$format=null;
				foreach($h as $val) {
					$matches=array();
					if (strpos($val,'DIMENSIONS')===0) {
						preg_match('/NTAX=(\d+)/',$val,$m1);
						preg_match('/NCHAR=(\d+)/',$val,$m2);
						$dimensions=array('taxa'=>$m1[1],'characters'=>$m2[1]);
					} else
					if (strpos($val,'FORMAT')===0) {
						//FORMAT MISSING=?  GAP=- SYMBOLS= " 0 1 2 3";
						$format='not implemented';
					}
				}

				//charlabels
				preg_match('/(CHARLABELS)(.+?)(;)/s',$data,$matches);
				$d=$matches[0];
				$d=preg_split('/\n/',$d);
				foreach((array)$d as $val) {
					$val=trim($val);
					if (strlen($val)==0 || strpos($val,'[')===false || strpos($val,']')===false)
						continue;
					$val=explode(' ',trim(preg_replace(array('/\s/','/(\[|\])/'),array(' ',''),$val)));
					$charlabels[(int)trim($val[0])]=str_replace('_',' ',$val[1]);
				}

				//statelabels
				preg_match('/(STATELABELS)(.+?)(;)(.+)(MATRIX)/s',$data,$matches);
				$d=$matches[0];
				$d=preg_split('/\n/',$d);
				$prev=null;
				foreach((array)$d as $val) {
					$val=trim($val);
					if (strlen($val)==0 || strpos($val,'[')===false || strpos($val,']')===false)
						continue;
					$val=explode(' ',trim(preg_replace(array('/\s/','/(\[|\])/'),array(' ',''),trim($val))));

					if (count($val)==3) {
						$prev=$charid=$val[0];
						$stateid=$val[1];
						$value=$val[2];
					} else
					if (count($val)==2) {
						$charid=$prev;
						$stateid=$val[0];
						$value=$val[1];
					}
					else
						continue;

					$statelabels[$charid][$stateid]=rtrim(str_replace('_',' ',$value),',');
				}

				//matrix
				preg_match('/(MATRIX)(.+?)(ENDBLOCK)/s',$data,$matches);
				$d=trim($matches[2]);
				$d=preg_split('/\n/',$d);
				$charindex=explode(' ',preg_replace(array('/\[CHARACTERS(\s+)/','/(\s+)/'),array('',' '),trim($d[0])));
				$taxa=array();
				$dCount = count($d);
				for($i=2;$i<$dCount;$i++) {
					$val=trim($d[$i]);
					if (strpos($val,';')!==false)
						continue;
					$states=array();
					$boom=explode(' ',preg_replace('/(\s+)/',' ',$val));
					$slice=array_slice($boom,1);
					foreach($slice as $key=>$val) {
						if (strpos($val,'{')!==false || strpos($val,'}')!==false) {
							$val=str_split(trim($val,'{}'));
						} else {
							$val=(array)$val;
						}
						$states[$charindex[$key]]=$val;
					}
					$taxa[] = array('label'=>str_replace('_',' ',$boom[0]),'states'=> $states);
				}

				if ($dimensions['taxa']!=count((array)$taxa))
					$this->addError('Number of actual taxa does not match header ('.count((array)$taxa).' vs '.$dimensions['taxa'].')');

				if ($dimensions['characters']!=count((array)$charlabels))
					$this->addError('Number of actual characters does not match header ('.count((array)$charlabels).' vs '.$dimensions['characters'].')');

				$mId=$this->createMatrix(array('matrixname'=>$matrixname));
				$this->addMessage('Created matrix "'.$matrixname.'"');
				$this->smarty->assign('mId',$mId);

				//saving characters
				$showOrder=0;
				foreach ((array) $charlabels as $cKey => $cVal) {

					$id=$this->createMatrixCharacter(
						array(
							'type'=>NEXUS_DEFAULT_CHARTYPE,
							'label'=>$cVal,
							'matrix_id'=>$mId,
							'showOrder'=>$show_order++
						)
					);

					$charlabels[$cKey] = array('id'=>$id,'label'=>$cVal);

				}

				//saving states
				foreach ((array)$statelabels as $lKey => $statelabel) {

					foreach ((array)$statelabel as $sKey => $sVal) {

						$this->models->CharacteristicsStates->save(
						array(
							'id' => null,
							'project_id' => $this->getCurrentProjectId(),
							'characteristic_id' => $charlabels[$lKey]['id'],
							'got_labels' => 1
						));

						$statelabels[$lKey][$sKey]=array('id'=>$this->models->CharacteristicsStates->getNewId(),'label'=>$sVal);

						$this->models->CharacteristicsLabelsStates->save(
						array(
							'id' => null,
							'project_id' => $this->getCurrentProjectId(),
							'state_id' => $statelabels[$lKey][$sKey]['id'],
							'language_id' => $this->getDefaultProjectLanguage(),
							'label' => $sVal
						));

					}

				}

				//resolving & connecting taxa
				$ranks=array();
				$pr=$this->newGetProjectRanks();
				foreach((array)$pr as $val)
					$ranks[]=$val['rank'];

				foreach((array)$taxa as $key=>$val) {

					$t=$this->getTaxonByName($val['label']);
					if (empty($t)) {
						$t=$this->getTaxonByName(str_replace($ranks,'',$val['label']));
						if (empty($t)) {
							$this->addError('taxon "'.$val['label'].'" could not be resolved (not saved).');
							continue;
						}
					}

					$taxonId=$t['id'];

					$this->models->MatricesTaxa->save(
					array(
						'id' => null,
						'project_id' => $this->getCurrentProjectId(),
						'matrix_id' => $mId,
						'taxon_id' => $taxonId
					));

					foreach((array)$val['states'] as $sKey=>$sVal) {

						foreach((array)$sVal as $tKey=>$tVal) {

							if ($tVal=='?') //missing value
								continue;

							$this->models->MatricesTaxaStates->setNoKeyViolationLogging(true);

							$cId=$charlabels[$sKey]['id'];
							$sId=$statelabels[$sKey][$tVal]['id'];

							$this->models->MatricesTaxaStates->save(
							array(
								'id' => null,
								'project_id' => $this->getCurrentProjectId(),
								'matrix_id' => $mId,
								'characteristic_id' => $cId,
								'state_id' => $sId,
								'taxon_id' => $taxonId
							));

						}

					}

					$this->addMessage('Saved states for taxon "'.$val['label'].'".');

				}

			}

		}

        $this->printPage('nexus_import');

    }

}

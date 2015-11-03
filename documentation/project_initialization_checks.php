-- INIT --
DICH KEY
make sure there are actual endpoints for the dich key to point to (not lethalm by the way)



-- FINAL --
DICH KEY
cleanup
store key tree

MATRIX KEY
acquire state image dimensions (l2 only)




-- SCRATCHPAD BELOW --

check for <!-- REFACNOW --> !!!
new SESSION HANDLER!


	public $usedHelpers = array(
		'session_module_settings',
    );

private $moduleSession;

$this->moduleSession=$this->helpers->SessionModuleSettings;
$this->moduleSession->setModule( array('environment'=>'admin','controller'=>$this->controllerBaseName) );
$this->moduleSession->getModuleSetting( 'suppressTaxonDivision' );
$this->moduleSession->setModuleSetting( array('setting'=>'suppressTaxonDivision','value'=>$state ) );



include_once ('ModuleSettingsReaderController.php');


$this->moduleSettings=new ModuleSettingsReaderController;
$this->moduleSettings->setUseDefaultWhenNoValue( true );
$this->moduleSettings->assignModuleSettings( $this->settings );
$this->moduleSettings->getModuleSetting('use_character_groups'); 
$this->moduleSettings->getModuleSetting(array('module'=>'species','setting'=>'use_variations'));
$this->moduleSettings->getModuleSetting(array('setting'=>'use_character_groups','subst'=>$substvalue)); 

rememeber!
http://localhost/linnaeus_ng/app/views/matrixkey/use_matrix.php?p=3&id=5


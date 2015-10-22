-- INIT --
DICH KEY
make sure there are actual endpoints for the dich key to point to (not lethalm by the way)



-- FINAL --
DICH KEY
cleanup
store key tree





-- SCRATCHPAD BELOW --

check for <!-- REFACNOW --> !!!
new SESSION HANDLER!


private $moduleSession;

$this->moduleSession=$this->helpers->SessionModuleSettings;
$this->moduleSession->setModule( array('environment'=>'admin','controller'=>$this->controllerBaseName) );

$this->moduleSession->getModuleSetting( 'suppressTaxonDivision' )

$this->moduleSession->setModuleSetting( array('setting'=>'suppressTaxonDivision','value'=>$state ) );

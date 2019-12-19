<?php

require_once ('../../../../configuration/app/controllers/UtilitiesController.php');

$c = new UtilitiesController(array('checkForProjectId'=>false));

$c->setStoreHistory(false);

$c->dynamicCssAction();

/*

	see
	linnaeus_ng\www\app\templates\templates\utilities\dynamic-css.tpl
	for actual css code

*/
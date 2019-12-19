<?php

require_once ('../../../../configuration/app/controllers/UtilitiesController.php');

$c = new UtilitiesController(array('checkForProjectId'=>false));

$c->setStoreHistory(false);

$c->ajaxInterfaceAction();


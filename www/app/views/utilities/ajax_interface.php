<?php

require_once ('../../../../configuration/app/controllers/UtilitiesController.php');

$c = new UtilitiesController();

$c->setStoreHistory(false);

$c->ajaxInterfaceAction();


<?php

require_once ('../../../../configuration/app/controllers/SearchControllerGeneral.php');

$c = new SearchControllerGeneral();

$c->setStoreHistory(false);

$c->ajaxInterfaceAction();

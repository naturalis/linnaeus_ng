<?php

require_once ('../../../../configuration/app/controllers/FreeModuleController.php');

$c = new FreeModuleController();

$c->setStoreHistory(false);

$c->ajaxInterfaceAction();

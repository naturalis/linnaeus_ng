<?php

require_once ('../../../../configuration/app/controllers/ModuleController.php');

$c = new ModuleController();

$c->setStoreHistory(false);

$c->ajaxInterfaceAction();

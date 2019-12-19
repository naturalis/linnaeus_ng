<?php

require_once ('../../../../configuration/app/controllers/TreeController.php');

$c = new TreeController();

$c->setStoreHistory(false);

$c->ajaxInterfaceAction();

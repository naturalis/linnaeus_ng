<?php

require_once ('../../../../configuration/app/controllers/KeyController.php');

$c = new KeyController();

$c->setStoreHistory(false);

$c->ajaxInterfaceAction();

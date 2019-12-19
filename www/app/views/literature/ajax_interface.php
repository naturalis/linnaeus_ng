<?php

require_once ('../../../../configuration/app/controllers/LiteratureController.php');

$c = new LiteratureController();

$c->setStoreHistory(false);

$c->ajaxInterfaceAction();

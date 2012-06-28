<?php

require_once ('../../../../configuration/app/controllers/SearchController.php');

$c = new SearchController();

$c->setStoreHistory(false);

$c->ajaxInterfaceAction();

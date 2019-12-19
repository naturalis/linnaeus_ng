<?php

require_once ('../../../../configuration/app/controllers/SearchControllerNSR.php');

$c = new SearchControllerNSR();

$c->setStoreHistory(false);

$c->ajaxInterfaceAction();

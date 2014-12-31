<?php

require_once ('../../../../configuration/app/controllers/SpeciesControllerNSR.php');

$c = new SpeciesControllerNSR();

$c->setStoreHistory(false);

$c->ajaxInterfaceAction();

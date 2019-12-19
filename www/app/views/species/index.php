<?php

//require_once ('../../../../configuration/app/controllers/SpeciesController.php');
//$c = new SpeciesController();

require_once ('../../../../configuration/app/controllers/SpeciesControllerNSR.php');

$c = new SpeciesControllerNSR();

$c->indexAction();

<?php

require_once ('../../../../configuration/app/controllers/SpeciesController.php');

$c = new SpeciesController();

$c->setTaxonType('higher');

$c->setStoreHistory(false);

$c->ajaxInterfaceAction();

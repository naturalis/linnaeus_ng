<?php

require_once ('../../../../configuration/app/controllers/TraitsController.php');

$c = new TraitsController(array('checkForProjectId'=>false));

$c->getAction();

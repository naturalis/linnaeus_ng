<?php

require_once ('../../../../configuration/app/controllers/WebservicesController.php');

$c = new WebservicesController(array('checkForProjectId'=>false));

$c->namesAction();


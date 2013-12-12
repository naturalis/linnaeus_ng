<?php

require_once ('../../../../configuration/app/controllers/LoaderController.php');

$c = new LoaderController(array('checkForSplash'=>false));


$c->splashAction();

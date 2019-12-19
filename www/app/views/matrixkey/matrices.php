<?php

require_once ('../../../../configuration/app/controllers/MatrixKeyController.php');
//require_once ('../../../../configuration/app/controllers/MatrixKeyControllerWeb.php');

$c = new MatrixKeyController();
//$c = new MatrixKeyControllerWeb();

$c->matricesAction();

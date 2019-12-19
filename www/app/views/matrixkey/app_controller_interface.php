<?php

require_once ('../../../../configuration/app/controllers/MatrixKeyAppController.php');
//require_once ('../../../../configuration/app/controllers/MatrixKeyControllerWeb.php');

$c = new MatrixKeyAppController();
//$c = new MatrixKeyControllerWeb();

$c->setStoreHistory(false);

$c->appControllerInterfaceAction();

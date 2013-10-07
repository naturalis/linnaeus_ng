<?php

require_once ('../../../../configuration/admin/controllers/UtilitiesController.php');

$c = new UtilitiesController();

if (!$c->isUserLoggedIn()) {
    
    $c->redirect('index.php');

} else {

    $c->controllerPublicName = '';
    
    $c->adminIndexAction();

}


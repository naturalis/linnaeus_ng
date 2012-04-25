<?php

/*
    
	because this file is outside the "views" directory, some logic is required here.
    
*/

require_once ('../../configuration/admin/controllers/ProjectsController.php');

$c = new ProjectsController();

if (!$c->isUserLoggedIn()) {
    
    $c->redirect('index.php');

} else {

    $c->controllerPublicName = '';
    
    $c->adminIndexAction();

}


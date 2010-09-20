<?php

/*
	
		because this file is outside the "views" directory, some logic is required here.
	
	*/

require_once ('../../configuration/admin/controllers/UsersController.php');

$c = new UsersController();

if (!$c->isUserLoggedIn()) {
    
    $c->redirect('index.php');

}
else {
    
    $c->controllerPublicName = '';
    
    $c->printPage();

}


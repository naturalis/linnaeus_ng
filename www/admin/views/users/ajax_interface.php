<?php

	// AJAX interface

	require_once('../../../../configuration/admin/controllers/UsersController.php');

	$c = new UsersController();

	$c->ajaxInterfaceAction();

?>
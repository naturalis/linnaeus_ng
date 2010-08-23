<?php

	require_once('../../../../configuration/admin/controllers/UsersController.php');

	$v = new UsersController();

	$v->createAction();

	$v->printPage();

?>
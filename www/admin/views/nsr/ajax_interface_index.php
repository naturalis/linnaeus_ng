<?php

	require_once ('../../../../configuration/admin/controllers/VersatileExportController.php');

	$c = new VersatileExportController();
	$c->setExcludeFromReferer(true);
	$c->setNoResubmitvalReset(true);
	$c->ajaxInterfaceAction();
	
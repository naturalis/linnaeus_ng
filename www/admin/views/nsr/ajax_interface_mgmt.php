<?php

	require_once ('../../../../configuration/admin/controllers/NsrTaxonManagement.php');

	$c = new NsrTaxonManagement();
	$c->setExcludeFromReferer(true);
	$c->setNoResubmitvalReset(true);
	$c->ajaxInterfaceAction();
	
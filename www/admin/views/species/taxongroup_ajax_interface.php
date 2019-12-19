<?php

require_once ('../../../../configuration/admin/controllers/TaxongroupController.php');

$c = new TaxongroupController();

$c->setExcludeFromReferer(true);

$c->setNoResubmitvalReset(true);

$c->ajaxInterfaceAction();


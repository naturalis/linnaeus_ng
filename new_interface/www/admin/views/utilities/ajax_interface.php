<?php

require_once ('../../../../configuration/admin/controllers/UtilitiesController.php');

$c = new UtilitiesController();

$c->setExcludeFromReferer(true);

$c->setNoResubmitvalReset(true);

$c->ajaxInterfaceAction();


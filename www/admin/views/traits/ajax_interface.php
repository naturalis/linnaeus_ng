<?php

require_once ('../../../../configuration/admin/controllers/TraitsController.php');

$c = new TraitsController();

$c->setExcludeFromReferer(true);

$c->setNoResubmitvalReset(true);

$c->ajaxInterfaceAction();


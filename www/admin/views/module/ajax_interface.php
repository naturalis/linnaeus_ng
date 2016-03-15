<?php

require_once ('../../../../configuration/admin/controllers/FreeModuleController.php');

$c = new FreeModuleController();

$c->setExcludeFromReferer(true);

$c->setNoResubmitvalReset(true);

$c->ajaxInterfaceAction();


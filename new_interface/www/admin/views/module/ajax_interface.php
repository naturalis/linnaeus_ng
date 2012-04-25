<?php

require_once ('../../../../configuration/admin/controllers/ModuleController.php');

$c = new ModuleController();

$c->setExcludeFromReferer(true);

$c->setNoResubmitvalReset(true);

$c->ajaxInterfaceAction();


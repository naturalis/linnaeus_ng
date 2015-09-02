<?php

require_once ('../../../../configuration/admin/controllers/ModuleSettingsController.php');

$c = new ModuleSettingsController();

$c->setExcludeFromReferer(true);

$c->setNoResubmitvalReset(true);

$c->ajaxInterfaceAction();


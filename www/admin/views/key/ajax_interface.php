<?php

require_once ('../../../../configuration/admin/controllers/KeysController.php');

$c = new KeysController();

$c->setExcludeFromReferer(true);

$c->setNoResubmitvalReset(true);

$c->ajaxInterfaceAction();


<?php

require_once ('../../../../configuration/admin/controllers/KeyController.php');

$c = new KeyController();

$c->setExcludeFromReferer(true);

$c->setNoResubmitvalReset(true);

$c->ajaxInterfaceAction();


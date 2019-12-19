<?php

require_once ('../../../../configuration/admin/controllers/NsrTreeController.php');

$c = new NsrTreeController();

$c->setExcludeFromReferer(true);

$c->setNoResubmitvalReset(true);

$c->ajaxInterfaceAction();


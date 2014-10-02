<?php

require_once ('../../../../configuration/admin/controllers/TreeController.php');

$c = new TreeController();

$c->setExcludeFromReferer(true);

$c->setNoResubmitvalReset(true);

$c->ajaxInterfaceAction();


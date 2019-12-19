<?php

require_once ('../../../../configuration/admin/controllers/IndexController.php');

$c = new IndexController();

$c->setExcludeFromReferer(true);

$c->setNoResubmitvalReset(true);

$c->ajaxInterfaceAction();


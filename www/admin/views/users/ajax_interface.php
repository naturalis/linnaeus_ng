<?php

require_once ('../../../../configuration/admin/controllers/UsersController.php');

$c = new UsersController();

$c->setExcludeFromReferer(true);

$c->setNoResubmitvalReset(true);

$c->ajaxInterfaceAction();


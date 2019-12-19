<?php

require_once ('../../../../configuration/admin/controllers/Literature2Controller.php');

$c = new Literature2Controller();

$c->setExcludeFromReferer(true);

$c->setNoResubmitvalReset(true);

$c->ajaxInterfaceAction();

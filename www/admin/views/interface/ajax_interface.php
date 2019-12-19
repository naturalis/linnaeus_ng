<?php

require_once ('../../../../configuration/admin/controllers/InterfaceController.php');

$c = new InterfaceController();

$c->setExcludeFromReferer(true);

$c->setNoResubmitvalReset(true);

$c->ajaxInterfaceAction();


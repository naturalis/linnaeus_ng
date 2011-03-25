<?php

require_once ('../../../../configuration/admin/controllers/MapKeyController.php');

$c = new MapKeyController();

$c->setExcludeFromReferer(true);

$c->setNoResubmitvalReset(true);

$c->ajaxInterfaceAction();

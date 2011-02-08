<?php

require_once ('../../../../configuration/admin/controllers/ContentController.php');

$c = new ContentController();

$c->setExcludeFromReferer(true);

$c->setNoResubmitvalReset(true);

$c->ajaxInterfaceAction();


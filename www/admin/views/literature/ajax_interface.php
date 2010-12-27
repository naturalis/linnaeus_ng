<?php

require_once ('../../../../configuration/admin/controllers/LiteratureController.php');

$c = new LiteratureController();

$c->setExcludeFromReferer(true);

$c->setNoResubmitvalReset(true);

$c->ajaxInterfaceAction();

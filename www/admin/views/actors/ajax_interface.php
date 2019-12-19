<?php

require_once ('../../../../configuration/admin/controllers/ActorsController.php');

$c = new ActorsController();

$c->setExcludeFromReferer(true);

$c->setNoResubmitvalReset(true);

$c->ajaxInterfaceAction();

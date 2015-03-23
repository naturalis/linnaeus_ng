<?php

require_once ('../../../../configuration/admin/controllers/TraitsTraitsController.php');

$c = new TraitsTraitsController();

$c->setExcludeFromReferer(true);

$c->setNoResubmitvalReset(true);

$c->ajaxInterfaceAction();


<?php

require_once ('../../../../configuration/admin/controllers/SearchController.php');

$c = new SearchController();

$c->setExcludeFromReferer(true);

$c->setNoResubmitvalReset(true);

$c->ajaxInterfaceAction();


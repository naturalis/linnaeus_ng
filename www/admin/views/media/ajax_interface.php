<?php

require_once ('../../../../configuration/admin/controllers/MediaController.php');

$c = new MediaController();

$c->setExcludeFromReferer(true);

$c->setNoResubmitvalReset(true);

$c->ajaxInterfaceAction();


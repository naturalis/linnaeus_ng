<?php

require_once ('../../../../configuration/admin/controllers/IntroductionController.php');

$c = new IntroductionController();

$c->setExcludeFromReferer(true);

$c->setNoResubmitvalReset(true);

$c->ajaxInterfaceAction();


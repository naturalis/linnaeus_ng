<?php

require_once ('../../../../configuration/app/controllers/IntroductionController.php');

$c = new IntroductionController();

$c->setStoreHistory(false);

$c->ajaxInterfaceAction();

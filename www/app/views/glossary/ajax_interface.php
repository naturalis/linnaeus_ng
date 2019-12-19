<?php

require_once ('../../../../configuration/app/controllers/GlossaryController.php');

$c = new GlossaryController();

$c->setStoreHistory(false);

$c->ajaxInterfaceAction();

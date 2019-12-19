<?php

require_once ('../../../../configuration/app/controllers/LinnaeusController.php');

$c = new LinnaeusController();

$c->setStoreHistory(false);

$c->ajaxInterfaceAction();

<?php

require_once ('../../../../configuration/admin/controllers/MatrixKeyExtController.php');

$c = new MatrixKeyExtController();

$c->setExcludeFromReferer(true);

$c->setNoResubmitvalReset(true);

$c->ajaxInterfaceAction();

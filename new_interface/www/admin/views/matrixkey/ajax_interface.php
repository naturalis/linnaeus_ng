<?php

require_once ('../../../../configuration/admin/controllers/MatrixKeyController.php');

$c = new MatrixKeyController();

$c->setExcludeFromReferer(true);

$c->setNoResubmitvalReset(true);

$c->ajaxInterfaceAction();

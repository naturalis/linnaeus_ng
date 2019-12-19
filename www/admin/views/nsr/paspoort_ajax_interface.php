<?php

require_once ('../../../../configuration/admin/controllers/NsrPaspoortController.php');

$c = new NsrPaspoortController();

$c->setExcludeFromReferer(true);

$c->setNoResubmitvalReset(true);

$c->ajaxInterfaceAction();


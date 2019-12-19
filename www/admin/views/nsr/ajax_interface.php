<?php

require_once ('../../../../configuration/admin/controllers/NsrTaxonController.php');

$c = new NsrTaxonController();

$c->setExcludeFromReferer(true);

$c->setNoResubmitvalReset(true);

$c->ajaxInterfaceAction();


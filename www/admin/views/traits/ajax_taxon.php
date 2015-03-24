<?php

require_once ('../../../../configuration/admin/controllers/TraitsTaxonController.php');

$c = new TraitsTaxonController();

$c->setExcludeFromReferer(true);

$c->setNoResubmitvalReset(true);

$c->ajaxInterfaceAction();


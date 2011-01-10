<?php

require_once ('../../../../configuration/admin/controllers/GlossaryController.php');

$c = new GlossaryController();

$c->setExcludeFromReferer(true);

$c->setNoResubmitvalReset(true);

$c->ajaxInterfaceAction();


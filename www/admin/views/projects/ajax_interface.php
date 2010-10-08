<?php

require_once ('../../../../configuration/admin/controllers/ProjectsController.php');

$c = new ProjectsController();

$c->setExcludeFromReferer(true);

$c->ajaxInterfaceAction();


<?php

require_once ('../../../../configuration/app/controllers/LinnaeusController.php');

$c = new LinnaeusController(array('checkForSplash'=>false,'checkForProjectId'=>false));

$c->setProjectAction();

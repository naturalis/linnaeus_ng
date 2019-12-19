<?php

require_once ('../../../../configuration/app/controllers/LinnaeusController.php');

$c = new LinnaeusController(array('checkForProjectId'=>false));

$c->noProjectAction();

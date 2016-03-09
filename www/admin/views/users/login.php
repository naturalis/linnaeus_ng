<?php

require_once ('../../../../configuration/admin/controllers/LoginController.php');

$c = new LoginController();

$c->setExcludeFromReferer(true);

$c->loginAction();
    

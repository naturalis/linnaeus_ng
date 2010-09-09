<?php

    include_once('model.php');

    class Role extends Model {

        const tableBaseName = 'roles';

        /**
        * Constructor, calls parent's constructor
        *
        * @access     public
        */
        public function __construct() {

            parent::__construct(self::tableBaseName);

        }

        /**
        * Destructor
        *
        * @access     public
        */
        public function __destruct() {

            parent::__destruct();

        }

    }


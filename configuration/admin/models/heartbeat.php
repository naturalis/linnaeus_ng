<?php

    include_once('model.php');

    class Heartbeat extends Model {

        const tableBaseName = 'heartbeats';

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


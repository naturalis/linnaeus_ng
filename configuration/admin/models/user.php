<?php

    include_once('model.php');

    class User extends Model {

        const tableBaseName = 'users';

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
        
        /**
        * Function cleans up user data before insertion
        *
        * Currently only trims all data, and sets email address to lowercase
        *
        * @access     public
        */
        public function sanatizeData($data) {

            if (isset($data['email_address'])) {

                $data['email_address'] = strtolower($data['email_address']);

            }

            foreach((array)$data as $key => $val) {

                $data[$key] = trim($val);

            }

            return $data;

        }

    }


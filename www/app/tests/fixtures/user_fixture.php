<?php 
/* SVN FILE: $Id$ */
/* User Fixture generated on: 2010-08-10 15:14:33 : 1281446073*/

class UserFixture extends CakeTestFixture {
	var $name = 'User';
	var $table = 'users';
	var $fields = array(
		'id' => array('type'=>'integer', 'null' => false, 'default' => NULL, 'key' => 'primary'),
		'username' => array('type'=>'string', 'null' => false, 'default' => NULL, 'length' => 24, 'key' => 'index'),
		'password' => array('type'=>'string', 'null' => false, 'default' => NULL, 'length' => 32),
		'first_name' => array('type'=>'string', 'null' => false, 'default' => NULL, 'length' => 64, 'key' => 'index'),
		'last_name' => array('type'=>'string', 'null' => false, 'default' => NULL, 'length' => 64),
		'organisation' => array('type'=>'string', 'null' => false, 'default' => NULL, 'length' => 64),
		'email_address' => array('type'=>'string', 'null' => false, 'default' => NULL, 'length' => 64),
		'telephone' => array('type'=>'string', 'null' => true, 'default' => NULL, 'length' => 32),
		'active' => array('type'=>'boolean', 'null' => false, 'default' => NULL),
		'first_login' => array('type'=>'datetime', 'null' => true, 'default' => NULL),
		'last_login' => array('type'=>'datetime', 'null' => true, 'default' => NULL),
		'login_number' => array('type'=>'integer', 'null' => false, 'default' => '0', 'length' => 6),
		'password_changed' => array('type'=>'datetime', 'null' => true, 'default' => NULL),
		'created' => array('type'=>'datetime', 'null' => false, 'default' => NULL),
		'updated' => array('type'=>'datetime', 'null' => true, 'default' => NULL),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1), 'username' => array('column' => array('username', 'email_address'), 'unique' => 1), 'first_name' => array('column' => array('first_name', 'last_name', 'active'), 'unique' => 0))
	);
	var $records = array(array(
		'id' => 1,
		'username' => 'Lorem ipsum dolor sit ',
		'password' => 'Lorem ipsum dolor sit amet',
		'first_name' => 'Lorem ipsum dolor sit amet',
		'last_name' => 'Lorem ipsum dolor sit amet',
		'organisation' => 'Lorem ipsum dolor sit amet',
		'email_address' => 'Lorem ipsum dolor sit amet',
		'telephone' => 'Lorem ipsum dolor sit amet',
		'active' => 1,
		'first_login' => '2010-08-10 15:14:33',
		'last_login' => '2010-08-10 15:14:33',
		'login_number' => 1,
		'password_changed' => '2010-08-10 15:14:33',
		'created' => '2010-08-10 15:14:33',
		'updated' => '2010-08-10 15:14:33'
	));
}
?>
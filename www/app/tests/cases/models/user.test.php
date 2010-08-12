<?php 
/* SVN FILE: $Id$ */
/* User Test cases generated on: 2010-08-10 15:14:35 : 1281446075*/
App::import('Model', 'User');

class UserTestCase extends CakeTestCase {
	var $User = null;
	var $fixtures = array('app.user');

	function startTest() {
		$this->User =& ClassRegistry::init('User');
	}

	function testUserInstance() {
		$this->assertTrue(is_a($this->User, 'User'));
	}

	function testUserFind() {
		$this->User->recursive = -1;
		$results = $this->User->find('first');
		$this->assertTrue(!empty($results));

		$expected = array('User' => array(
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
		$this->assertEqual($results, $expected);
	}
}
?>
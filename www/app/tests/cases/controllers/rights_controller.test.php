<?php 
/* SVN FILE: $Id$ */
/* RightsController Test cases generated on: 2010-08-10 15:13:29 : 1281446009*/
App::import('Controller', 'Rights');

class TestRights extends RightsController {
	var $autoRender = false;
}

class RightsControllerTest extends CakeTestCase {
	var $Rights = null;

	function startTest() {
		$this->Rights = new TestRights();
		$this->Rights->constructClasses();
	}

	function testRightsControllerInstance() {
		$this->assertTrue(is_a($this->Rights, 'RightsController'));
	}

	function endTest() {
		unset($this->Rights);
	}
}
?>
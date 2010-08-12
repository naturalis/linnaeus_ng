<?php 
/* SVN FILE: $Id$ */
/* LanguagesController Test cases generated on: 2010-08-10 15:07:53 : 1281445673*/
App::import('Controller', 'Languages');

class TestLanguages extends LanguagesController {
	var $autoRender = false;
}

class LanguagesControllerTest extends CakeTestCase {
	var $Languages = null;

	function startTest() {
		$this->Languages = new TestLanguages();
		$this->Languages->constructClasses();
	}

	function testLanguagesControllerInstance() {
		$this->assertTrue(is_a($this->Languages, 'LanguagesController'));
	}

	function endTest() {
		unset($this->Languages);
	}
}
?>
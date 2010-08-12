<?php 
/* SVN FILE: $Id$ */
/* Module Test cases generated on: 2010-08-10 15:09:56 : 1281445796*/
App::import('Model', 'Module');

class ModuleTestCase extends CakeTestCase {
	var $Module = null;
	var $fixtures = array('app.module');

	function startTest() {
		$this->Module =& ClassRegistry::init('Module');
	}

	function testModuleInstance() {
		$this->assertTrue(is_a($this->Module, 'Module'));
	}

	function testModuleFind() {
		$this->Module->recursive = -1;
		$results = $this->Module->find('first');
		$this->assertTrue(!empty($results));

		$expected = array('Module' => array(
			'id' => 1,
			'name' => 'Lorem ipsum dolor sit amet',
			'description' => 'Lorem ipsum dolor sit amet, aliquet feugiat. Convallis morbi fringilla gravida,phasellus feugiat dapibus velit nunc, pulvinar eget sollicitudin venenatis cum nullam,vivamus ut a sed, mollitia lectus. Nulla vestibulum massa neque ut et, id hendrerit sit,feugiat in taciti enim proin nibh, tempor dignissim, rhoncus duis vestibulum nunc mattis convallis.',
			'created' => '2010-08-10 15:09:55',
			'updated' => '2010-08-10 15:09:55'
		));
		$this->assertEqual($results, $expected);
	}
}
?>
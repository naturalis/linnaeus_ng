<?php 
/* SVN FILE: $Id$ */
/* Right Test cases generated on: 2010-08-10 15:12:53 : 1281445973*/
App::import('Model', 'Right');

class RightTestCase extends CakeTestCase {
	var $Right = null;
	var $fixtures = array('app.right');

	function startTest() {
		$this->Right =& ClassRegistry::init('Right');
	}

	function testRightInstance() {
		$this->assertTrue(is_a($this->Right, 'Right'));
	}

	function testRightFind() {
		$this->Right->recursive = -1;
		$results = $this->Right->find('first');
		$this->assertTrue(!empty($results));

		$expected = array('Right' => array(
			'id' => 1,
			'name' => 'Lorem ipsum dolor sit amet',
			'description' => 'Lorem ipsum dolor sit amet, aliquet feugiat. Convallis morbi fringilla gravida,phasellus feugiat dapibus velit nunc, pulvinar eget sollicitudin venenatis cum nullam,vivamus ut a sed, mollitia lectus. Nulla vestibulum massa neque ut et, id hendrerit sit,feugiat in taciti enim proin nibh, tempor dignissim, rhoncus duis vestibulum nunc mattis convallis.',
			'created' => '2010-08-10 15:12:52',
			'updated' => '2010-08-10 15:12:52'
		));
		$this->assertEqual($results, $expected);
	}
}
?>
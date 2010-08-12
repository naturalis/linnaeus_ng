<?php 
/* SVN FILE: $Id$ */
/* Project Test cases generated on: 2010-08-10 15:11:51 : 1281445911*/
App::import('Model', 'Project');

class ProjectTestCase extends CakeTestCase {
	var $Project = null;
	var $fixtures = array('app.project');

	function startTest() {
		$this->Project =& ClassRegistry::init('Project');
	}

	function testProjectInstance() {
		$this->assertTrue(is_a($this->Project, 'Project'));
	}

	function testProjectFind() {
		$this->Project->recursive = -1;
		$results = $this->Project->find('first');
		$this->assertTrue(!empty($results));

		$expected = array('Project' => array(
			'id' => 1,
			'name' => 'Lorem ipsum dolor sit amet',
			'description' => 'Lorem ipsum dolor sit amet, aliquet feugiat. Convallis morbi fringilla gravida,phasellus feugiat dapibus velit nunc, pulvinar eget sollicitudin venenatis cum nullam,vivamus ut a sed, mollitia lectus. Nulla vestibulum massa neque ut et, id hendrerit sit,feugiat in taciti enim proin nibh, tempor dignissim, rhoncus duis vestibulum nunc mattis convallis.',
			'organisation' => 'Lorem ipsum dolor sit amet',
			'logo' => 'Lorem ipsum dolor sit amet',
			'created' => '2010-08-10 15:11:50',
			'updated' => '2010-08-10 15:11:50'
		));
		$this->assertEqual($results, $expected);
	}
}
?>
<?php 
/* SVN FILE: $Id$ */
/* Project Fixture generated on: 2010-08-10 15:11:50 : 1281445910*/

class ProjectFixture extends CakeTestFixture {
	var $name = 'Project';
	var $table = 'projects';
	var $fields = array(
		'id' => array('type'=>'integer', 'null' => false, 'default' => NULL, 'key' => 'primary'),
		'name' => array('type'=>'string', 'null' => false, 'default' => NULL, 'length' => 64, 'key' => 'unique'),
		'description' => array('type'=>'text', 'null' => false, 'default' => NULL),
		'organisation' => array('type'=>'string', 'null' => false, 'default' => NULL, 'length' => 64, 'key' => 'index'),
		'logo' => array('type'=>'string', 'null' => false, 'default' => NULL),
		'created' => array('type'=>'datetime', 'null' => false, 'default' => NULL),
		'updated' => array('type'=>'datetime', 'null' => false, 'default' => NULL),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1), 'name' => array('column' => 'name', 'unique' => 1), 'organisation' => array('column' => 'organisation', 'unique' => 0))
	);
	var $records = array(array(
		'id' => 1,
		'name' => 'Lorem ipsum dolor sit amet',
		'description' => 'Lorem ipsum dolor sit amet, aliquet feugiat. Convallis morbi fringilla gravida,phasellus feugiat dapibus velit nunc, pulvinar eget sollicitudin venenatis cum nullam,vivamus ut a sed, mollitia lectus. Nulla vestibulum massa neque ut et, id hendrerit sit,feugiat in taciti enim proin nibh, tempor dignissim, rhoncus duis vestibulum nunc mattis convallis.',
		'organisation' => 'Lorem ipsum dolor sit amet',
		'logo' => 'Lorem ipsum dolor sit amet',
		'created' => '2010-08-10 15:11:50',
		'updated' => '2010-08-10 15:11:50'
	));
}
?>
<?php 
/* SVN FILE: $Id$ */
/* Language Fixture generated on: 2010-08-10 15:07:06 : 1281445626*/

class LanguageFixture extends CakeTestFixture {
	var $name = 'Language';
	var $table = 'languages';
	var $fields = array(
		'id' => array('type'=>'integer', 'null' => false, 'default' => NULL, 'length' => 3, 'key' => 'primary'),
		'name' => array('type'=>'string', 'null' => false, 'default' => NULL, 'length' => 32, 'key' => 'unique'),
		'created' => array('type'=>'datetime', 'null' => false, 'default' => NULL),
		'updated' => array('type'=>'datetime', 'null' => false, 'default' => NULL),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1), 'name' => array('column' => 'name', 'unique' => 1))
	);
	var $records = array(array(
		'id' => 1,
		'name' => 'Lorem ipsum dolor sit amet',
		'created' => '2010-08-10 15:07:06',
		'updated' => '2010-08-10 15:07:06'
	));
}
?>
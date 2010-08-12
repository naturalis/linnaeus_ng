<?php
class Right extends AppModel {

	var $name = 'Right';

	//The Associations below have been created with all possible keys, those that are not needed can be removed
	var $hasAndBelongsToMany = array(
		'projects_modules_users_rights' => array(
			'className' => 'projects_modules_users_rights',
			'joinTable' => 'projects_modules_users_rights',
			'foreignKey' => 'right_id',
			'associationForeignKey' => 'right_id',
			'unique' => true,
			'conditions' => '',
			'fields' => '',
			'order' => '',
			'limit' => '',
			'offset' => '',
			'finderQuery' => '',
			'deleteQuery' => '',
			'insertQuery' => ''
		)
	);

}
?>
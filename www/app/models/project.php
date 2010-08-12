<?php
class Project extends AppModel {

	var $name = 'Project';

	//The Associations below have been created with all possible keys, those that are not needed can be removed
	var $hasAndBelongsToMany = array(
		'Language' => array(
			'className' => 'Language',
			'joinTable' => 'projects_languages',
			'foreignKey' => 'project_id',
			'associationForeignKey' => 'language_id',
			'unique' => true,
			'conditions' => '',
			'fields' => '',
			'order' => '',
			'limit' => '',
			'offset' => '',
			'finderQuery' => '',
			'deleteQuery' => '',
			'insertQuery' => ''
		),
		'Module' => array(
			'className' => 'Module',
			'joinTable' => 'projects_modules',
			'foreignKey' => 'project_id',
			'associationForeignKey' => 'module_id',
			'unique' => true,
			'conditions' => '',
			'fields' => '',
			'order' => '',
			'limit' => '',
			'offset' => '',
			'finderQuery' => '',
			'deleteQuery' => '',
			'insertQuery' => ''
		),
		'projects_modules_users_rights' => array(
			'className' => 'projects_modules_users_rights',
			'joinTable' => 'projects_modules_users_rights',
			'foreignKey' => 'project_id',
			'associationForeignKey' => 'project_id',
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
<?php
class Language extends AppModel {

	var $name = 'Language';

	//The Associations below have been created with all possible keys, those that are not needed can be removed
	var $hasAndBelongsToMany = array(
		'Project' => array(
			'className' => 'Project',
			'joinTable' => 'projects_languages',
			'foreignKey' => 'language_id',
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
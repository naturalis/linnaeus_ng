<?php

class customConfiguration
{
	
	private $_project_index_texts =
		array(
			'page_title'=>'Select a project to work on',
			'page_header'=>'Select a project to work on',
			'search_placeholder'=>'',
			'left_bar_title'=>'',
			'left_bar_text'=>'',
		);
		
	public function getProjectIndexTexts()
	{
		return $this->_project_index_texts;
	}
	

}

<?php

class customConfiguration
{
	
	private $_project_index_texts =
		array(
			'page_title'=>'Soortzoekers Nederlandse biodiversiteit',
			'page_header'=>'Soortzoekers<br />Nederlandse biodiversiteit',
			'search_placeholder'=>'Zoek een soortgroep',
			'left_bar_title'=>'Soort determineren?',
			'left_bar_text'=>'Selecteer de betreffende soortgroep om uw vondst(en) op naam te brengen.<p>Het is van belang om na de determinatie de waarneming ook door te geven via Waarneming.nl of Telmee.</p>',
		);
		
	public function getProjectIndexTexts()
	{
		return $this->_project_index_texts;
	}
	

}

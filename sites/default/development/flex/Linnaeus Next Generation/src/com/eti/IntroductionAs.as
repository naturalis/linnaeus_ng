// ActionScript file

public function initIntroduction():void
{
	projectDescription.text=parentDocument.projectSettings[0].field_project_description[0]['value'];	
	projectAuthors.text=parentDocument.projectSettings[0].field_project_authors[0]['value'];
}
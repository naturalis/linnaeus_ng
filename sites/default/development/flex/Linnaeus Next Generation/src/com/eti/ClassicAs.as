// ActionScript file

public function initClassic():void
{
	drupal_text.text = "couplet ID = " + parentDocument.classicNodes[2].field_couplet[0]['value'] + "\n" +
						"child ID = " + parentDocument.classicNodes[2].field_child[0]['value'] + "\n" +
						"node ID = " + parentDocument.classicNodes[2].nid + "\n" +
						"title = " + parentDocument.classicNodes[2].title + "\n" +
						"body = " + parentDocument.classicNodes[2].body + "\n" +
						"species node ID = " + parentDocument.classicNodes[2].field_species[0].nid;
}
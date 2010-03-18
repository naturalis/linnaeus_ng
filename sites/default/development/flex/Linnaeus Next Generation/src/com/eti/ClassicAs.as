// ActionScript file
import flash.events.Event;

import mx.controls.*;
import mx.events.ListEvent;

public function initClassicPage():void{
	the_questions.addEventListener(ListEvent.ITEM_CLICK, question_click);
	remaining_species.addEventListener(ListEvent.ITEM_CLICK, species_click);	//parentDocument.parentDocument.specieslistEvent);
	drupal_text.text = "couplet ID = " + parentDocument.classicNodes[0].field_couplet[0]['value'] + "\n"+
						"child ID = " + parentDocument.classicNodes[0].field_child[0]['value'] + "\n"+
						"node ID = " + parentDocument.classicNodes[0].nid + "\n"+
						"title = " + parentDocument.classicNodes[0].title + "\n"+
						"body = " + parentDocument.classicNodes[0].body + "\n"+
						"species node ID = " + parentDocument.classicNodes[0].field_species[0].nid;
}

// a question was clicked
public function question_click(event:Event):void{
	if (the_questions.selectedItem.field_child[0]['value'] != 0){ // next question
		parentDocument.the_couplet = the_questions.selectedItem.field_child[0]['value'];
		my_questions.refresh();
		// hier reductie remaining species lijst
	}
	else{ // go to species
		parentDocument.changeView("moduleSpecies");
		parentDocument.selectKeySpeciesOnGrid();
	}
	drupal_text.text = "couplet ID = " + parentDocument.classicNodes[parentDocument.the_couplet].field_couplet[0]['value'] + "\n"+
						"child ID = " + parentDocument.classicNodes[parentDocument.the_couplet].field_child[0]['value'] + "\n"+
						"node ID = " + parentDocument.classicNodes[parentDocument.the_couplet].nid + "\n"+
						"title = " + parentDocument.classicNodes[parentDocument.the_couplet].title + "\n"+
						"body = " + parentDocument.classicNodes[parentDocument.the_couplet].body + "\n"+
						"species node ID = " + parentDocument.classicNodes[parentDocument.the_couplet].field_species[0].nid + "\n"+
						"species = " + parentDocument.classicNodes[parentDocument.the_couplet].field_species[0]['value'];
}

public function species_click(event:Event):void{
//	parentDocument.parentDocument.selectSpeciesFromRemaining(remaining_species.selectedItem.title);
	var i:int;
	my_dummy_text.text = remaining_species.selectedItem.title + "\n";
	for (i=109;i<=116;i++){
		if (i == parentDocument.classicNodes[parentDocument.the_couplet].field_species[0].nid){
			my_dummy_text.text += "i = " + i + "\n";
		}else{
			my_dummy_text.text += "i = " + i + ", nid = " + parentDocument.classicNodes[parentDocument.the_couplet].field_species[0].nid + "\n";
		}
	}
//	for each (title in remaining_species){
//		Alert.show("Naam.title: " + title);
//	}
}

private function key_filter(item:Object):Boolean{
	return item.field_couplet[0]['value'] == parentDocument.the_couplet;
}

private function species_filter(item:Object):Boolean{
	// return item.nid == 114;    -- voorbeeld
	return "TRUE";
}
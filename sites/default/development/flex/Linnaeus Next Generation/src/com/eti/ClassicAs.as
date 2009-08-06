// ActionScript file
import mx.controls.*;
import mx.events.ListEvent;
import com.eti.Navigator;

public function initClassicPage():void{
	the_questions.addEventListener(ListEvent.ITEM_CLICK,question_click);
}

// a question was clicked
public function question_click(event:Event):void{
	if (the_questions.selectedItem.field_child[0]['value'] != 0){ // next question
		parentDocument.the_couplet = the_questions.selectedItem.field_child[0]['value'];
		my_questions.refresh();
	}
	else{ // go to species
		parentDocument.changeView("moduleSpecies");
		parentDocument.selectKeySpeciesOnGrid();
	}
}

private function key_filter(item:Object):Boolean{
	return item.field_couplet[0]['value'] == parentDocument.the_couplet;
}
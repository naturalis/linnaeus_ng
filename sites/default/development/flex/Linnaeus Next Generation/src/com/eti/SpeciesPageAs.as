// ActionScript file
import com.eti.Navigator;

import mx.collections.ArrayCollection;
import mx.controls.*;
import mx.events.ItemClickEvent;
import mx.rpc.events.*;

private var previousSelectedItem:Object;

public function initSpeciesPage():void
{
	view_edit_tab.addEventListener(ItemClickEvent.ITEM_CLICK,viewEditNewClickEvent);
	imagepanel.visible=false;
	navigator.initNavigator();
}
private function viewEditNewClickEvent(event:ItemClickEvent):void {
	//Tab "View" or "Edit" clicked? Make sure there is still a species selected 
	//was unselected if tab New was clicked before
	if (event.index==0||event.index==1){
		restoreSpecies();
	}
	
	//Tab "New" clicked?
	if (event.index==2){
		newSpecies();
	}
}

private function restoreSpecies():void{
	if(! navigator.species_select.selectedItem){
		 navigator.species_select.selectedItem=previousSelectedItem;	
		if( navigator.species_select.selectedItem.field_image[0]){
			parentDocument.getFile(navigator.species_select.selectedItem.field_image[0].fid);
		}
	}
}

private function newSpecies():void
{
	previousSelectedItem= navigator.species_select.selectedItem;
	navigator.species_select.selectedItem = undefined;
	var barImages:Array;
	imagesBarInit(barImages);
	species_name.title = "";	
	species_image.source="";	
	imagepanel.visible=false;
	if(new_species_name) new_species_name.text="";
	if (new_species_description) new_species_description.htmlText="";
}

public function onSaved(event:ResultEvent):void
{
	//nid=event.result.toString();
	Alert.show("Species was saved", "Saved");
	previousSelectedItem=event.result.toString();
	
}

public function onDeleted(event:ResultEvent):void
{
	Alert.show("Species was deleted", "Deleted");
}

public function imagesBarInit(items:Array):void
{
	var im:ArrayCollection = new ArrayCollection(items);	
    imagesBar.dataProvider = im;
}
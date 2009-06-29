// ActionScript file
public function initModulesBar():void
{
	 modulesIntroduction.addEventListener(MouseEvent.CLICK,handleModuleBarClickEvent);
	 modulesSpecies.addEventListener(MouseEvent.CLICK,handleModuleBarClickEvent);
}
private function handleModuleBarClickEvent(event:Event):void 
{	
 if (event.currentTarget.id=="modulesIntroduction") parentDocument.changeView('moduleIntroduction');
 if (event.currentTarget.id=="modulesSpecies") parentDocument.changeView('moduleSpecies');
}    
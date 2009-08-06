// ActionScript file
import com.adobe.crypto.HMAC;
import com.adobe.crypto.SHA256;
import com.flexblocks.imagecropper.*;

import flash.display.BitmapData;
import flash.display.Loader;
import flash.events.Event;
import flash.net.FileReference;

import mx.collections.ArrayCollection;
import mx.collections.XMLListCollection;
import mx.controls.*;
import mx.events.MenuEvent;
import mx.graphics.ImageSnapshot;
import mx.graphics.codec.*;
import mx.rpc.events.*;
import mx.utils.ArrayUtil;
import mx.utils.Base64Decoder;
import mx.utils.Base64Encoder;

private var apiKey:String = "ef5b020a46f3338cd3d92fbb3c1aff9f";
//private var apiKey:String = "d2854d74b36e0c772be75cfcc1494b71";
private var apiDomain:String = "localhost";
private var sessionID:String;
private var fidOriginalImage:int;
private var fidOverviewImage:int;
private var fid:int;
private var nid:int;
private var base64Dec:Base64Decoder;
private var base64Enc:Base64Encoder;
private var fileRef:FileReference;
private var pathToSpeciesFiles:String="species/images/";
private var basePath:String="http://localhost/linnaeus_ng/";
private var htmlTextField:String;

private var previousSelectedIndex:Number=0;
private var fidOverviewImageRemove:Number=0;
private var barImageId:Array=new Array;
private var currentImageId:Number;

public var projectSettings:Array;
public var filedata:Array;
public var currentUser:String;
public var fileOriginalBase64:String;
public var newFileName:String;
public var localImage:ByteArray;
public var classicNodes:Array;
public var the_couplet:Number=1;

[Bindable]
public var questions:Array;
[Bindable]
public var speciesnodes:Array;
[Bindable]
public var keynodes:Array;
[Bindable]
public var tileItems:ArrayCollection;
[Bindable]
public var menuBarCollection:XMLListCollection;

public function init():void{
	system.connect();	
	fileRef = new FileReference();
	fileRef.addEventListener(Event.SELECT, selectEvent);
	fileRef.addEventListener(IOErrorEvent.IO_ERROR, ioErrorHandler);
	menuBarCollection = new XMLListCollection(menubarXML);
}

public function onSystemConnect(event:ResultEvent):void{
	sessionID = event.result.sessid;
	currentUser = event.result.user.name;
	//Alert.show(currentUser,"User");	
	user.text="Not logged in";
	if(currentUser!=null){
		moduleSpecies.view_edit_tab.visible=true;
		user.text="Logged in as: " + currentUser;
	}
	getSettings();
	getSpecies();
	getClassicKeyNodes();
}

public function onFault(event:FaultEvent):void{
	Alert.show(event.fault.faultString, "Error");
}

//file from desktop selected
public function selectEvent(event:Event):void{
	//fileRef.addEventListener(ProgressEvent.PROGRESS, onFileLoadProgress);
	fileRef.addEventListener(Event.COMPLETE, onFileLoadComplete);
	fileRef.load();
}

//load local file
public function onFileLoadComplete(event:Event):void{
	var byteArr:ByteArray = fileRef.data;
	localImage=byteArr;
	newFileName=fileRef.name;
	base64Enc = new Base64Encoder();
	base64Enc.encodeBytes(byteArr);
	fileOriginalBase64=base64Enc.toString();
	moduleSpecies.species_image.load(byteArr);
	moduleSpecies.imagepanel.visible=true;
	//switch to ImageCrop view
	changeView('moduleImageCrop');
}

public function initImage():void{
	var loader:Loader = new Loader();
	loader.contentLoaderInfo.addEventListener(Event.COMPLETE, loaderCompleteHandler);
	loader.contentLoaderInfo.addEventListener(IOErrorEvent.IO_ERROR, ioErrorHandler);
	loader.loadBytes(localImage);
}

public function initKey():void{
	the_couplet = 1;
	moduleClassic.my_questions.refresh();
}

public function loaderCompleteHandler(event:Event):void{
	var bd:BitmapData = new BitmapData( event.currentTarget.loader.width, event.currentTarget.loader.height );
	bd.draw(event.currentTarget.loader);
	moduleImageCrop.imageCropper.sourceImage=bd;
	//Alert.show("completeHandler: " + event);
}

public function imageSelected():void{
	moduleSpecies.species_image.load(moduleImageCrop.croppedImage.source);
	changeView('moduleSpecies');
}

public function ioErrorHandler(event:IOErrorEvent):void{
	Alert.show("ioErrorHandler: " + event);
}

//load file from server
public function onFileResult(event:ResultEvent):void{
	filedata=ArrayUtil.toArray(event.result);	
 	var byteArr:ByteArray;
	base64Dec = new Base64Decoder();
	base64Dec.decode(filedata[0].file);
	byteArr = base64Dec.toByteArray();
	moduleSpecies.species_image.load(byteArr);
	moduleSpecies.imagepanel.visible=true;
	if(moduleSpecies.view_edit_tab.selectedIndex==1)moduleSpecies.buttonDeleteImage.visible=true;
}

//initial loaded specieslist
public function onSpeciesViewResult(event:ResultEvent):void{
	speciesnodes = ArrayUtil.toArray(event.result);	
	if(nid>0){//saved a new species
		//todo: find index for species_select with nid and select the item.
		nid=0;
		previousSelectedIndex=0;
	}
	moduleSpecies.navigator.species_select.selectedIndex=previousSelectedIndex;
	//get file of first species or previous species from the specieslist the the list is loaded
	if(speciesnodes[previousSelectedIndex].field_image[0]){
		fid=speciesnodes[previousSelectedIndex].field_image[0].fid;
		currentImageId=0;
		getFile(fid);
		var barImages:Array=new Array;
		var i:Number=0;
		for each (var img:Object in speciesnodes[previousSelectedIndex].field_image){
			if (img.filepath.substr(0,40)=="sites/all/files/species/images/overview/"){
				//add thumbnail to array
				barImages.push(basePath+"sites/all/files/imagefield_thumbs/species/images/overview/"+img.filename);
				barImageId.push(i);
			}
			i++;
		}
		moduleSpecies.imagesBarInit(barImages);
	}	
	
	//initialize tilelist
	var tileImages:Array=new Array;
	for each (var tileImg:Object in speciesnodes){
		if(tileImg.field_image[0]){
			tileImages.push(basePath+"sites/all/files/imagefield_thumbs/species/images/overview/"+tileImg.field_image[0].filename);
		}
	}
	tileItems = new ArrayCollection(tileImages);
	//tileListInit(tileImages);
}

public function onClassicViewResult(event:ResultEvent):void{
	//processing of keynodes
	classicNodes = ArrayUtil.toArray(event.result);
}

public function showImagesBarImage():void{
	var id:Number=barImageId[ moduleSpecies.imagesBar.selectedIndex];
	currentImageId=id;
	getFile( moduleSpecies.navigator.species_select.selectedItem.field_image[id].fid);
}

public function onSettingsViewResult(event:ResultEvent):void{
	projectSettings=ArrayUtil.toArray(event.result);
	header.text=projectSettings[0].field_project_title[0]['value'];	
}

// a species was selected from the list with the mouse
public function specieslistEvent(event:Event):void{
	specieslistChange();
}

// a species was selected
public function specieslistChange():void{
	//if tab="new", change to tab="view" if a species is selected
	if(moduleSpecies.view_edit_tab.selectedIndex==2){
		moduleSpecies.view_edit_tab.selectedIndex=0;
	};
	if(moduleSpecies.view_edit_tab.selectedIndex==1){
		moduleSpecies.buttonDeleteImage.visible=false;
	}
	
	moduleSpecies.species_image.source="";
	moduleSpecies.imagepanel.visible=false;

	var barImages:Array=new Array;
	if(moduleSpecies.navigator.species_select.selectedItem.field_image[0]){
		fid = moduleSpecies.navigator.species_select.selectedItem.field_image[0].fid;
		currentImageId = 0;
		getFile(fid);
		var i:Number=0;
		for each (var img:Object in moduleSpecies.navigator.species_select.selectedItem.field_image){
			if (img.filepath.substr(0,40)=="sites/all/files/species/images/overview/"){
				//add thumbnail to array
				barImages.push(basePath+"sites/all/files/imagefield_thumbs/species/images/overview/"+img.filename);
				barImageId.push(i);
			}
			i++;
		}
	}
	moduleSpecies.imagesBarInit(barImages);
}

// compare the nid of the species from the key to the nid's of the navigator list and jump to it when there is a match:
public function selectKeySpeciesOnGrid():void{
	var key_result_nid:int = moduleClassic.the_questions.selectedItem.field_species[0].nid;
//	Alert.show("My item: " + key_result_nid);
	var gridData:Object = moduleSpecies.navigator.species_select.dataProvider;
	for(var i:Number=0; i < gridData.length; i++){
		var thisObj:Object = gridData.getItemAt(i);
		if(thisObj.nid == key_result_nid){	// there is a match
			moduleSpecies.navigator.species_select.selectedIndex = i;
			//sometimes scrollToIndex doesn't work without validateNow()
			moduleSpecies.navigator.species_select.validateNow();
			moduleSpecies.navigator.species_select.scrollToIndex(i);
			// now show the right pictures:
			specieslistChange();
		}
	}
}

public function changeView(newView:String):void{
	if(newView=='moduleSpecies'){
		mainContainer.selectedChild=moduleSpecies;
	}
	if(newView=='moduleIntroduction'){
		mainContainer.selectedChild=moduleIntroduction;
	}
	if(newView=='moduleClassic'){
		mainContainer.selectedChild=moduleClassic;
		initKey();
	}
	if(newView=='moduleMatrix'){
		mainContainer.selectedChild=moduleMatrix;
	}
	if(newView=='moduleImageCrop'){
		mainContainer.selectedChild=moduleImageCrop;
		initImage();
	}
}

public function getSettings():void{
	var hashedArray:Array = hashKey("views.get");
	settingsview.get(hashedArray[0],hashedArray[1],hashedArray[2],hashedArray[3],sessionID,"project_settings");
}

public function getSpecies():void{
	var hashedArray:Array = hashKey("views.get");
	speciesview.get(hashedArray[0],hashedArray[1],hashedArray[2],hashedArray[3],sessionID,"list_species");
}

public function getClassicKeyNodes():void{
	var hashedArray:Array = hashKey("views.get");
	classicview.get(hashedArray[0],hashedArray[1],hashedArray[2],hashedArray[3],sessionID,"list_keynodes");
}

public function getFile(fid:int):void{
	moduleSpecies.imagepanel.visible=false;
	var hashedArray:Array = hashKey("file.get");
	file.get(hashedArray[0],hashedArray[1],hashedArray[2],hashedArray[3],sessionID,fid);
}

public function saveNode():void{
	var edit:Object;
	previousSelectedIndex=0;
	if (moduleSpecies.navigator.species_select.selectedItem){
		previousSelectedIndex=moduleSpecies.navigator.species_select.selectedIndex;
		edit = moduleSpecies.navigator.species_select.selectedItem;
		var curr_date:Date = new Date();
		edit.changed = curr_date.getTime(); //Upon update, node object must include both "nid" and "changed".
		edit.type="species";
		edit.title=moduleSpecies.edit_species_name.text;
		edit.body=moduleSpecies.edit_species_description.htmlText;
		if(fidOverviewImage>0){ //add new images to images array
			edit.field_image.push({fid:fidOverviewImage});
			edit.field_image.push({fid:fidOriginalImage});
			fidOriginalImage=0;
			fidOverviewImage=0;
		}
		if(fidOverviewImageRemove>0){ //remove images from images array
			edit.field_image[currentImageId]["fid"]=0;
			edit.field_image[currentImageId+1]["fid"]=0;
			fidOverviewImageRemove=0;
		}
	}
	else {
		edit = new Object;
		edit.type="species";
		edit.title= moduleSpecies.new_species_name.text;
		edit.body= moduleSpecies.new_species_description.htmlText;
		//image 0=overview image, 1=original image
		edit.field_image = new Array({ fid:fidOverviewImage },{ fid:fidOriginalImage });
		fidOriginalImage=0;
		fidOverviewImage=0;
	}
	if (edit.title == "" || edit.body == ""){
		Alert.show("Enter a name and description", "Error");
	}
	else {
		var hashedArray:Array = hashKey("node.save");
		node.save(hashedArray[0],hashedArray[1],hashedArray[2],hashedArray[3],sessionID,edit);
		moduleSpecies.view_edit_tab.selectedIndex=0;
		getSpecies();
	}	
}

public function fileSaveResult(event:ResultEvent):void{
	if(fidOriginalImage > 0){//first image was already saved
		//get fid from second node
		fidOverviewImage = event.result.toString();
		//after saving images save node data	
		saveNode();
	}
	else {
		fidOriginalImage = event.result.toString();
	}
}

public function saveSpecies():void{
	//if there is a new image,first save image, than save node with resulting fid
	if(moduleSpecies.species_image.source!="" && newFileName){
		/*
		var jpgEnc:JPEGEncoder = new JPEGEncoder(100);
		var ohSnap:ImageSnapshot;		
		ohSnap = ImageSnapshot.captureImage(species_image, 0, jpgEnc);
		capture image file. you can change this to almost anything you want on the canvans
		var photoBase64:String = ImageSnapshot.encodeImageAsBase64(ohSnap);
		*/
		//Save original image
		var fileObj:Object = {
			file:fileOriginalBase64,
			fid:"",
			filepath:pathToSpeciesFiles+'original/'+newFileName,
			filesize: ""
		};
		var hashedArray:Array = hashKey("file.save");
		file.save(hashedArray[0],hashedArray[1],hashedArray[2],hashedArray[3],sessionID,fileObj);
		//save overview image
		var jpgEnc:JPEGEncoder = new JPEGEncoder(100);
		var Snap:ImageSnapshot;
		Snap = ImageSnapshot.captureImage(moduleSpecies.species_image, 0, jpgEnc); // capture image file.
		var fileOverviewBase64:String = ImageSnapshot.encodeImageAsBase64(Snap);
		fileObj = {
			file:fileOverviewBase64,
			fid:"",
			filepath:pathToSpeciesFiles+'overview/'+newFileName,
			filesize: ""
		};
		hashedArray = hashKey("file.save");
		file.save(hashedArray[0],hashedArray[1],hashedArray[2],hashedArray[3],sessionID,fileObj);	
	}
	else {
		//save only node
		fid=0;
		saveNode();
	}
}

public function deleteSpecies():void{
	var edit:Object;
	if (moduleSpecies.navigator.species_select.selectedItem) {
		edit =  moduleSpecies.navigator.species_select.selectedItem;
		var hashedArray:Array = hashKey("node.deleteNode");
		node.deleteNode(hashedArray[0],hashedArray[1],hashedArray[2],hashedArray[3],sessionID,edit.nid);
		previousSelectedIndex=0;
		getSpecies();
	}
	else {
		Alert.show("No species to delete", "Error");
	}
}

public function deleteImage():void{
	fidOverviewImageRemove=moduleSpecies.navigator.species_select.selectedItem.field_image[currentImageId].fid;
	saveNode();
	moduleSpecies.species_image.source="";
	moduleSpecies.imagepanel.visible=false;
	moduleSpecies.buttonDeleteImage.visible=false;
}

public function hashKey(serviceMethod:String):Array{
	var captureTime:String = (Math.round((new Date().getTime())/1000)).toString();
	var captureRandom:String = randomString(10);
	var hashString:String = captureTime + ";";
	hashString += apiDomain + ";";
	hashString += captureRandom +";";
	hashString += serviceMethod;
	return [HMAC.hash(apiKey,hashString,SHA256),apiDomain,captureTime,captureRandom];
}

private function randomString(Stringlength:Number):String{
	var allowedChar:String = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
	var allowedArray:Array = allowedChar.split("");
	var randomChars:String = "";
	for (var i:Number = 0; i < Stringlength; i++){
		randomChars += allowedArray[Math.floor(Math.random() * allowedArray.length)];
	}
	return randomChars;
}

public function removeEditorButtons(htmlTextField:String):void {
	if(htmlTextField=='edit_species_description'){
		moduleSpecies.edit_species_description.toolbar.removeChild(moduleSpecies.edit_species_description.fontFamilyCombo);
		//moduleSpecies.edit_species_description.toolbar.removeChild(moduleSpecies.edit_species_description.fontSizeCombo);
		//moduleSpecies.edit_species_description.toolbar.removeChild(moduleSpecies.edit_species_description.colorPicker);
	}
	if(htmlTextField=='new_species_description'){
		moduleSpecies.new_species_description.toolbar.removeChild(moduleSpecies.new_species_description.fontFamilyCombo);
		//moduleSpecies.new_species_description.toolbar.removeChild(moduleSpecies.new_species_description.fontSizeCombo);
		//moduleSpecies.new_species_description.toolbar.removeChild(moduleSpecies.new_species_description.colorPicker);
	}
}

public function openFileBrowser():void {
	fileRef.browse();
}

// Event handler for the MenuBar control's itemClick event.
private function menuHandler(event:MenuEvent):void {
	// Don't open the Alert for a menu bar item that opens a popup submenu.
	if (event.item.@data != "top") {
		Alert.show("Label: " + event.item.@label + "\n" + "Data: " + event.item.@data, "Clicked menu item");
	}
}

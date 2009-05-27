// ActionScript file
import com.adobe.crypto.HMAC;
import com.adobe.crypto.SHA256;
import com.flexblocks.imagecropper.*;

import flash.display.BitmapData;
import flash.display.Loader;
import flash.events.Event;
import flash.net.FileReference;

import mx.collections.ArrayCollection;
import mx.controls.*;
import mx.events.ItemClickEvent;
import mx.events.ListEvent;
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
private var previousSelectedItem:Object;
private var previousSelectedIndex:Number=0;
private var fidOverviewImageRemove:Number=0;
private var barImageId:Array=new Array;
private var currentImageId:Number;

[Bindable]
public var speciesnodes:Array;
public var projectSettings:Array;
public var filedata:Array;
public var currentUser:String;
public var fileOriginalBase64:String;
public var newFileName:String;
public var localImage:ByteArray;
public var tileItems:ArrayCollection;

public function init():void
{
	system.connect();	
	
	fileRef = new FileReference();
    fileRef.addEventListener(Event.SELECT, selectEvent);
    fileRef.addEventListener(IOErrorEvent.IO_ERROR, ioErrorHandler);
    
    species_select.addEventListener(ListEvent.CHANGE,specieslistEvent);
    view_edit_tab.addEventListener(ItemClickEvent.ITEM_CLICK,viewEditNewClickEvent);
    imagepanel.visible=false;
}

public function onSystemConnect(event:ResultEvent):void{
	sessionID = event.result.sessid;
	currentUser = event.result.user.name;	
	//Alert.show(currentUser,"User");	
	user.text="Not logged in";
	if(currentUser!=null){
		view_edit_tab.visible=true;
		user.text="Logged in as: " + currentUser;
	}
	getSettings();
	getSpecies();	
}

public function onFault(event:FaultEvent):void
{
	Alert.show(event.fault.faultString, "Error");
}
public function viewEditNewClickEvent(event:ItemClickEvent):void {
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
public function restoreSpecies():void{
	if(!species_select.selectedItem){
		species_select.selectedItem=previousSelectedItem;	
		if(species_select.selectedItem.field_image[0]){
			getFile(species_select.selectedItem.field_image[0].fid);
		}
	}
	
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
	species_image.load(byteArr);	
	 imagepanel.visible=true;
	 //switch to ImageCrop view
	 changeView('moduleImageCrop');	 
}
public function initImage():void
{
	var loader:Loader = new Loader();	
	loader.contentLoaderInfo.addEventListener(Event.COMPLETE, loaderCompleteHandler);
	loader.contentLoaderInfo.addEventListener(IOErrorEvent.IO_ERROR, ioErrorHandler);
	loader.loadBytes(localImage);
}

public function loaderCompleteHandler(event:Event):void
{
	var bd:BitmapData = new BitmapData( event.currentTarget.loader.width, event.currentTarget.loader.height );
    bd.draw( event.currentTarget.loader) ;
	imageCropper.sourceImage=bd;	
	//Alert.show("completeHandler: " + event);

}
public function imageSelected():void
{
	species_image.load(croppedImage.source);
	changeView('moduleSpecies');
}
public function ioErrorHandler(event:IOErrorEvent):void {
	Alert.show("ioErrorHandler: " + event);
}

public function imagesBarInit(items:Array):void
{
	var im:ArrayCollection = new ArrayCollection(items);	
    imagesBar.dataProvider = im;
}
public function initTileList():void
{	
   tilelist.dataProvider = tileItems;
}

//load file from server
public function onFileResult(event:ResultEvent):void
{
	filedata=ArrayUtil.toArray(event.result);	
 	var byteArr:ByteArray; 	
    base64Dec = new Base64Decoder();
    base64Dec.decode(filedata[0].file);
    byteArr = base64Dec.toByteArray();    
    species_image.load(byteArr);
   	imagepanel.visible=true;
   	if(view_edit_tab.selectedIndex==1)buttonDeleteImage.visible=true;
}


//initial loaded specieslist
public function onSpeciesViewResult(event:ResultEvent):void
{	
	speciesnodes = ArrayUtil.toArray(event.result);	
	if(nid>0){//saved a new species
		//todo: find index for species_select with nid and select the item.	
		nid=0;
		previousSelectedIndex=0;
	}
	species_select.selectedIndex=previousSelectedIndex;
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
		imagesBarInit(barImages);
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


public function showImagesBarImage():void
{
	var id:Number=barImageId[imagesBar.selectedIndex];
	currentImageId=id;
	getFile(species_select.selectedItem.field_image[id].fid);
}

public function onSettingsViewResult(event:ResultEvent):void
{	
	projectSettings=ArrayUtil.toArray(event.result);
	header.text=projectSettings[0].field_project_title[0]['value'];	
}

// a species was selected from list
public function specieslistEvent(event:Event):void
{
	//if tab="new", change to tab="view" if a species is selected
	if(view_edit_tab.selectedIndex==2){
		view_edit_tab.selectedIndex=0;
	};
	if(view_edit_tab.selectedIndex==1){
	buttonDeleteImage.visible=false;
	}
	
	species_image.source="";
	imagepanel.visible=false;
	
	var barImages:Array=new Array;
	if(species_select.selectedItem.field_image[0]){
		fid=species_select.selectedItem.field_image[0].fid;	
		currentImageId=0;
		getFile(fid);
		
		var i:Number=0;
		for each (var img:Object in species_select.selectedItem.field_image){			
			if (img.filepath.substr(0,40)=="sites/all/files/species/images/overview/"){		
				//add thumbnail to array		
				barImages.push(basePath+"sites/all/files/imagefield_thumbs/species/images/overview/"+img.filename);
				barImageId.push(i);
			}
			i++;
		}				
	}
	imagesBarInit(barImages);
}
public function changeView(newView:String):void
{	
	if(newView=='moduleSpecies'){
		mainContainer.selectedChild=moduleSpecies;
	}
	if(newView=='moduleIntroduction'){
		mainContainer.selectedChild=moduleIntroduction;		
	}
	if(newView=='moduleImageCrop'){
		mainContainer.selectedChild=moduleImageCrop;
		initImage();
	}
}

public function initIntroduction():void
{
	projectDescription.text=projectSettings[0].field_project_description[0]['value'];	
	projectAuthors.text=projectSettings[0].field_project_authors[0]['value'];
}

public function getSettings():void
{
	var hashedArray:Array = hashKey("views.get");	
	settingsview.get(hashedArray[0],hashedArray[1],hashedArray[2],hashedArray[3],sessionID,"project_settings");	
}

public function getSpecies():void
{
	var hashedArray:Array = hashKey("views.get");	
	speciesview.get(hashedArray[0],hashedArray[1],hashedArray[2],hashedArray[3],sessionID,"list_species");	
}

public function getFile(fid:int):void
{		
	imagepanel.visible=false;
	var hashedArray:Array = hashKey("file.get");	
	file.get(hashedArray[0],hashedArray[1],hashedArray[2],hashedArray[3],sessionID,fid);	
}

public function saveNode():void
{
	var edit:Object;
	previousSelectedIndex=0;
	if (species_select.selectedItem) {
		previousSelectedIndex=species_select.selectedIndex;
		edit = species_select.selectedItem;
		var curr_date:Date = new Date();
		edit.changed = curr_date.getTime(); //Upon update, node object must include both "nid" and "changed".
		edit.type="species";
		edit.title=edit_species_name.text;
		edit.body=edit_species_description.htmlText;
		if(fidOverviewImage>0){//add new images to images array
			edit.field_image.push({fid:fidOverviewImage});
			edit.field_image.push({fid:fidOriginalImage});
			fidOriginalImage=0;
			fidOverviewImage=0;
		}
		if(fidOverviewImageRemove>0){//remove images from images array
			edit.field_image[currentImageId]["fid"]=0;
			edit.field_image[currentImageId+1]["fid"]=0;
			fidOverviewImageRemove=0;
		}				
	}
	else {
		edit = new Object;	
		edit.type="species";
		edit.title=new_species_name.text;
		edit.body=new_species_description.htmlText;
		//image 0=overview image, 1=original image
		edit.field_image = new Array({ fid:fidOverviewImage },{ fid:fidOriginalImage });
		fidOriginalImage=0;
		fidOverviewImage=0;
	}
		

	
	if (edit.title == "" || edit.body == ""){
		Alert.show("Enter a name and description", "Error");		
	}else{	   
		var hashedArray:Array = hashKey("node.save");
		node.save(hashedArray[0],hashedArray[1],hashedArray[2],hashedArray[3],sessionID,edit);
		view_edit_tab.selectedIndex=0;
		getSpecies();				
	}
	
}


public function fileSaveResult(event:ResultEvent):void{
	
	if(fidOriginalImage > 0){//first image was already saved
	    //get fid from second node
		fidOverviewImage = event.result.toString();
			//after saving images save node data	
	        saveNode();	        
	}else{
		fidOriginalImage = event.result.toString();
	}
}

public function saveSpecies():void{
	
	//if there is a new image,first save image, than save node with resulting fid
	if(species_image.source!="" && newFileName){	
		/*	
		var jpgEnc:JPEGEncoder = new JPEGEncoder(100);
		var ohSnap:ImageSnapshot;		
	  	ohSnap = ImageSnapshot.captureImage(species_image, 0, jpgEnc); // capture image file. you can change this to almost anything you want on the canvans
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
	  	Snap = ImageSnapshot.captureImage(species_image, 0, jpgEnc); // capture image file. 
		var fileOverviewBase64:String = ImageSnapshot.encodeImageAsBase64(Snap);
		  fileObj = {
	     	file:fileOverviewBase64,
	        fid:"",
	        filepath:pathToSpeciesFiles+'overview/'+newFileName, 
	        filesize: ""
	    };	    	    
	    hashedArray = hashKey("file.save");
		file.save(hashedArray[0],hashedArray[1],hashedArray[2],hashedArray[3],sessionID,fileObj);
		
	}else{		
		//save only node		
		fid=0;
		saveNode();
	}
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

public function newSpecies():void
{
	previousSelectedItem=species_select.selectedItem;
	species_select.selectedItem = undefined;
	var barImages:Array;
	imagesBarInit(barImages);
	species_name.title = "";	
	species_image.source="";	
	imagepanel.visible=false;
	if(new_species_name) new_species_name.text="";
	if (new_species_description)new_species_description.htmlText="";
}

public function deleteSpecies():void
{
	var edit:Object;
	if (species_select.selectedItem) {
		edit = species_select.selectedItem;		
		var hashedArray:Array = hashKey("node.deleteNode");
		node.deleteNode(hashedArray[0],hashedArray[1],hashedArray[2],hashedArray[3],sessionID,edit.nid);
		previousSelectedIndex=0;
		getSpecies();
	}
	else {
		Alert.show("No species to delete", "Error");
	}			
}

public function deleteImage():void
{
	
	fidOverviewImageRemove=species_select.selectedItem.field_image[currentImageId].fid;
	saveNode();
	species_image.source="";	
	imagepanel.visible=false;
	buttonDeleteImage.visible=false;
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
          edit_species_description.toolbar.removeChild(edit_species_description.fontFamilyCombo);
          edit_species_description.toolbar.removeChild(edit_species_description.fontSizeCombo);
          edit_species_description.toolbar.removeChild(edit_species_description.colorPicker);  
 	}
 	if(htmlTextField=='new_species_description'){
 		  new_species_description.toolbar.removeChild(new_species_description.fontFamilyCombo);
          new_species_description.toolbar.removeChild(new_species_description.fontSizeCombo);
          new_species_description.toolbar.removeChild(new_species_description.colorPicker);  
 	}
}

/*
*  function to print out the contents of an array similar to the PHP print_r() function
*  usage: print_r(some_array);
*/
public function print_r(obj:*, level:int = 0, output:String = ""):* {
    var tabs:String = "";
    for(var i:int = 0; i < level; i++, tabs += "\t");
   
    for(var child:* in obj){
        output += tabs +"["+ child +"] => "+ obj[child];
       
        var childOutput:String = print_r(obj[child], level+1);
        if(childOutput != '') output += ' {\n'+ childOutput + tabs +'}';
       
        output += "\n";
    }
   
    if(level == 0){
    	 Alert.show(output);
    	 trace(output);
    }
    else return output;
}



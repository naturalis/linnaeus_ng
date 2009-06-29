// ActionScript file
import mx.controls.*;

// Index of last image selected using the selectImage ComboBox			
private var lastSelectedImage:uint = 0;

// Current values for the cropping rectangle, handle size, and aspect ratio constraint settings
private var currentCropbox:Rectangle;
private var currentHandleSize:uint = 10;
private var currentCropConstraint:Boolean = false

// Image 1 ("Image Larger then Component"): Last values for the cropping rectangle, handle size, and aspect ratio constraint settings
private var img1Cropbox:Rectangle;
private var img1HandleSize:uint = 10;
private var img1CropConstraint:Boolean = false;

// Image 2 ("Image Smaller than Component"): Last values for the cropping rectangle, handle size, and aspect ratio constraint settings
private var img2Cropbox:Rectangle;			
private var img2HandleSize:uint = 10;
private var img2CropConstraint:Boolean = false;			
			
// --------------------------------------------------------------------------------------------------
// imageReady - Called when the ImageCropper component has loaded and initialized an image
// --------------------------------------------------------------------------------------------------
private function imageReady():void {
	
	// Enable the controls (including the imageCropper). Note that the imageCropper must be enabled before changing property values or calling setCropRect().
	enableControls(true, true);
	
	imageCropper.setCropRect(241,281,-1,-1,true);
	
	// Get the cropped image 
	doCrop();				
}

// --------------------------------------------------------------------------------------------------
// doCrop - Get the cropped image from the ImageCropper component
// --------------------------------------------------------------------------------------------------
private function doCrop():void {
	
	// Get the cropped BitmapData
	var newImage:BitmapData = imageCropper.croppedBitmapData;
	
	// Set the width and height of the croppedImage Image based on the dimensions of the cropped image
	//croppedImage.width = newImage.width;
	//croppedImage.height = newImage.height;
	croppedImage.width = 241;
	croppedImage.height = 281;

	// Create a new Bitmap from the BitmapData and assign it to the croppedImage Image
	croppedImage.source = new Bitmap(newImage);
	
	// Display the cropping rectangle in relative to the ImageCropper component and relative to the image
	imageCropperRect.text = imageCropper.getCropRect(true, true).toString();
	sourceImageRect.text = imageCropper.getCropRect(false, true).toString();
}	

// --------------------------------------------------------------------------------------------------
// enableControls - Enables or disables the controls
// --------------------------------------------------------------------------------------------------

private function enableControls(enable:Boolean, includeEnabled:Boolean = false):void {
	
	// Set the enabled state for all controls
	imageCropper.enabled = enable;
}
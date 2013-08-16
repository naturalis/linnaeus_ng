function init(p) 
{
	if (p.matrix) appController.setmatrix(p.matrix);	
	if (p.language) appController.setlanguage(p.language);	
}

function main() 
{
	appController.set({28267:true,28037:true,28062:true});
	appController.states(renderStates,errorHandler);
}

function renderStates(elements)
{
	
	
}

function errorHandler()
{
	var e=appController.geterror();
	alert('error: '+e.message+' (error '+e.code+')');
}

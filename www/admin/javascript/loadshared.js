var sharedScripts = new Array("/shared/javascript/jquery.js");

function loadScript(url) {
   var e = document.createElement("script");
   e.src = url;
   e.type="text/javascript";
   document.getElementsByTagName("head")[0].appendChild(e);
}

onload = function() {

	for(var i=0;i<sharedScripts.length;i++)
		loadScript(sharedScripts[i]);

}
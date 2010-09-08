234
a:4:{s:8:"template";a:4:{s:7:"add.tpl";b:1;s:26:"../shared/admin-header.tpl";b:1;s:18:"_add_edit_body.tpl";b:1;s:26:"../shared/admin-footer.tpl";b:1;}s:9:"timestamp";i:1283943791;s:7:"expires";i:1283947391;s:13:"cache_serials";a:0:{}}<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

	<title>Imaginary Beings - Add a new taxon</title>

	<link href="/admin/images/system/favicon.ico" rel="shortcut icon" type="image/x-icon" />
	<link href="/admin/images/system/favicon.ico" rel="icon" type="image/x-icon" />

	<style type="text/css" media="all">
		@import url("/admin/style/main.css");
		@import url("/admin/style/admin-inputs.css");
		@import url("/admin/style/admin-help.css");
		@import url("/admin/style/admin.css");
	</style>

	<script type="text/javascript" src="/admin/javascript/jquery-1.4.2.min.js"></script>
	<script type="text/javascript" src="/admin/javascript/main.js"></script>
<script type="text/javascript" src="/admin/javascript/tinymce/jscripts/tiny_mce/tiny_mce.js" ></script >

<script type="text/javascript">
tinyMCE.init({
		mode : "textareas",
		theme : "advanced",
		plugins : "spellchecker,advhr,insertdatetime,preview",	
		
		// Theme options - button# indicated the row# only
	theme_advanced_buttons1 : "newdocument,|,bold,italic,underline,|,justifyleft,justifycenter,justifyright,fontselect,fontsizeselect,formatselect",
	theme_advanced_buttons2 : "cut,copy,paste,|,bullist,numlist,|,outdent,indent,|,undo,redo,|,link,unlink,anchor,image,|,code,preview,|,forecolor,backcolor",
	theme_advanced_buttons3 : "insertdate,inserttime,|,spellchecker,advhr,,removeformat,|,sub,sup,|,charmap,emotions",	
	theme_advanced_toolbar_location : "top",
	theme_advanced_toolbar_align : "left",
	theme_advanced_statusbar_location : "bottom" //(n.b. no trailing comma in last line of code)
	//theme_advanced_resizing : true //leave this out as there is an intermittent bug.
});
</script>


</head>

<body><div id="body-container">
<div id="header-container">
	<a href="/admin/admin-index.php"><img src="/admin/images/system/linnaeus_logo.png" id="lng-logo" />
	<img src="/admin/images/system/eti_logo.png" id="eti-logo" /></a>
</div>
<div id="page-container">

<div id="page-header-titles">
	<span id="page-header-title">Linnaeus NG Administration v0.1</span><br />
	<span id="page-header-projectname">Imaginary Beings</span>
<!--DEBUG ONLY:--><span style="color:white">2</span>
<br />	<span id="page-header-appname"><a href="index.php">Species module</a></span><br />	<span id="page-header-pageaction">Add a new taxon</span>
</div>


<div id="page-main">

<form name="theForm" id="theForm">
	<input type="hidden" name="taxon_id" id="taxon_id" value="" />  
<div id="taxon-navigation-table-div">
<table id="taxon-navigation-table">
	<tr>
		<td id="taxon-navigation-cell">
			<span style="float:right">
				<span id="message-container" style="margin-right:10px">&nbsp;</span>
				<input type="button" value="save" onclick="taxonSaveData()" style="margin-right:5px" />
				<input type="button" value="undo" onclick="taxonMessage('coming soon')" style="margin-right:5px" />
				<input type="button" value="delete" onclick="taxonMessage('coming soon')" style="margin-right:5px" />
				<input type="button" value="taxon list" onclick="window.open('list.php','_top');" style="" />
			</span>
		</td>
	</tr>
</table>
</div>
<div id="taxon-language-table-div">
<table id="taxon-language-table" class="taxon-language-table">
	<tr>
		<td class="taxon-language-cell" onclick="taxonGetData(24)">
			Dutch		</td>
		<td class="taxon-language-cell-active">
			English *		</td>
		<td class="taxon-language-cell" onclick="taxonGetData(50)">
			Japanese		</td>
	</tr>
</table>
</div>

Taxon name:<input type="text" maxlength="64" name="taxon" id="taxon-name-input" value="" />
<textarea name="content" style="width:880px;height:600px;" id="taxon-content"></textarea>
</form>




<script type="text/JavaScript">
$(document).ready(function(){

	taxonAddLanguage([24,'Dutch',0]);
	taxonAddLanguage([26,'English',1]);
	taxonAddLanguage([50,'Japanese',0]);
	taxonActiveLanguage = 26;
	taxonUpdateLanguageBlock();

});
</script>

</div>


<script type="text/JavaScript">
$(document).ready(function(){
	activeLanguage = 26;
});
</script>



</div ends="page-container">

<div id="footer-container">
	<div id="footer-menu">
		<a href="/admin/admin-index.php">Main index</a>
		<a href="/admin/views/users/logout.php">Log out (logged in as Jorge Luis Borges)</a>
		<br />
	</div>
</div ends="footer-container">

</div ends="body-container"></body>
</html>
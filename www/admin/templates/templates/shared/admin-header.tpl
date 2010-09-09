<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

	<title>{$session.project.name}{if $session.project.name!='' && $pageName != ''} - {/if}{$pageName}</title>

	<link href="{$rootWebUrl}admin/images/system/favicon.ico" rel="shortcut icon" type="image/x-icon" />
	<link href="{$rootWebUrl}admin/images/system/favicon.ico" rel="icon" type="image/x-icon" />

	<style type="text/css" media="all">
		@import url("{$rootWebUrl}admin/style/main.css");
		@import url("{$rootWebUrl}admin/style/admin-inputs.css");
		@import url("{$rootWebUrl}admin/style/admin-help.css");
		@import url("{$rootWebUrl}admin/style/admin.css");
	</style>

	<script type="text/javascript" src="{$rootWebUrl}admin/javascript/jquery-1.4.2.min.js"></script>
	<script type="text/javascript" src="{$rootWebUrl}admin/javascript/main.js"></script>
{if $includeHtmlEditor}
<script type="text/javascript" src="{$rootWebUrl}admin/javascript/tinymce/jscripts/tiny_mce/tiny_mce.js" ></script >
{literal}
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
{/literal}
{/if}

</head>

<body><div id="body-container">
<div id="header-container">
	<a href="{$rootWebUrl}admin/admin-index.php"><img src="{$rootWebUrl}admin/images/system/linnaeus_logo.png" id="lng-logo" />
	<img src="{$rootWebUrl}admin/images/system/eti_logo.png" id="eti-logo" /></a>
</div>
<div id="page-container">

<div id="page-header-titles">
	<span id="page-header-title">{$applicationName} v{$applicationVersion}</span>
	<br />
{if $breadcrumbs}
	<div id="breadcrumbs">
	{section name=i loop=$breadcrumbs}
	{assign var=n value=$n+1}
{if $n==$breadcrumbs|@count}
		<span id="crumb-current">{$breadcrumbs[i].name}</span>
		<span class="crumb-arrow">&nbsp;</span>
	{else}
		<span class="crumb"><a href="{$breadcrumbs[i].url}">{$breadcrumbs[i].name}</a></span>
		<span class="crumb-arrow">&rarr;</span>
	{/if}
	{/section}
	</div>
{/if}
</div>



{if $helpTexts}
<div id="block-inline-help">
	<div id="title" onclick="allToggleHelpVisibility();">Help</div>
	<div class="body-collapsed" id="body-visible">
{section name=i loop=$helpTexts}
		<div class="subject">{$helpTexts[i].subject}</div>
		<div class="text">{$helpTexts[i].helptext}</div>
{/section}
	</div>
</div>
{/if}
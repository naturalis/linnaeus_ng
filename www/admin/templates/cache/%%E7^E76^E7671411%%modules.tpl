249
a:4:{s:8:"template";a:4:{s:11:"modules.tpl";b:1;s:26:"../shared/admin-header.tpl";b:1;s:28:"../shared/admin-messages.tpl";b:1;s:26:"../shared/admin-footer.tpl";b:1;}s:9:"timestamp";i:1284037831;s:7:"expires";i:1284041431;s:13:"cache_serials";a:0:{}}<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

	<title>Imaginary Beings - Project modules</title>

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
<br />	<span id="page-header-appname"><a href="index.php">Project administration</a></span><br />	<span id="page-header-pageaction">Project modules</span>
</div>


<div id="page-main">

<div class="text-block">
Select the standard modules you wish to use in your project:<br />
<table>
	<tr>
				<td
			title="in use in your project"
			class="cell-module-in-use" 
			id="cell-1a"
		>&nbsp;
			
		</td>
		<td 
			class="cell-module-deactivate" 
			title="deactivate (no data will be deleted)" 
			onclick="moduleChangeModuleStatus(this)"
			id="cell-1b"
		>&nbsp;
			
		</td>
		<td
			class="cell-module-invisible" 
			title="" 
			onclick="moduleChangeModuleStatus(this)"
			id="cell-1c"
		>&nbsp;
		</td>
		<td>
			<span 
				class="cell-module-title-in-use" 
				id="cell-1d">
			<span class="cell-module-title">Introduction</span> - Comprehensive project introduction</span>
			<span id="cell-1e" style="visibility:hidden">Introduction</span>
		</td>
	
</tr>
	<tr>
			<td
			class="cell-module-unused" 
			id="cell-2a"
			title="not in use in your project" 
		>&nbsp;
			
		</td>
		<td
			class="cell-module-activate" 
			title="activate" 
			onclick="moduleChangeModuleStatus(this)"
			id="cell-2b"
		>&nbsp;
			
		</td>
		<td
			class="cell-module-invisible" 
			title="" 
			onclick="moduleChangeModuleStatus(this)"
			id="cell-2c"
		>&nbsp;
			
		</td>	
		<td>
			<span
				class="cell-module-title-unused" 
				id="cell-2d">
			<span class="cell-module-title">Glossary</span> - Project glossary</span>
			<span id="cell-2e" style="visibility:hidden">Glossary</span>
		</td>
	</tr>
	<tr>
				<td
			title="in use in your project"
			class="cell-module-in-use" 
			id="cell-3a"
		>&nbsp;
			
		</td>
		<td 
			class="cell-module-deactivate" 
			title="deactivate (no data will be deleted)" 
			onclick="moduleChangeModuleStatus(this)"
			id="cell-3b"
		>&nbsp;
			
		</td>
		<td
			class="cell-module-invisible" 
			title="" 
			onclick="moduleChangeModuleStatus(this)"
			id="cell-3c"
		>&nbsp;
		</td>
		<td>
			<span 
				class="cell-module-title-in-use" 
				id="cell-3d">
			<span class="cell-module-title">Literature</span> - Literary references</span>
			<span id="cell-3e" style="visibility:hidden">Literature</span>
		</td>
	
</tr>
	<tr>
			<td
			class="cell-module-unused" 
			id="cell-4a"
			title="not in use in your project" 
		>&nbsp;
			
		</td>
		<td
			class="cell-module-activate" 
			title="activate" 
			onclick="moduleChangeModuleStatus(this)"
			id="cell-4b"
		>&nbsp;
			
		</td>
		<td
			class="cell-module-invisible" 
			title="" 
			onclick="moduleChangeModuleStatus(this)"
			id="cell-4c"
		>&nbsp;
			
		</td>	
		<td>
			<span
				class="cell-module-title-unused" 
				id="cell-4d">
			<span class="cell-module-title">Species module</span> - Detailed pages for taxa</span>
			<span id="cell-4e" style="visibility:hidden">Species module</span>
		</td>
	</tr>
	<tr>
			<td
			class="cell-module-unused" 
			id="cell-5a"
			title="not in use in your project" 
		>&nbsp;
			
		</td>
		<td
			class="cell-module-activate" 
			title="activate" 
			onclick="moduleChangeModuleStatus(this)"
			id="cell-5b"
		>&nbsp;
			
		</td>
		<td
			class="cell-module-invisible" 
			title="" 
			onclick="moduleChangeModuleStatus(this)"
			id="cell-5c"
		>&nbsp;
			
		</td>	
		<td>
			<span
				class="cell-module-title-unused" 
				id="cell-5d">
			<span class="cell-module-title">Higher taxa</span> - Detailed pages for higher taxa</span>
			<span id="cell-5e" style="visibility:hidden">Higher taxa</span>
		</td>
	</tr>
	<tr>
			<td
			class="cell-module-unused" 
			id="cell-6a"
			title="not in use in your project" 
		>&nbsp;
			
		</td>
		<td
			class="cell-module-activate" 
			title="activate" 
			onclick="moduleChangeModuleStatus(this)"
			id="cell-6b"
		>&nbsp;
			
		</td>
		<td
			class="cell-module-invisible" 
			title="" 
			onclick="moduleChangeModuleStatus(this)"
			id="cell-6c"
		>&nbsp;
			
		</td>	
		<td>
			<span
				class="cell-module-title-unused" 
				id="cell-6d">
			<span class="cell-module-title">Text key</span> - Dichotomic key based on text only</span>
			<span id="cell-6e" style="visibility:hidden">Text key</span>
		</td>
	</tr>
	<tr>
				<td
			title="in use in your project"
			class="cell-module-in-use" 
			id="cell-7a"
		>&nbsp;
			
		</td>
		<td 
			class="cell-module-deactivate" 
			title="deactivate (no data will be deleted)" 
			onclick="moduleChangeModuleStatus(this)"
			id="cell-7b"
		>&nbsp;
			
		</td>
		<td
			class="cell-module-invisible" 
			title="" 
			onclick="moduleChangeModuleStatus(this)"
			id="cell-7c"
		>&nbsp;
		</td>
		<td>
			<span 
				class="cell-module-title-in-use" 
				id="cell-7d">
			<span class="cell-module-title">Picture key</span> - Dichotomic key based on pictures and text</span>
			<span id="cell-7e" style="visibility:hidden">Picture key</span>
		</td>
	
</tr>
	<tr>
			<td
			class="cell-module-unused" 
			id="cell-8a"
			title="not in use in your project" 
		>&nbsp;
			
		</td>
		<td
			class="cell-module-activate" 
			title="activate" 
			onclick="moduleChangeModuleStatus(this)"
			id="cell-8b"
		>&nbsp;
			
		</td>
		<td
			class="cell-module-invisible" 
			title="" 
			onclick="moduleChangeModuleStatus(this)"
			id="cell-8c"
		>&nbsp;
			
		</td>	
		<td>
			<span
				class="cell-module-title-unused" 
				id="cell-8d">
			<span class="cell-module-title">Matrix key</span> - Key based on attributes</span>
			<span id="cell-8e" style="visibility:hidden">Matrix key</span>
		</td>
	</tr>
	<tr>
				<td
			title="in use in your project"
			class="cell-module-in-use" 
			id="cell-9a"
		>&nbsp;
			
		</td>
		<td 
			class="cell-module-deactivate" 
			title="deactivate (no data will be deleted)" 
			onclick="moduleChangeModuleStatus(this)"
			id="cell-9b"
		>&nbsp;
			
		</td>
		<td
			class="cell-module-invisible" 
			title="" 
			onclick="moduleChangeModuleStatus(this)"
			id="cell-9c"
		>&nbsp;
		</td>
		<td>
			<span 
				class="cell-module-title-in-use" 
				id="cell-9d">
			<span class="cell-module-title">Map key</span> - Key based on species distribution</span>
			<span id="cell-9e" style="visibility:hidden">Map key</span>
		</td>
	
</tr>
</table>
</div>

<br />

<div class="text-block">
Besides these standard modules, you can add up to five extra content modules to your project:<br />
<table>
	<tr id="row-f17">
			<td
			title="in use in your project"
			class="cell-module-in-use" 
			id="cell-f17a"
		>&nbsp;
			
		</td>
		<td 
			class="cell-module-deactivate" 
			title="deactivate (no data will be deleted)" 
			onclick="moduleChangeModuleStatus(this)"
			id="cell-f17b"
		>&nbsp;
			
		</td>
		<td
			class="cell-module-invisible" 
			title="" 
			onclick="moduleChangeModuleStatus(this,['row-f17'])"
			id="cell-f17c"
		>&nbsp;
		</td>
		<td>
			<span 
				class="cell-module-title-in-use" 
				id="cell-f17d">
			<span class="cell-module-title">Animal sounds</span></span>
			<span id="cell-f17e" style="visibility:hidden">Animal sounds</span>
		</td>
	</tr>
	<tr id="row-f28">
			<td
			title="in use in your project"
			class="cell-module-in-use" 
			id="cell-f28a"
		>&nbsp;
			
		</td>
		<td 
			class="cell-module-deactivate" 
			title="deactivate (no data will be deleted)" 
			onclick="moduleChangeModuleStatus(this)"
			id="cell-f28b"
		>&nbsp;
			
		</td>
		<td
			class="cell-module-invisible" 
			title="" 
			onclick="moduleChangeModuleStatus(this,['row-f28'])"
			id="cell-f28c"
		>&nbsp;
		</td>
		<td>
			<span 
				class="cell-module-title-in-use" 
				id="cell-f28d">
			<span class="cell-module-title">Wookel</span></span>
			<span id="cell-f28e" style="visibility:hidden">Wookel</span>
		</td>
	</tr>
</table>

<table id="new-input" class="">
<tr>
	<td colspan="4">&nbsp;</td>
</tr>
<tr >
	<td colspan="4">
		<form action="" method="post">
		<input type="hidden" name="rnd" value="263029786">
		Enter new module's name: <input type="text" name="module_new" id="module_new" value="" maxlength="32" />
		<input type="submit" value="add module" onclick="addFreeModule();" />
		</form>	
	</td>
</tr>
</table>

</div>

</div>

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
249
a:4:{s:8:"template";a:4:{s:11:"modules.tpl";b:1;s:26:"../shared/admin-header.tpl";b:1;s:28:"../shared/admin-messages.tpl";b:1;s:26:"../shared/admin-footer.tpl";b:1;}s:9:"timestamp";i:1283337024;s:7:"expires";i:1283340624;s:13:"cache_serials";a:0:{}}<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />

	<title>Imaginary Beings - Project modules</title>

	<link href="/admin/images/system/favicon.ico" rel="shortcut icon" type="image/x-icon" />
	<link href="/admin/images/system/favicon.ico" rel="icon" type="image/x-icon" />

	<style type="text/css" media="all">
		@import url("/admin/style/main.css");
		@import url("/admin/style/admin.css");
	</style>

	<script type="text/javascript" src="/admin/javascript/jquery-1.4.2.min.js"></script>
	<script type="text/javascript" src="/admin/javascript/main.js"></script>

</head>

<body><div id="admin-body-container">
<div id="admin-header-container">
	<img src="/admin/images/system/linnaeus_logo.png" id="admin-page-lng-logo" />
	<img src="/admin/images/system/eti_logo.png" id="admin-page-eti-logo" />
</div>
<div id="admin-page-container">

<div id="admin-titles">
	<span id="admin-title">Linnaeus NG Administration v0.1</span><br />
	<span id="admin-project-title">Imaginary Beings</span><br />	<span id="admin-apptitle"><a href="index.php">Project administration</a></span><br />	<span id="admin-pagetitle">Project modules</span>
</div>


<div id="admin-main">

<div class="admin-text-block">
Select the standard modules you wish to use in your project:<br />
<table>
	<tr>
				<td
			title="in use in your project, but inactive" 
			class="admin-td-module-inactive"
			id="cell-1a"
		>&nbsp;
			
		</td>
		<td 
			class="admin-td-module-reactivate" 
			title="re-activate" 
			onclick="moduleAction(this)"
			id="cell-1b"
		>&nbsp;
			
		</td>
		<td
			class="admin-td-module-delete" 
			title="delete module and data" 
			onclick="moduleAction(this)"
			id="cell-1c"
		>&nbsp;
			
		</td>
		<td>
			<span
				class="admin-td-module-title-deactivated" 
				id="cell-1d">
			<span class="admin-td-module-title">Introduction</span> - Comprehensive project introduction
			<span id="cell-1e" style="visibility:hidden">Introduction</span>
			</span>
		</td>
	
</tr>
	<tr>
				<td
			title="in use in your project"
			class="admin-td-module-inuse" 
			id="cell-2a"
		>&nbsp;
			
		</td>
		<td 
			class="admin-td-module-deactivate" 
			title="deactivate (no data will be deleted)" 
			onclick="moduleAction(this)"
			id="cell-2b"
		>&nbsp;
			
		</td>
		<td
			class="admin-td-module-invisible" 
			title="" 
			onclick="moduleAction(this)"
			id="cell-2c"
		>&nbsp;
		</td>
		<td>
			<span 
				class="admin-td-module-title-inuse" 
				id="cell-2d">
			<span class="admin-td-module-title">Glossary</span> - Project glossary</span>
			<span id="cell-2e" style="visibility:hidden">Glossary</span>
		</td>
	
</tr>
	<tr>
				<td
			title="in use in your project"
			class="admin-td-module-inuse" 
			id="cell-3a"
		>&nbsp;
			
		</td>
		<td 
			class="admin-td-module-deactivate" 
			title="deactivate (no data will be deleted)" 
			onclick="moduleAction(this)"
			id="cell-3b"
		>&nbsp;
			
		</td>
		<td
			class="admin-td-module-invisible" 
			title="" 
			onclick="moduleAction(this)"
			id="cell-3c"
		>&nbsp;
		</td>
		<td>
			<span 
				class="admin-td-module-title-inuse" 
				id="cell-3d">
			<span class="admin-td-module-title">Literature</span> - Literary references</span>
			<span id="cell-3e" style="visibility:hidden">Literature</span>
		</td>
	
</tr>
	<tr>
			<td
			class="admin-td-module-unused" 
			id="cell-4a"
			title="not in use in your project" 
		>
			&nbsp;
		</td>
		<td
			class="admin-td-module-activate" 
			title="activate" 
			onclick="moduleAction(this)"
			id="cell-4b"
		>&nbsp;
			
		</td>
		<td
			class="admin-td-module-invisible" 
			title="" 
			onclick="moduleAction(this)"
			id="cell-4c"
		>&nbsp;
			
		</td>	
		<td>
			<span
				class="admin-td-module-title-unused" 
				id="cell-4d">
			<span class="admin-td-module-title">Species module</span> - Detailed pages for taxa</span>
			<span id="cell-4e" style="visibility:hidden">Species module</span>
		</td>
	</tr>
	<tr>
			<td
			class="admin-td-module-unused" 
			id="cell-5a"
			title="not in use in your project" 
		>
			&nbsp;
		</td>
		<td
			class="admin-td-module-activate" 
			title="activate" 
			onclick="moduleAction(this)"
			id="cell-5b"
		>&nbsp;
			
		</td>
		<td
			class="admin-td-module-invisible" 
			title="" 
			onclick="moduleAction(this)"
			id="cell-5c"
		>&nbsp;
			
		</td>	
		<td>
			<span
				class="admin-td-module-title-unused" 
				id="cell-5d">
			<span class="admin-td-module-title">Higher taxa</span> - Detailed pages for higher taxa</span>
			<span id="cell-5e" style="visibility:hidden">Higher taxa</span>
		</td>
	</tr>
	<tr>
				<td
			title="in use in your project, but inactive" 
			class="admin-td-module-inactive"
			id="cell-6a"
		>&nbsp;
			
		</td>
		<td 
			class="admin-td-module-reactivate" 
			title="re-activate" 
			onclick="moduleAction(this)"
			id="cell-6b"
		>&nbsp;
			
		</td>
		<td
			class="admin-td-module-delete" 
			title="delete module and data" 
			onclick="moduleAction(this)"
			id="cell-6c"
		>&nbsp;
			
		</td>
		<td>
			<span
				class="admin-td-module-title-deactivated" 
				id="cell-6d">
			<span class="admin-td-module-title">Text key</span> - Dichotomic key based on text only
			<span id="cell-6e" style="visibility:hidden">Text key</span>
			</span>
		</td>
	
</tr>
	<tr>
				<td
			title="in use in your project, but inactive" 
			class="admin-td-module-inactive"
			id="cell-7a"
		>&nbsp;
			
		</td>
		<td 
			class="admin-td-module-reactivate" 
			title="re-activate" 
			onclick="moduleAction(this)"
			id="cell-7b"
		>&nbsp;
			
		</td>
		<td
			class="admin-td-module-delete" 
			title="delete module and data" 
			onclick="moduleAction(this)"
			id="cell-7c"
		>&nbsp;
			
		</td>
		<td>
			<span
				class="admin-td-module-title-deactivated" 
				id="cell-7d">
			<span class="admin-td-module-title">Picture key</span> - Dichotomic key based on pictures and text
			<span id="cell-7e" style="visibility:hidden">Picture key</span>
			</span>
		</td>
	
</tr>
	<tr>
				<td
			title="in use in your project"
			class="admin-td-module-inuse" 
			id="cell-8a"
		>&nbsp;
			
		</td>
		<td 
			class="admin-td-module-deactivate" 
			title="deactivate (no data will be deleted)" 
			onclick="moduleAction(this)"
			id="cell-8b"
		>&nbsp;
			
		</td>
		<td
			class="admin-td-module-invisible" 
			title="" 
			onclick="moduleAction(this)"
			id="cell-8c"
		>&nbsp;
		</td>
		<td>
			<span 
				class="admin-td-module-title-inuse" 
				id="cell-8d">
			<span class="admin-td-module-title">Matrix key</span> - Key based on attributes</span>
			<span id="cell-8e" style="visibility:hidden">Matrix key</span>
		</td>
	
</tr>
	<tr>
				<td
			title="in use in your project"
			class="admin-td-module-inuse" 
			id="cell-9a"
		>&nbsp;
			
		</td>
		<td 
			class="admin-td-module-deactivate" 
			title="deactivate (no data will be deleted)" 
			onclick="moduleAction(this)"
			id="cell-9b"
		>&nbsp;
			
		</td>
		<td
			class="admin-td-module-invisible" 
			title="" 
			onclick="moduleAction(this)"
			id="cell-9c"
		>&nbsp;
		</td>
		<td>
			<span 
				class="admin-td-module-title-inuse" 
				id="cell-9d">
			<span class="admin-td-module-title">Map key</span> - Key based on species distribution</span>
			<span id="cell-9e" style="visibility:hidden">Map key</span>
		</td>
	
</tr>
</table>
</div>

<br />

<div class="admin-text-block">
Besides these standard modules, you can add up to five extra content modules to your project:<br />
<table>
	<tr id="row-f23">
			<td
			title="in use in your project"
			class="admin-td-module-inuse" 
			id="cell-f23a"
		>&nbsp;
			
		</td>
		<td 
			class="admin-td-module-deactivate" 
			title="deactivate (no data will be deleted)" 
			onclick="moduleAction(this)"
			id="cell-f23b"
		>&nbsp;
			
		</td>
		<td
			class="admin-td-module-invisible" 
			title="" 
			onclick="moduleAction(this,['row-f23'])"
			id="cell-f23c"
		>&nbsp;
		</td>
		<td>
			<span 
				class="admin-td-module-title-inuse" 
				id="cell-f23d">
			<span class="admin-td-module-title">h=Yyyy</span></span>
			<span id="cell-f23e" style="visibility:hidden">h=Yyyy</span>
		</td>
	</tr>
	<tr id="row-f22">
			<td
			title="in use in your project"
			class="admin-td-module-inuse" 
			id="cell-f22a"
		>&nbsp;
			
		</td>
		<td 
			class="admin-td-module-deactivate" 
			title="deactivate (no data will be deleted)" 
			onclick="moduleAction(this)"
			id="cell-f22b"
		>&nbsp;
			
		</td>
		<td
			class="admin-td-module-invisible" 
			title="" 
			onclick="moduleAction(this,['row-f22'])"
			id="cell-f22c"
		>&nbsp;
		</td>
		<td>
			<span 
				class="admin-td-module-title-inuse" 
				id="cell-f22d">
			<span class="admin-td-module-title">BlaDeeBla</span></span>
			<span id="cell-f22e" style="visibility:hidden">BlaDeeBla</span>
		</td>
	</tr>
	<tr id="row-f17">
			<td
			title="in use in your project"
			class="admin-td-module-inuse" 
			id="cell-f17a"
		>&nbsp;
			
		</td>
		<td 
			class="admin-td-module-deactivate" 
			title="deactivate (no data will be deleted)" 
			onclick="moduleAction(this)"
			id="cell-f17b"
		>&nbsp;
			
		</td>
		<td
			class="admin-td-module-invisible" 
			title="" 
			onclick="moduleAction(this,['row-f17'])"
			id="cell-f17c"
		>&nbsp;
		</td>
		<td>
			<span 
				class="admin-td-module-title-inuse" 
				id="cell-f17d">
			<span class="admin-td-module-title">Animal sounds</span></span>
			<span id="cell-f17e" style="visibility:hidden">Animal sounds</span>
		</td>
	</tr>
	<tr id="row-f25">
			<td
			title="in use in your project"
			class="admin-td-module-inuse" 
			id="cell-f25a"
		>&nbsp;
			
		</td>
		<td 
			class="admin-td-module-deactivate" 
			title="deactivate (no data will be deleted)" 
			onclick="moduleAction(this)"
			id="cell-f25b"
		>&nbsp;
			
		</td>
		<td
			class="admin-td-module-invisible" 
			title="" 
			onclick="moduleAction(this,['row-f25'])"
			id="cell-f25c"
		>&nbsp;
		</td>
		<td>
			<span 
				class="admin-td-module-title-inuse" 
				id="cell-f25d">
			<span class="admin-td-module-title">Fuckroni</span></span>
			<span id="cell-f25e" style="visibility:hidden">Fuckroni</span>
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
		<input type="hidden" name="rnd" value="716889780">
		Enter new module's name: <input type="text" name="module_new" id="module_new" value="" maxlength="32" />
		<input type="submit" value="add module" onclick="addFreeModule();" />
		</form>	
	</td>
</tr>
</table>

</div>

</div>

</div ends="admin-page-container">
<div id="admin-footer-container">
	<div id="admin-footer-menu">
		<a href="/admin/admin-index.php">Main index</a>
		<a href="/admin/views/users/logout.php">Log out (logged in as Jorge Luis Borges)</a>
		<br />
	</div>
</div ends="admin-footer-container">
</div ends="admin-body-container"></body>
</html>
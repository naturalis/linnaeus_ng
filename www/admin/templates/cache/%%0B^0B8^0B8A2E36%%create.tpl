248
a:4:{s:8:"template";a:4:{s:10:"create.tpl";b:1;s:26:"../shared/admin-header.tpl";b:1;s:28:"../shared/admin-messages.tpl";b:1;s:26:"../shared/admin-footer.tpl";b:1;}s:9:"timestamp";i:1283349257;s:7:"expires";i:1283352857;s:13:"cache_serials";a:0:{}}<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />

	<title>Imaginary Beings - Create new collaborator</title>

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
	<span id="admin-project-title">Imaginary Beings</span><br />	<span id="admin-apptitle"><a href="index.php">User administration</a></span><br />	<span id="admin-pagetitle">Create new collaborator</span>
</div>


<div id="admin-main">
Please verify the data below. Click 'Save' to save the user data; or 'Back' to return to the previous screen.

<form method="post" action="" name="theForm" id="theForm">
	<input name="id" value="-1" type="hidden" />
	<input name="checked" id="checked" value="1" type="hidden" />

<table>
	<tr>
		<td>username:</td>
		<td>
		uuuuwwwww		</td>
	</tr>
	<tr>
		<td>password:</td>
		<td>
		uuuuuuuu		</td>
	</tr>
	<tr>
		<td>first_name:</td>
		<td>
		uuuu		</td>
	</tr>
	<tr>
		<td>last_name:</td>
		<td>
		uuuu		</td>
	</tr>
	<tr>
		<td>gender:</td>
		<td>
		f		</td>
	</tr>
	<tr>
		<td>email_address:</td>
		<td>
		uuuu@rrr.nnn		</td>
	</tr>
	<tr>
		<td>role in current project:</td>
		<td>
		Expert		</td>
	</tr>
	<tr>
		<td colspan="2">
					<input
				type="button" 
				value="Back" 
				onclick="document.getElementById('checked').value='-1';document.getElementById('theForm').submit()" />
					<input type="submit" value="Save" />
		</td>
	</tr>
</table>

</form>

</div>



<script type="text/JavaScript">

$(document).ready(function(){

	$('#username').focus();

});

</script>


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
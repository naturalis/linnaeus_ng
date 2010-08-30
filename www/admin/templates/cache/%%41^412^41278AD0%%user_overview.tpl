215
a:4:{s:8:"template";a:3:{s:17:"user_overview.tpl";b:1;s:26:"../shared/admin-header.tpl";b:1;s:26:"../shared/admin-footer.tpl";b:1;}s:9:"timestamp";i:1283158836;s:7:"expires";i:1283162436;s:13:"cache_serials";a:0:{}}<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />

	<title>Imaginary Beings - Project collaborator overview</title>

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
	<img src="/admin/images/system/linnaeus_logo.png" id="admin-page-eti-logo" />
</div>
<div id="admin-page-container">

<div id="admin-titles">
	<span id="admin-title">Linnaeus NG Administration v0.1</span><br />
	<span id="admin-project-title">Imaginary Beings</span><br />	<span id="admin-apptitle"><a href="index.php">User administration</a></span><br />	<span id="admin-pagetitle">Project collaborator overview</span>
</div>


<div id="admin-main">
<table>
<tr>
	<th onclick="tableColumnSort('id');">id</th>
	<th onclick="tableColumnSort('first_name');">first name</th>
	<th onclick="tableColumnSort('last_name');">last name</th>
	<th onclick="tableColumnSort('gender');">gender</th>
	<th onclick="tableColumnSort('email_address');">e-mail</th>
	<th onclick="tableColumnSort('role');">role</th>
	<th></th>
	<th></th>
</tr>
<tr>
	<td>4</td>
	<td>Wouter</td>
	<td>Addink</td>
	<td>m</td>
	<td>waddink@eti.uva.nl</td>
	<td>Contributor</td>
	<td>[<a href="view.php?id=4">view</a>]</td>
	<td>[<a href="edit.php?id=4">edit</a>]</td>
</tr>
<tr>
	<td>2</td>
	<td>Jorge Luis</td>
	<td>Borges</td>
	<td>m</td>
	<td>slavlab@xs4all.nl</td>
	<td>Lead expert</td>
	<td>[<a href="view.php?id=2">view</a>]</td>
	<td>[<a href="edit.php?id=2">edit</a>]</td>
</tr>
<tr>
	<td>1</td>
	<td>Maarten</td>
	<td>Schermer</td>
	<td>m</td>
	<td>maarten.schermer@xs4all.nl</td>
	<td>Expert</td>
	<td>[<a href="view.php?id=1">view</a>]</td>
	<td>[<a href="edit.php?id=1">edit</a>]</td>
</tr>
</table>
</div>

<form method="post" action="" name="postForm" id="postForm">
<input type="hidden" name="key" id="key" value="last_name" />
<input type="hidden" name="dir" value="asc"  />
</form>

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
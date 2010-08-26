205
a:4:{s:8:"template";a:3:{s:8:"edit.tpl";b:1;s:26:"../shared/admin-header.tpl";b:1;s:26:"../shared/admin-footer.tpl";b:1;}s:9:"timestamp";i:1282827585;s:7:"expires";i:1282831185;s:13:"cache_serials";a:0:{}}<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Polar Bears of Amsterdam - Edit project user</title>
<style type="text/css" media="all">
  @import url("../../style/main.css");
  @import url("../../style/admin.css");
</style>
<script type="text/javascript" src="../../javascript/main.js"></script>
</head>
<body><div id="admin-body-container">
<div id="admin-header-container">
	<img src="../../images/system/eti-logo.png" id="admin-page-eti-logo" />
</div>
<div id="admin-page-container">

<div id="admin-titles">
	<span id="admin-title">Linnaeus NG Administration v0.1</span><br />
	<span id="admin-project-title">Polar Bears of Amsterdam</span><br />	<span id="admin-subtitle">Edit project user</span>
</div>

<div id="inlineHelp">
	<div id="inlineHelp-title" onclick="toggleHelpVisibility();">Help</div>
	<div class="inlineHelp-body-hidden" id="inlineHelp-body">
		<div class="inlineHelp-subject">Role</div>
		<div class="inlineHelp-text">The 'role' indicates the role this user will have in the current project. Hover your mouse over the role's names to see a short description.</div>
		<div class="inlineHelp-subject">Active</div>
		<div class="inlineHelp-text">'Active' indicates whether a user is actively working on the current project. When set to 'n', the user can no longer log in or work on the project. It allows you to temporarily disable users without deleting them outright.<br />Users that have the role of 'Lead expert' cannot change role, or be made in-active, as they are the lead manager of a project.</div>
	</div>
</div>

<div id="admin-main">
<form method="post" action="" name="theForm" id="theForm">
<input name="id" value="1" type="hidden" />
<input name="checked" id="checked" value="1" type="hidden" />
<input name="delete" id="delete" value="0" type="hidden" />
<input name="userProjectRole" value="1" type="hidden" />

<table>
	<tr>
		<td>username</td>
		<td><input type="text" name="username" value="mdschermer" maxlength="16" /></td>
	</tr>
	<tr>
		<td>password</td>
		<td><input type="password" name="password" value="" maxlength="16" />&nbsp;(leave blank to leave unchanged)</td>
	</tr>
	<tr>
		<td>password (repeat)</td>
		<td><input type="password" name="password_2" value="" maxlength="16" />&nbsp;(leave blank to leave unchanged)</td>
	</tr>
	<tr>
		<td>first_name</td><td><input type="text" name="first_name" value="Maarten" maxlength="16" /></td>
	</tr>
	<tr>
		<td>last_name</td><td><input type="text" name="last_name" value="Schermer" maxlength="16" /></td>
	</tr>
	<tr>
		<td>gender</td>
		<td>
			<label for="gender-f"><input type="radio" id="gender-f" name="gender" value="f" />f</label>
			<label for="gender-m"><input type="radio" id="gender-m" name="gender" value="m" checked="checked" />m</label>
		</td>
	</tr>
	<tr>
		<td>email_address</td>
		<td><input type="text" name="email_address" value="maarten.schermer@xs4all.nl" maxlength="64" /></td>
	</tr>

	<tr>
		<td>role in current project:</td>
		<td>
		Lead expert</td>
	</tr>
	<tr>
		<td>active</td>
		<td>
			<label for="active-y">
				<input
					type="radio" 
					id="active-y" 
					name="active" 
					value="1"
					 disabled="disabled" 
					checked="checked"/>y
			</label>
			<label for="active-n">
				<input
					type="radio" 
					id="active-n" 
					name="active" 
					value="0" 
					 disabled="disabled" 
					 />n
			</label>
		</td>
	</tr>
	<tr>
		<td colspan="2">&nbsp;</td>
	</tr>
	<tr>
		<td colspan="2">
			<input type="submit" value="Save" />
			
			<input type="button" value="Delete" onclick="if (confirm('Are you sure?')) { var e = document.getElementById('delete'); e.value = '1'; e = document.getElementById('theForm'); e.submit(); } " />
			
			<input type="button" value="Back" onclick="window.open('user_overview.php','_self');" />
		</td>
	</tr>
</table>
</form>

</div>


</div ends="admin-page-container">
<div id="admin-footer-container">
	<div id="admin-footer-menu">
		<a href="index.php">User management index</a>
		<a href="/admin/admin-index.php">Main index</a>
		<a href="choose_project.php">Switch projects</a>
		<a href="logout.php">Log out</a>
	</div>
</div ends="admin-footer-container">
</div ends="admin-body-container"></body>
</html>
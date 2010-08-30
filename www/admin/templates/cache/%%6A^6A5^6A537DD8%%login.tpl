206
a:4:{s:8:"template";a:3:{s:9:"login.tpl";b:1;s:26:"../shared/admin-header.tpl";b:1;s:26:"../shared/admin-footer.tpl";b:1;}s:9:"timestamp";i:1283181560;s:7:"expires";i:1283185160;s:13:"cache_serials";a:0:{}}<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />

	<title>Login</title>

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
	<span id="admin-apptitle"><a href="index.php">User administration</a></span><br />	<span id="admin-pagetitle">Login</span>
</div>

<div id="inlineHelp">
	<div id="inlineHelp-title" onclick="toggleHelpVisibility();">Help</div>
	<div class="inlineHelp-body-hidden" id="inlineHelp-body">
		<div class="inlineHelp-subject">Logging in</div>
		<div class="inlineHelp-text">To log in, fill in your Linnaeus NG-username and password, and press the button labeled "Login".</div>
		<div class="inlineHelp-subject">Problems logging in?</div>
		<div class="inlineHelp-text">If you cannot login, please <a href="mailto:helpdesk@linnaeus.eti.uva.nl">contact the helpdesk</a>.</div>
	</div>
</div>


<div id="admin-main">
	<form method="post" action="login.php">
	<table>
		<tr><td colspan="2">Please enter your username and password and click 'Login'.</td></tr>
		<tr><td>Your username:</td><td><input type="text" name="username" id="username" value="" maxlength="32" /></td></tr>
		<tr><td>Your password:</td><td><input type="password" name="password" value="" maxlength="32" /><br /></td></tr>
		<tr><td colspan="2"><input type="submit" value="login" /></td></tr>
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
</div ends="admin-footer-container">
</div ends="admin-body-container"></body>
</html>
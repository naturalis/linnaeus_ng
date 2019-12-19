<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<!-- meta name="viewport" content="initial-scale=1.0, user-scalable=no" / --><!-- might be required for google map -->

	<title>Select a project to work on</title>

	<link href="admin/media/system/favicon.ico" rel="shortcut icon" type="image/x-icon" />
	<link href="admin/media/system/favicon.ico" rel="icon" type="image/x-icon" />
	<style type="text/css" media="all">
		@import url("admin/style/main.css");
		@import url("admin/style/admin-inputs.css");
		@import url("admin/style/admin-help.css");
		@import url("admin/style/admin.css");
	</style>
	<script type="text/javascript" src="admin/javascript/jquery-1.4.2.min.js"></script>
	<script type="text/javascript" src="admin/javascript/sprintf-0.7-beta1.js"></script>
	<script type="text/javascript" src="admin/javascript/main.js"></script>	
</head>
<body><div id="body-container">

<div id="header-container">
	<img src="admin/media/system/logo_linnaeus_ng.png" id="lng-logo">

	<!-- div class="header-branding">
		Linnæus NG&trade;
	</div -->

	<div class="header-user">
	</div>
</div>

{if !empty($error)}
<div id="page-block-errors">
<span class="message-error">{$error}</span><br />
</div>
{/if}


<div id="page-main">Welcome to Linnaeus NG.<br />
<br />
{if $hasEntryProgram && $showEntryProgramLink}
To use the administration, follow <a href="admin/views/users/login.php">this link</a>.<br />
{/if}
To use an application, follow one of the following links:<br />
<ul>
{foreach from=$projects key=k item=v}
<li><span class="a" onclick="$('#p').val('{$v.id}');$('#theForm').submit();">{$v.title}</span></li>
{/foreach}
</ul>
</div>
<form method="post" id="theForm" action="app/views/linnaeus/set_project.php">
<input type="hidden" name="p" id="p" value="" />
<input type="hidden" name="rnd" value="{1|rand:99999999}" />
</form>

</div>
<div id="footer-container"></div ends="footer-container">
</div ends="body-container">

</body>
</html>

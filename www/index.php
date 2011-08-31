<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

  <title></title>

  <link href="admin/media/system/favicon.ico" rel="shortcut icon" type="image/x-icon" />
  <link href="admin/media/system/favicon.ico" rel="icon" type="image/x-icon" />

  <style type="text/css" media="all">
    @import url("admin/style/main.css");
    @import url("admin/style/admin-inputs.css");
    @import url("admin/style/admin-help.css");
    @import url("admin/style/admin.css");
  </style>

  <script type="text/javascript" src="admin/javascript/jquery-1.4.2.min.js"></script>
  <script type="text/javascript" src="admin/javascript/main.js"></script>


</head>

<body><div id="body-container">
<div id="header-container">
  <a href="admin/admin-index.php"><img src="admin/media/system/linnaeus_logo.png" id="lng-logo" />
  <img src="admin/media/system/eti_logo.png" id="eti-logo" /></a>


</div>

<div id="page-container">

<div id="page-header-titles"><span id="page-header-title">Linnaeus NG</span><br />
<span id="admin-subtitle"></span></div>

<div id="page-main">Welcome to Linnaeus NG.<br />
<br />
To use the administration, follow <a href="admin/views/users/login.php">this link</a>.<br />
To use an application, follow one of the following links:<br />
<ul>
<li><span class="pseudo-a" onclick="$('#p').val('161');$('#theForm').submit();">Euphausiids</span></li>
<li><span class="pseudo-a" onclick="$('#p').val('imaginary_beings');$('#theForm').submit();">Imaginary Beings Of The Literary World</span></li>
<li><span class="pseudo-a" onclick="$('#p').val('1');$('#theForm').submit();">Polar Bears Of Amsterdam</span></li>
<li><span class="pseudo-a" onclick="$('#p').val('5');$('#theForm').submit();">Lemuren</span></li>
<li><span class="pseudo-a" onclick="$('#p').val('64');$('#theForm').submit();">TanBIF</span></li>
</ul>
<br />
</div>
<form method="post" id="theForm" action="app/views/linnaeus/set_project.php">
<input type="hidden" name="p" id="p" value="" />
<input type="hidden" name="rnd" value="<?php echo uniqid(null,true); ?>" />
</form>

</div ends="page-container">
<div id="footer-container"></div ends="footer-container">
</div ends="body-container">

</body>
</html>

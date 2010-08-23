206
a:4:{s:8:"template";a:3:{s:9:"login.tpl";b:1;s:26:"../shared/admin-header.tpl";b:1;s:26:"../shared/admin-footer.tpl";b:1;}s:9:"timestamp";i:1282576412;s:7:"expires";i:1282580012;s:13:"cache_serials";a:0:{}}<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Polar Bears of Amsterdam</title>
<style type="text/css" media="all">
  @import url("/admin/style/main.css");
</style>
</head>
<body>
<div id="admin-titles">
<span id="admin-title">Administration</span><br />
<span id="admin-subtitle">Login page</span>
</div>

<div id="admin-messages">
</div>

<div id="admin-main">
<form method="post" action="login.php">
your username:<input type="text" name="username" value="mdschermer" maxlength="32" /><br />
your password:<input type="text" name="password" value="balance" maxlength="32" /><br />
<input type="submit" value="login" />
</form>
</div>

<div id="admin-bottom">
</div>



<style>
#debug {
	white-space:pre;
	font-size:10px;
	margin-top:15px;
	color:#666;
	font-family:Arial, Helvetica, sans-serif;
}
</style>
<hr style="border-top:1px dotted #ddd;" />
<span onclick="var a=document.getElementById('debug'); if(a.style.visibility=='visible') {a.style.visibility='collapse';} else {a.style.visibility='visible';} " style="cursor:pointer">&nbsp;&Delta;&nbsp;</span>

<div id="debug" style="visibility:collapse;">
array(5) {
  ["login_start_page"]=>
  string(28) "/admin/views/users/index.php"
  ["_current_project_id"]=>
  string(1) "1"
  ["_current_project_name"]=>
  string(24) "Polar Bears of Amsterdam"
  ["data"]=>
  array(0) {
  }
  ["history"]=>
  array(1) {
    [0]=>
    array(2) {
      ["time"]=>
      int(1282576412)
      ["url"]=>
      string(28) "/admin/views/users/login.php"
    }
  }
}
</div>
</body>
</html>
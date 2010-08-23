254
a:4:{s:8:"template";a:4:{s:18:"choose_project.tpl";b:1;s:26:"../shared/admin-header.tpl";b:1;s:26:"../shared/admin-bottom.tpl";b:1;s:26:"../shared/admin-footer.tpl";b:1;}s:9:"timestamp";i:1282561610;s:7:"expires";i:1282565210;s:13:"cache_serials";a:0:{}}<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Imaginary Beings</title>
</head>
<body>
<div id="admin-titles">
<span id="admin-title">Administration menu</span><br />
<span id="admin-subtitle">Choose you project</span>
</div>

<div id="admin-main">
<ul class="admin-list">
<li><a href="?project_id=1">Polar Bears of Amsterdam</a></li>
<li><a href="?project_id=2">Imaginary Beings</a></li>

</ul>
</div>

<div id="admin-bottom">
<a href="index.php">Back to index</a>&nbsp;
<a href="choose_project.php">Switch projects</a>&nbsp;
<a href="logout.php">Logout</a>
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
  ["history"]=>
  array(10) {
    [0]=>
    array(2) {
      ["time"]=>
      int(1282561546)
      ["url"]=>
      string(28) "/admin/views/users/index.php"
    }
    [1]=>
    array(2) {
      ["time"]=>
      int(1282561547)
      ["url"]=>
      string(29) "/admin/views/users/create.php"
    }
    [2]=>
    array(2) {
      ["time"]=>
      int(1282561602)
      ["url"]=>
      string(28) "/admin/views/users/index.php"
    }
    [3]=>
    array(2) {
      ["time"]=>
      int(1282561605)
      ["url"]=>
      string(37) "/admin/views/users/choose_project.php"
    }
    [4]=>
    array(2) {
      ["time"]=>
      int(1282561605)
      ["url"]=>
      string(28) "/admin/views/users/index.php"
    }
    [5]=>
    array(2) {
      ["time"]=>
      int(1282561606)
      ["url"]=>
      string(29) "/admin/views/users/create.php"
    }
    [6]=>
    array(2) {
      ["time"]=>
      int(1282561606)
      ["url"]=>
      string(28) "/admin/views/users/index.php"
    }
    [7]=>
    array(2) {
      ["time"]=>
      int(1282561608)
      ["url"]=>
      string(29) "/admin/views/users/create.php"
    }
    [8]=>
    array(2) {
      ["time"]=>
      int(1282561608)
      ["url"]=>
      string(28) "/admin/views/users/index.php"
    }
    [9]=>
    array(2) {
      ["time"]=>
      int(1282561610)
      ["url"]=>
      string(37) "/admin/views/users/choose_project.php"
    }
  }
  ["user"]=>
  array(17) {
    ["id"]=>
    string(1) "1"
    ["username"]=>
    string(10) "mdschermer"
    ["password"]=>
    string(32) "2069ca795d8e10a6f9a92dd57d01af10"
    ["first_name"]=>
    string(7) "Maarten"
    ["last_name"]=>
    string(8) "Schermer"
    ["gender"]=>
    string(1) "m"
    ["email_address"]=>
    string(26) "maarten.schermer@xs4all.nl"
    ["active"]=>
    string(1) "1"
    ["last_login"]=>
    string(19) "2010-08-23 12:34:37"
    ["logins"]=>
    string(3) "200"
    ["password_changed"]=>
    NULL
    ["last_change"]=>
    string(19) "2010-08-23 12:34:37"
    ["created"]=>
    string(19) "2010-08-18 16:36:56"
    ["_login"]=>
    array(2) {
      ["time"]=>
      int(1282560203)
      ["remember"]=>
      bool(false)
    }
    ["_rights"]=>
    array(4) {
      [0]=>
      array(7) {
        ["id"]=>
        string(1) "1"
        ["project_id"]=>
        string(1) "1"
        ["user_id"]=>
        string(1) "1"
        ["right_id"]=>
        string(1) "2"
        ["project"]=>
        string(24) "Polar Bears of Amsterdam"
        ["action"]=>
        string(11) "Create user"
        ["full_path"]=>
        string(29) "/admin/views/users/create.php"
      }
      [1]=>
      array(7) {
        ["id"]=>
        string(1) "2"
        ["project_id"]=>
        string(1) "1"
        ["user_id"]=>
        string(1) "1"
        ["right_id"]=>
        string(1) "3"
        ["project"]=>
        string(24) "Polar Bears of Amsterdam"
        ["action"]=>
        string(9) "Edit user"
        ["full_path"]=>
        string(27) "/admin/views/users/edit.php"
      }
      [2]=>
      array(7) {
        ["id"]=>
        string(1) "3"
        ["project_id"]=>
        string(1) "1"
        ["user_id"]=>
        string(1) "1"
        ["right_id"]=>
        string(1) "4"
        ["project"]=>
        string(24) "Polar Bears of Amsterdam"
        ["action"]=>
        string(9) "View user"
        ["full_path"]=>
        string(27) "/admin/views/users/view.php"
      }
      [3]=>
      array(7) {
        ["id"]=>
        string(1) "4"
        ["project_id"]=>
        string(1) "2"
        ["user_id"]=>
        string(1) "1"
        ["right_id"]=>
        string(1) "4"
        ["project"]=>
        string(16) "Imaginary Beings"
        ["action"]=>
        string(9) "View user"
        ["full_path"]=>
        string(27) "/admin/views/users/view.php"
      }
    }
    ["_current_project_id"]=>
    string(1) "1"
    ["_current_project_name"]=>
    string(24) "Polar Bears of Amsterdam"
  }
  ["_current_project_id"]=>
  string(1) "2"
  ["_current_project_name"]=>
  string(16) "Imaginary Beings"
}
</div>
</body>
</html>
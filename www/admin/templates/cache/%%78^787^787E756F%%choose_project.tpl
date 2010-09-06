216
a:4:{s:8:"template";a:3:{s:18:"choose_project.tpl";b:1;s:26:"../shared/admin-header.tpl";b:1;s:26:"../shared/admin-footer.tpl";b:1;}s:9:"timestamp";i:1283791400;s:7:"expires";i:1283795000;s:13:"cache_serials";a:0:{}}<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />

	<title>Select a project to work on</title>

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
	<img src="/admin/images/system/linnaeus_logo.png" id="lng-logo" />
	<img src="/admin/images/system/eti_logo.png" id="eti-logo" />
</div>
<div id="page-container">

<div id="page-header-titles">
	<span id="page-header-title">Linnaeus NG Administration v0.1</span><br />
	<span id="page-header-appname"><a href="index.php">User administration</a></span><br />	<span id="page-header-pageaction">Select a project to work on</span>
</div>



<div id="page-main">
<ul class="admin-list">
<li>
	<a href="?project_id=2">Imaginary Beings</a>
</li>
<li>
	<a href="?project_id=1">Polar Bears of Amsterdam</a>
<span title="current active project">*</span></li>

</ul>
</div>

</div ends="page-container">

<div id="footer-container">
	<div id="footer-menu">
		<a href="/admin/admin-index.php">Main index</a>
		<a href="/admin/views/users/choose_project.php">Switch projects</a>
		<a href="/admin/views/users/logout.php">Log out (logged in as Maarten Schermer)</a>
		<br />
	</div>
</div ends="footer-container">


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
<span onclick="var a=document.getElementById('debug'); if(a.style.visibility=='visible') {a.style.visibility='hidden';} else {a.style.visibility='visible';} " style="cursor:pointer">&nbsp;&Delta;&nbsp;</span>

<div id="debug" style="visibility:hidden">
array(2) {
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
    string(19) "2010-09-06 18:26:31"
    ["logins"]=>
    string(2) "15"
    ["password_changed"]=>
    NULL
    ["last_change"]=>
    string(19) "2010-09-06 18:26:31"
    ["created"]=>
    string(19) "2010-08-27 14:35:58"
    ["_login"]=>
    array(2) {
      ["time"]=>
      int(1283791350)
      ["remember"]=>
      bool(false)
    }
    ["_roles"]=>
    array(2) {
      [0]=>
      array(7) {
        ["id"]=>
        string(2) "13"
        ["project_id"]=>
        string(1) "2"
        ["role_id"]=>
        string(1) "3"
        ["user_id"]=>
        string(1) "1"
        ["project_name"]=>
        string(16) "Imaginary Beings"
        ["role_name"]=>
        string(6) "Expert"
        ["role_description"]=>
        string(28) "Content manager of a project"
      }
      [1]=>
      array(7) {
        ["id"]=>
        string(2) "12"
        ["project_id"]=>
        string(1) "1"
        ["role_id"]=>
        string(1) "2"
        ["user_id"]=>
        string(1) "1"
        ["project_name"]=>
        string(24) "Polar Bears of Amsterdam"
        ["role_name"]=>
        string(11) "Lead expert"
        ["role_description"]=>
        string(28) "General manager of a project"
      }
    }
    ["_rights"]=>
    array(2) {
      [2]=>
      array(2) {
        ["users"]=>
        array(3) {
          [3]=>
          string(14) "choose_project"
          [6]=>
          string(13) "user_overview"
          [7]=>
          string(4) "view"
        }
        ["projects"]=>
        array(1) {
          [8]=>
          string(1) "*"
        }
      }
      [1]=>
      array(2) {
        ["users"]=>
        array(1) {
          [1]=>
          string(1) "*"
        }
        ["projects"]=>
        array(1) {
          [8]=>
          string(1) "*"
        }
      }
    }
    ["_number_of_projects"]=>
    int(2)
  }
  ["project"]=>
  array(2) {
    ["id"]=>
    string(1) "1"
    ["_current_project_name"]=>
    string(24) "Polar Bears of Amsterdam"
  }
}
</div>
</div ends="body-container"></body>
</html>
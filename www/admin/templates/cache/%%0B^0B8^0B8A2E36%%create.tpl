246
a:4:{s:8:"template";a:4:{s:10:"create.tpl";b:1;s:26:"../shared/admin-header.tpl";b:1;s:26:"../shared/admin-bottom.tpl";b:1;s:26:"../shared/admin-footer.tpl";b:1;}s:9:"timestamp";i:1282576477;s:7:"expires";i:1282580077;s:13:"cache_serials";a:0:{}}<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
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
<span id="admin-title">Administration menu</span><br />
<span id="admin-subtitle">User management: create user</span>
</div>

<div id="admin-main">
Please verify the data below. Click 'Save' to save the user data; or 'Back' to return to the previous screen.
<form method="post" action="" name="theForm" id="theForm">
<input name="id" value="-1" type="hidden" />
<input name="checked" id="checked" value="1" type="hidden" />
<table>
	<tr>
		<td>username</td><td>
					serge
				</td>
	</tr>
	<tr>
		<td>password</td><td>
					m@@ts
				</td>
	</tr>
	<tr>
		<td>first_name</td><td>
					serge
				</td>
	</tr>
	<tr>
		<td>last_name</td><td>
					maats
				</td>
	</tr>
	<tr>
		<td>gender</td>
		<td>
					f
				</td>
	</tr>
	<tr>
		<td>email_address</td><td>
					serge.maats@xs4all.nl
			</tr>
	<tr>
		<td colspan="2">
					<input type="button" value="Back" onclick="document.getElementById('checked').value='-1';document.getElementById('theForm').submit()" />
					<input type="submit" value="Save" />
		</td>
	</tr>
</table>

</form>

</div>

<div id="admin-messages">
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
array(6) {
  ["login_start_page"]=>
  string(28) "/admin/views/users/index.php"
  ["_current_project_id"]=>
  string(1) "1"
  ["_current_project_name"]=>
  string(24) "Polar Bears of Amsterdam"
  ["data"]=>
  array(1) {
    ["new_user"]=>
    array(8) {
      ["id"]=>
      string(2) "-1"
      ["username"]=>
      string(5) "serge"
      ["password"]=>
      string(5) "m@@ts"
      ["password_2"]=>
      string(5) "m@@ts"
      ["first_name"]=>
      string(5) "serge"
      ["last_name"]=>
      string(5) "maats"
      ["gender"]=>
      string(1) "f"
      ["email_address"]=>
      string(21) "serge.maats@xs4all.nl"
    }
  }
  ["history"]=>
  array(3) {
    [0]=>
    array(2) {
      ["time"]=>
      int(1282576414)
      ["url"]=>
      string(28) "/admin/views/users/login.php"
    }
    [1]=>
    array(2) {
      ["time"]=>
      int(1282576414)
      ["url"]=>
      string(28) "/admin/views/users/index.php"
    }
    [2]=>
    array(2) {
      ["time"]=>
      int(1282576477)
      ["url"]=>
      string(29) "/admin/views/users/create.php"
    }
  }
  ["user"]=>
  array(15) {
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
    string(19) "2010-08-23 13:39:16"
    ["logins"]=>
    string(3) "204"
    ["password_changed"]=>
    NULL
    ["last_change"]=>
    string(19) "2010-08-23 13:39:16"
    ["created"]=>
    string(19) "2010-08-18 16:36:56"
    ["_login"]=>
    array(2) {
      ["time"]=>
      int(1282576414)
      ["remember"]=>
      bool(false)
    }
    ["_rights"]=>
    array(6) {
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
      [4]=>
      array(7) {
        ["id"]=>
        string(1) "8"
        ["project_id"]=>
        string(1) "1"
        ["user_id"]=>
        string(1) "1"
        ["right_id"]=>
        string(1) "5"
        ["project"]=>
        string(24) "Polar Bears of Amsterdam"
        ["action"]=>
        string(5) "Login"
        ["full_path"]=>
        string(28) "/admin/views/users/login.php"
      }
      [5]=>
      array(7) {
        ["id"]=>
        string(1) "9"
        ["project_id"]=>
        string(1) "2"
        ["user_id"]=>
        string(1) "1"
        ["right_id"]=>
        string(1) "5"
        ["project"]=>
        string(16) "Imaginary Beings"
        ["action"]=>
        string(5) "Login"
        ["full_path"]=>
        string(28) "/admin/views/users/login.php"
      }
    }
  }
}
</div>
</body>
</html>
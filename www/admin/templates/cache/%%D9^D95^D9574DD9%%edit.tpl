245
a:4:{s:8:"template";a:4:{s:8:"edit.tpl";b:1;s:26:"../shared/admin-header.tpl";b:1;s:28:"../shared/admin-messages.tpl";b:1;s:26:"../shared/admin-footer.tpl";b:1;}s:9:"timestamp";i:1283760045;s:7:"expires";i:1283763645;s:13:"cache_serials";a:0:{}}<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />

	<title>Imaginary Beings - Edit project collaborator</title>

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
	<span id="page-header-projectname">Imaginary Beings</span><br />	<span id="page-header-appname"><a href="index.php">User administration</a></span><br />	<span id="page-header-pageaction">Edit project collaborator</span>
</div>

<div id="block-inline-help">
	<div id="title" onclick="allToggleHelpVisibility();">Help</div>
	<div class="body-collapsed" id="body-visible">
		<div class="subject">Role</div>
		<div class="text">The 'role' indicates the role this user will have in the current project. Hover your mouse over the role's names to see a short description.</div>
		<div class="subject">Active</div>
		<div class="text">'Active' indicates whether a user is actively working on the current project. When set to 'n', the user can no longer log in or work on the project. It allows you to temporarily disable users without deleting them outright.<br />Users that have the role of 'Lead expert' cannot change role, or be made in-active, as they are the lead manager of a project.</div>
	</div>
</div>

<div id="page-main">
<form method="post" action="" name="theForm" id="theForm">
	<input name="id" value="13" type="hidden" />
	<input name="checked" id="checked" value="1" type="hidden" />
	<input name="delete" id="delete" value="0" type="hidden" />
	<input name="userProjectRole" value="22" type="hidden" />
<script type="text/javascript">
	userid = '13';
</script>
<table>
	<tr>
		<td>username</td>
		<td>
			<input
				type="text" 
				name="username" 
				id="username" 
				value="ijsbeer" 
				maxlength="16" 
				onblur="userRemoteValueCheck(this.id,[this.value],['e','f'],userid)" 
			/>
			<span class="asterisk-required-field">*</span>
			<span id="username-message" class=""></span>
		</td>
	</tr>
	<tr>
		<td>password</td>
		<td>
			<input 
				type="password" 
				name="password" 
				id="password" 
				value="" 
				maxlength="16" 
				onblur="if (this.value) { userRemoteValueCheck(this.id,[this.value],['f'],userid); }"
			/>
			<span class="asterisk-required-field">*</span>
			<span id="password-message" class="">(leave blank to leave unchanged)</span>
		</td>
	</tr>
	<tr>
		<td>password (repeat)</td>
		<td>
			<input 
				type="password" 
				name="password_2" 
				id="password_2" 
				value="" 
				maxlength="16" 
				onblur="if (this.value || $('#password.val().)) { userRemoteValueCheck(this.id,[this.value,document.getElementById('password').value],['f','q'],userid); }"
			/>
			<span class="asterisk-required-field">*</span>
			<span id="password_2-message" class="">(leave blank to leave unchanged)</span>
		</td>
	</tr>
	<tr>
		<td>first_name</td>
		<td>
			<input 
				type="text" 
				name="first_name" 
				id="first_name" 
				value="IJs" 
				maxlength="32"
				onblur="userRemoteValueCheck(this.id,[this.value],['f'],userid)"
			/>
			<span class="asterisk-required-field">*</span>
			<span id="first_name-message" class=""></span>
		</td>
	</tr>
	<tr>
		<td>last_name</td>
		<td>
			<input 
				type="text" 
				name="last_name" 
				id="last_name" 
				value="Beer" 
				maxlength="32"
				onblur="userRemoteValueCheck(this.id,[this.value],['f'],userid)"
			/>
			<span class="asterisk-required-field">*</span>
			<span id="last_name-message" class=""></span>
		</td>
	</tr>
	<tr>
		<td>gender</td>
		<td>
			<label for="gender-f">
				<input 
					type="radio" 
					id="gender-f" 
					name="gender" 
					value="f" 				/>f
			</label>
			<label for="gender-m">
				<input
					type="radio" 
					id="gender-m" 
					name="gender" 
					value="m" checked="checked" 
				/>m
			</label>
			<span class="asterisk-required-field">*</span>
		</td>
	</tr>
	<tr>
		<td>email_address</td>
		<td>
			<input 
				type="text" 
				name="email_address" 
				id="email_address" 
				value="ijsbeer@thirdeyefoundation.com" 
				maxlength="64"
				onblur="userRemoteValueCheck(this.id,[this.value],['f','e'],userid)"
			/>
			<span class="asterisk-required-field">*</span>
			<span id="email_address-message" class=""></span>
		</td>
	</tr>

	<tr>
		<td>role in current project:</td>
		<td>
					<select name="role_id">
							<option 
					title="Expert: Content manager of a project" 
					value="3"
									>Expert</option>
							<option 
					title="Editor: Edits specific parts of a project" 
					value="4"
									>Editor</option>
							<option 
					title="Contributor: Contributes to a project but cannot edit (current)" 
					value="5"
					 selected class="option-selected" 				>Contributor</option>
						</select>
		</td>
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
					 
					checked="checked"/>y
			</label>
			<label for="active-n">
				<input
					type="radio" 
					id="active-n" 
					name="active" 
					value="0" 
					 
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



<script type="text/JavaScript">

$(document).ready(function(){

	$('#username').focus();

});

</script>


</div ends="page-container">

<div id="footer-container">
	<div id="footer-menu">
		<a href="/admin/admin-index.php">Main index</a>
		<a href="/admin/views/users/logout.php">Log out (logged in as Jorge Luis Borges)</a>
		<br />
	</div>
</div ends="footer-container">

</div ends="body-container"></body>
</html>
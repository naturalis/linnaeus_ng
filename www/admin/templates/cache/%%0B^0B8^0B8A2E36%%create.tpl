208
a:4:{s:8:"template";a:3:{s:10:"create.tpl";b:1;s:26:"../shared/admin-header.tpl";b:1;s:26:"../shared/admin-footer.tpl";b:1;}s:9:"timestamp";i:1283163688;s:7:"expires";i:1283167288;s:13:"cache_serials";a:0:{}}<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />

	<title>Imaginary Beings - Create new collaborator</title>

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
	<span id="admin-project-title">Imaginary Beings</span><br />	<span id="admin-apptitle"><a href="index.php">User administration</a></span><br />	<span id="admin-pagetitle">Create new collaborator</span>
</div>


<div id="admin-main">

<form method="post" action="" name="theForm" id="theForm">
	<input name="id" value="-1" type="hidden" />
	<input name="checked" id="checked" value="" type="hidden" />

<table>
	<tr>
		<td>username:</td>
		<td>
					<input 
				type="text" 
				name="username" 
				id="username" 
				value="" 
				maxlength="16" 
				onblur="remoteValueCheck(this.id,[this.value],['e','f'])" 
			/>
			<span class="admin-required-field-asterisk">*</span>
			<span id="username-message" class=""></span>			</td>
	</tr>
	<tr>
		<td>password:</td>
		<td>
					<input
				type="password"
				name="password"
				id="password"
				value=""
				maxlength="16"
				onblur="remoteValueCheck(this.id,[this.value],['f'])"
			/>
			<span class="admin-required-field-asterisk">*</span>
			<span id="password-message" class=""></span>	</td>	
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
				onblur="remoteValueCheck(this.id,[this.value,document.getElementById('password').value],['f','q'])"
			/>
			<span class="admin-required-field-asterisk">*</span>
			<span id="password_2-message" class=""></span>			</td>
	</tr>
	<tr>
		<td>first_name:</td>
		<td>
					<input
				type="text"
				name="first_name"
				id="first_name"
				value="" 
				maxlength="32" 
				onblur="remoteValueCheck(this.id,[this.value],['f'])"
			/>
			<span class="admin-required-field-asterisk">*</span>
			<span id="first_name-message" class=""></span>			</td>
	</tr>
	<tr>
		<td>last_name:</td>
		<td>
					<input
				type="text"
				name="last_name"
				id="last_name"
				value=""
				maxlength="32"
				onblur="remoteValueCheck(this.id,[this.value],['f'])"
			/>
			<span class="admin-required-field-asterisk">*</span>
			<span id="last_name-message" class=""></span>			</td>
	</tr>
	<tr>
		<td>gender:</td>
		<td>
					<label for="gender-f">
				<input 
					type="radio" 
					id="gender-f" 
					name="gender" 
					value="f" 
					checked="checked"				/>f
			</label>
			<label for="gender-m">
				<input
					type="radio" 
					id="gender-m" 
					name="gender" 
					value="m"
									 />m
			</label>
			<span class="admin-required-field-asterisk">*</span>
				</td>
	</tr>
	<tr>
		<td>email_address:</td>
		<td>
					<input
				type="text" 
				name="email_address" 
				id="email_address" 
				value="" 
				maxlength="64" 
				onblur="remoteValueCheck(this.id,[this.value],['f','e'])"
			/>
			<span class="admin-required-field-asterisk">*</span>
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
					title="Contributor: Contributes to a project but cannot edit" 
					value="5"
									>Contributor</option>
						</select>
			<span class="admin-required-field-asterisk">*</span>
				</td>
	</tr>
	<tr>
		<td colspan="2">
					<input type="submit" value="Save" />
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
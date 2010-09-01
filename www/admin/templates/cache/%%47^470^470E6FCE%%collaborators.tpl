255
a:4:{s:8:"template";a:4:{s:17:"collaborators.tpl";b:1;s:26:"../shared/admin-header.tpl";b:1;s:28:"../shared/admin-messages.tpl";b:1;s:26:"../shared/admin-footer.tpl";b:1;}s:9:"timestamp";i:1283338980;s:7:"expires";i:1283342580;s:13:"cache_serials";a:0:{}}<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />

	<title>Imaginary Beings - Collaborator tasks</title>

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
	<img src="/admin/images/system/linnaeus_logo.png" id="admin-page-lng-logo" />
	<img src="/admin/images/system/eti_logo.png" id="admin-page-eti-logo" />
</div>
<div id="admin-page-container">

<div id="admin-titles">
	<span id="admin-title">Linnaeus NG Administration v0.1</span><br />
	<span id="admin-project-title">Imaginary Beings</span><br />	<span id="admin-apptitle"><a href="index.php">Project administration</a></span><br />	<span id="admin-pagetitle">Collaborator tasks</span>
</div>


<div id="admin-main">
<div class="admin-text-block">
Select the standard modules you wish to use in your project:<br />
<table>
	<tr>
			<td title="in use in your project, but inactive" class="admin-td-module-inactive" >&nbsp;</td>
		<td style="width:100px">
			<span class="admin-td-module-title-deactivated" id="cell-1d">
					<span class="admin-td-module-title">Introduction</span>
			</span>
		</td>
		<td>
			<span onclick="toggleModuleUsers(1);" style="cursor:pointer"><span id="cell-1n">2</span> collaborators</span>
		</td>
	</tr>
	<tr id="users-1" class="admin-modusers-hidden">
		<td colspan="3">
			<table>
											<tr>
					<td style="width:15px;">
					</td>
									<td 
						id="cell-1-13a"
						class="admin-td-module-title-inuse">
							IJs Beer
						</td>
					<td>Contributor</td>
					<td 
						title="remove collaborator" 
						class="admin-td-moduser-remove"
						id="cell-1-13b"
						onclick="moduleUserAction(this,1,13,'remove')">
					</td>
								</tr>
											<tr>
					<td style="width:15px;">
					</td>
									<td
						id="cell-1-2a"
						class="">
						Jorge Luis Borges
					</td>
					<td>Lead expert</td>
					<td
						title="add collaborator" 
						class="admin-td-moduser-inactive"
						id="cell-1-2b"
						onclick="moduleUserAction(this,1,2,'add')">
					</td>
								</tr>
											<tr>
					<td style="width:15px;">
					</td>
									<td 
						id="cell-1-11a"
						class="admin-td-module-title-inuse">
							Gideon Gijswijt
						</td>
					<td>Editor</td>
					<td 
						title="remove collaborator" 
						class="admin-td-moduser-remove"
						id="cell-1-11b"
						onclick="moduleUserAction(this,1,11,'remove')">
					</td>
								</tr>
											<tr>
					<td style="width:15px;">
					</td>
									<td
						id="cell-1-1a"
						class="">
						Maarten Schermer
					</td>
					<td>Expert</td>
					<td
						title="add collaborator" 
						class="admin-td-moduser-inactive"
						id="cell-1-1b"
						onclick="moduleUserAction(this,1,1,'add')">
					</td>
								</tr>
						
			</table>
		</td>
	</tr>
	<tr>
			<td title="in use in your project" class="admin-td-module-inuse">&nbsp;</td>
		<td style="width:100px">
			<span class="admin-td-module-title-inuse" id="cell-2d">
					<span class="admin-td-module-title">Glossary</span>
			</span>
		</td>
		<td>
			<span onclick="toggleModuleUsers(2);" style="cursor:pointer"><span id="cell-2n">0</span> collaborators</span>
		</td>
	</tr>
	<tr id="users-2" class="admin-modusers-hidden">
		<td colspan="3">
			<table>
											<tr>
					<td style="width:15px;">
					</td>
									<td
						id="cell-2-13a"
						class="">
						IJs Beer
					</td>
					<td>Contributor</td>
					<td
						title="add collaborator" 
						class="admin-td-moduser-inactive"
						id="cell-2-13b"
						onclick="moduleUserAction(this,2,13,'add')">
					</td>
								</tr>
											<tr>
					<td style="width:15px;">
					</td>
									<td
						id="cell-2-2a"
						class="">
						Jorge Luis Borges
					</td>
					<td>Lead expert</td>
					<td
						title="add collaborator" 
						class="admin-td-moduser-inactive"
						id="cell-2-2b"
						onclick="moduleUserAction(this,2,2,'add')">
					</td>
								</tr>
											<tr>
					<td style="width:15px;">
					</td>
									<td
						id="cell-2-11a"
						class="">
						Gideon Gijswijt
					</td>
					<td>Editor</td>
					<td
						title="add collaborator" 
						class="admin-td-moduser-inactive"
						id="cell-2-11b"
						onclick="moduleUserAction(this,2,11,'add')">
					</td>
								</tr>
											<tr>
					<td style="width:15px;">
					</td>
									<td
						id="cell-2-1a"
						class="">
						Maarten Schermer
					</td>
					<td>Expert</td>
					<td
						title="add collaborator" 
						class="admin-td-moduser-inactive"
						id="cell-2-1b"
						onclick="moduleUserAction(this,2,1,'add')">
					</td>
								</tr>
						
			</table>
		</td>
	</tr>
	<tr>
			<td title="in use in your project" class="admin-td-module-inuse">&nbsp;</td>
		<td style="width:100px">
			<span class="admin-td-module-title-inuse" id="cell-3d">
					<span class="admin-td-module-title">Literature</span>
			</span>
		</td>
		<td>
			<span onclick="toggleModuleUsers(3);" style="cursor:pointer"><span id="cell-3n">0</span> collaborators</span>
		</td>
	</tr>
	<tr id="users-3" class="admin-modusers-hidden">
		<td colspan="3">
			<table>
											<tr>
					<td style="width:15px;">
					</td>
									<td
						id="cell-3-13a"
						class="">
						IJs Beer
					</td>
					<td>Contributor</td>
					<td
						title="add collaborator" 
						class="admin-td-moduser-inactive"
						id="cell-3-13b"
						onclick="moduleUserAction(this,3,13,'add')">
					</td>
								</tr>
											<tr>
					<td style="width:15px;">
					</td>
									<td
						id="cell-3-2a"
						class="">
						Jorge Luis Borges
					</td>
					<td>Lead expert</td>
					<td
						title="add collaborator" 
						class="admin-td-moduser-inactive"
						id="cell-3-2b"
						onclick="moduleUserAction(this,3,2,'add')">
					</td>
								</tr>
											<tr>
					<td style="width:15px;">
					</td>
									<td
						id="cell-3-11a"
						class="">
						Gideon Gijswijt
					</td>
					<td>Editor</td>
					<td
						title="add collaborator" 
						class="admin-td-moduser-inactive"
						id="cell-3-11b"
						onclick="moduleUserAction(this,3,11,'add')">
					</td>
								</tr>
											<tr>
					<td style="width:15px;">
					</td>
									<td
						id="cell-3-1a"
						class="">
						Maarten Schermer
					</td>
					<td>Expert</td>
					<td
						title="add collaborator" 
						class="admin-td-moduser-inactive"
						id="cell-3-1b"
						onclick="moduleUserAction(this,3,1,'add')">
					</td>
								</tr>
						
			</table>
		</td>
	</tr>
	<tr>
			<td title="in use in your project" class="admin-td-module-inuse">&nbsp;</td>
		<td style="width:100px">
			<span class="admin-td-module-title-inuse" id="cell-11d">
					<span class="admin-td-module-title">Map key</span>
			</span>
		</td>
		<td>
			<span onclick="toggleModuleUsers(11);" style="cursor:pointer"><span id="cell-11n">0</span> collaborators</span>
		</td>
	</tr>
	<tr id="users-11" class="admin-modusers-hidden">
		<td colspan="3">
			<table>
											<tr>
					<td style="width:15px;">
					</td>
									<td
						id="cell-11-13a"
						class="">
						IJs Beer
					</td>
					<td>Contributor</td>
					<td
						title="add collaborator" 
						class="admin-td-moduser-inactive"
						id="cell-11-13b"
						onclick="moduleUserAction(this,11,13,'add')">
					</td>
								</tr>
											<tr>
					<td style="width:15px;">
					</td>
									<td
						id="cell-11-2a"
						class="">
						Jorge Luis Borges
					</td>
					<td>Lead expert</td>
					<td
						title="add collaborator" 
						class="admin-td-moduser-inactive"
						id="cell-11-2b"
						onclick="moduleUserAction(this,11,2,'add')">
					</td>
								</tr>
											<tr>
					<td style="width:15px;">
					</td>
									<td
						id="cell-11-11a"
						class="">
						Gideon Gijswijt
					</td>
					<td>Editor</td>
					<td
						title="add collaborator" 
						class="admin-td-moduser-inactive"
						id="cell-11-11b"
						onclick="moduleUserAction(this,11,11,'add')">
					</td>
								</tr>
											<tr>
					<td style="width:15px;">
					</td>
									<td
						id="cell-11-1a"
						class="">
						Maarten Schermer
					</td>
					<td>Expert</td>
					<td
						title="add collaborator" 
						class="admin-td-moduser-inactive"
						id="cell-11-1b"
						onclick="moduleUserAction(this,11,1,'add')">
					</td>
								</tr>
						
			</table>
		</td>
	</tr>
	<tr>
			<td title="in use in your project, but inactive" class="admin-td-module-inactive" >&nbsp;</td>
		<td style="width:100px">
			<span class="admin-td-module-title-deactivated" id="cell-6d">
					<span class="admin-td-module-title">Text key</span>
			</span>
		</td>
		<td>
			<span onclick="toggleModuleUsers(6);" style="cursor:pointer"><span id="cell-6n">0</span> collaborators</span>
		</td>
	</tr>
	<tr id="users-6" class="admin-modusers-hidden">
		<td colspan="3">
			<table>
											<tr>
					<td style="width:15px;">
					</td>
									<td
						id="cell-6-13a"
						class="">
						IJs Beer
					</td>
					<td>Contributor</td>
					<td
						title="add collaborator" 
						class="admin-td-moduser-inactive"
						id="cell-6-13b"
						onclick="moduleUserAction(this,6,13,'add')">
					</td>
								</tr>
											<tr>
					<td style="width:15px;">
					</td>
									<td
						id="cell-6-2a"
						class="">
						Jorge Luis Borges
					</td>
					<td>Lead expert</td>
					<td
						title="add collaborator" 
						class="admin-td-moduser-inactive"
						id="cell-6-2b"
						onclick="moduleUserAction(this,6,2,'add')">
					</td>
								</tr>
											<tr>
					<td style="width:15px;">
					</td>
									<td
						id="cell-6-11a"
						class="">
						Gideon Gijswijt
					</td>
					<td>Editor</td>
					<td
						title="add collaborator" 
						class="admin-td-moduser-inactive"
						id="cell-6-11b"
						onclick="moduleUserAction(this,6,11,'add')">
					</td>
								</tr>
											<tr>
					<td style="width:15px;">
					</td>
									<td
						id="cell-6-1a"
						class="">
						Maarten Schermer
					</td>
					<td>Expert</td>
					<td
						title="add collaborator" 
						class="admin-td-moduser-inactive"
						id="cell-6-1b"
						onclick="moduleUserAction(this,6,1,'add')">
					</td>
								</tr>
						
			</table>
		</td>
	</tr>
	<tr>
			<td title="in use in your project, but inactive" class="admin-td-module-inactive" >&nbsp;</td>
		<td style="width:100px">
			<span class="admin-td-module-title-deactivated" id="cell-13d">
					<span class="admin-td-module-title">Picture key</span>
			</span>
		</td>
		<td>
			<span onclick="toggleModuleUsers(13);" style="cursor:pointer"><span id="cell-13n">0</span> collaborators</span>
		</td>
	</tr>
	<tr id="users-13" class="admin-modusers-hidden">
		<td colspan="3">
			<table>
											<tr>
					<td style="width:15px;">
					</td>
									<td
						id="cell-13-13a"
						class="">
						IJs Beer
					</td>
					<td>Contributor</td>
					<td
						title="add collaborator" 
						class="admin-td-moduser-inactive"
						id="cell-13-13b"
						onclick="moduleUserAction(this,13,13,'add')">
					</td>
								</tr>
											<tr>
					<td style="width:15px;">
					</td>
									<td
						id="cell-13-2a"
						class="">
						Jorge Luis Borges
					</td>
					<td>Lead expert</td>
					<td
						title="add collaborator" 
						class="admin-td-moduser-inactive"
						id="cell-13-2b"
						onclick="moduleUserAction(this,13,2,'add')">
					</td>
								</tr>
											<tr>
					<td style="width:15px;">
					</td>
									<td
						id="cell-13-11a"
						class="">
						Gideon Gijswijt
					</td>
					<td>Editor</td>
					<td
						title="add collaborator" 
						class="admin-td-moduser-inactive"
						id="cell-13-11b"
						onclick="moduleUserAction(this,13,11,'add')">
					</td>
								</tr>
											<tr>
					<td style="width:15px;">
					</td>
									<td
						id="cell-13-1a"
						class="">
						Maarten Schermer
					</td>
					<td>Expert</td>
					<td
						title="add collaborator" 
						class="admin-td-moduser-inactive"
						id="cell-13-1b"
						onclick="moduleUserAction(this,13,1,'add')">
					</td>
								</tr>
						
			</table>
		</td>
	</tr>
	<tr>
			<td title="in use in your project" class="admin-td-module-inuse">&nbsp;</td>
		<td style="width:100px">
			<span class="admin-td-module-title-inuse" id="cell-10d">
					<span class="admin-td-module-title">Matrix key</span>
			</span>
		</td>
		<td>
			<span onclick="toggleModuleUsers(10);" style="cursor:pointer"><span id="cell-10n">0</span> collaborators</span>
		</td>
	</tr>
	<tr id="users-10" class="admin-modusers-hidden">
		<td colspan="3">
			<table>
											<tr>
					<td style="width:15px;">
					</td>
									<td
						id="cell-10-13a"
						class="">
						IJs Beer
					</td>
					<td>Contributor</td>
					<td
						title="add collaborator" 
						class="admin-td-moduser-inactive"
						id="cell-10-13b"
						onclick="moduleUserAction(this,10,13,'add')">
					</td>
								</tr>
											<tr>
					<td style="width:15px;">
					</td>
									<td
						id="cell-10-2a"
						class="">
						Jorge Luis Borges
					</td>
					<td>Lead expert</td>
					<td
						title="add collaborator" 
						class="admin-td-moduser-inactive"
						id="cell-10-2b"
						onclick="moduleUserAction(this,10,2,'add')">
					</td>
								</tr>
											<tr>
					<td style="width:15px;">
					</td>
									<td
						id="cell-10-11a"
						class="">
						Gideon Gijswijt
					</td>
					<td>Editor</td>
					<td
						title="add collaborator" 
						class="admin-td-moduser-inactive"
						id="cell-10-11b"
						onclick="moduleUserAction(this,10,11,'add')">
					</td>
								</tr>
											<tr>
					<td style="width:15px;">
					</td>
									<td
						id="cell-10-1a"
						class="">
						Maarten Schermer
					</td>
					<td>Expert</td>
					<td
						title="add collaborator" 
						class="admin-td-moduser-inactive"
						id="cell-10-1b"
						onclick="moduleUserAction(this,10,1,'add')">
					</td>
								</tr>
						
			</table>
		</td>
	</tr>
	<tr>
			<td title="in use in your project" class="admin-td-module-inuse">&nbsp;</td>
		<td style="width:100px">
			<span class="admin-td-module-title-inuse" id="cell-12d">
					<span class="admin-td-module-title">Map key</span>
			</span>
		</td>
		<td>
			<span onclick="toggleModuleUsers(12);" style="cursor:pointer"><span id="cell-12n">0</span> collaborators</span>
		</td>
	</tr>
	<tr id="users-12" class="admin-modusers-hidden">
		<td colspan="3">
			<table>
											<tr>
					<td style="width:15px;">
					</td>
									<td
						id="cell-12-13a"
						class="">
						IJs Beer
					</td>
					<td>Contributor</td>
					<td
						title="add collaborator" 
						class="admin-td-moduser-inactive"
						id="cell-12-13b"
						onclick="moduleUserAction(this,12,13,'add')">
					</td>
								</tr>
											<tr>
					<td style="width:15px;">
					</td>
									<td
						id="cell-12-2a"
						class="">
						Jorge Luis Borges
					</td>
					<td>Lead expert</td>
					<td
						title="add collaborator" 
						class="admin-td-moduser-inactive"
						id="cell-12-2b"
						onclick="moduleUserAction(this,12,2,'add')">
					</td>
								</tr>
											<tr>
					<td style="width:15px;">
					</td>
									<td
						id="cell-12-11a"
						class="">
						Gideon Gijswijt
					</td>
					<td>Editor</td>
					<td
						title="add collaborator" 
						class="admin-td-moduser-inactive"
						id="cell-12-11b"
						onclick="moduleUserAction(this,12,11,'add')">
					</td>
								</tr>
											<tr>
					<td style="width:15px;">
					</td>
									<td
						id="cell-12-1a"
						class="">
						Maarten Schermer
					</td>
					<td>Expert</td>
					<td
						title="add collaborator" 
						class="admin-td-moduser-inactive"
						id="cell-12-1b"
						onclick="moduleUserAction(this,12,1,'add')">
					</td>
								</tr>
						
			</table>
		</td>
	</tr>
	<tr>
			<td title="in use in your project, but inactive" class="admin-td-module-inactive" >&nbsp;</td>
		<td style="width:100px">
			<span class="admin-td-module-title-deactivated" id="cell-14d">
					<span class="admin-td-module-title">Picture key</span>
			</span>
		</td>
		<td>
			<span onclick="toggleModuleUsers(14);" style="cursor:pointer"><span id="cell-14n">0</span> collaborators</span>
		</td>
	</tr>
	<tr id="users-14" class="admin-modusers-hidden">
		<td colspan="3">
			<table>
											<tr>
					<td style="width:15px;">
					</td>
									<td
						id="cell-14-13a"
						class="">
						IJs Beer
					</td>
					<td>Contributor</td>
					<td
						title="add collaborator" 
						class="admin-td-moduser-inactive"
						id="cell-14-13b"
						onclick="moduleUserAction(this,14,13,'add')">
					</td>
								</tr>
											<tr>
					<td style="width:15px;">
					</td>
									<td
						id="cell-14-2a"
						class="">
						Jorge Luis Borges
					</td>
					<td>Lead expert</td>
					<td
						title="add collaborator" 
						class="admin-td-moduser-inactive"
						id="cell-14-2b"
						onclick="moduleUserAction(this,14,2,'add')">
					</td>
								</tr>
											<tr>
					<td style="width:15px;">
					</td>
									<td
						id="cell-14-11a"
						class="">
						Gideon Gijswijt
					</td>
					<td>Editor</td>
					<td
						title="add collaborator" 
						class="admin-td-moduser-inactive"
						id="cell-14-11b"
						onclick="moduleUserAction(this,14,11,'add')">
					</td>
								</tr>
											<tr>
					<td style="width:15px;">
					</td>
									<td
						id="cell-14-1a"
						class="">
						Maarten Schermer
					</td>
					<td>Expert</td>
					<td
						title="add collaborator" 
						class="admin-td-moduser-inactive"
						id="cell-14-1b"
						onclick="moduleUserAction(this,14,1,'add')">
					</td>
								</tr>
						
			</table>
		</td>
	</tr>
</table>
</div>

<br />

<div class="admin-text-block">
Besides these standard modules, you can add up to five extra content modules to your project:<br />
<table>
	<tr id="row-f23">
			<td title="in use in your project" class="admin-td-module-inuse" id="cell-f23a">&nbsp;</td>
		<td>
			<span class="admin-td-module-title-inuse" id="cell-f23d">
					<span class="admin-td-module-title">h=Yyyy</span>
			</span>
		</td>
</tr>
	<tr id="row-f22">
			<td title="in use in your project" class="admin-td-module-inuse" id="cell-f22a">&nbsp;</td>
		<td>
			<span class="admin-td-module-title-inuse" id="cell-f22d">
					<span class="admin-td-module-title">BlaDeeBla</span>
			</span>
		</td>
</tr>
	<tr id="row-f17">
			<td title="in use in your project" class="admin-td-module-inuse" id="cell-f17a">&nbsp;</td>
		<td>
			<span class="admin-td-module-title-inuse" id="cell-f17d">
					<span class="admin-td-module-title">Animal sounds</span>
			</span>
		</td>
</tr>
	<tr id="row-f25">
			<td title="in use in your project" class="admin-td-module-inuse" id="cell-f25a">&nbsp;</td>
		<td>
			<span class="admin-td-module-title-inuse" id="cell-f25d">
					<span class="admin-td-module-title">Fuckroni</span>
			</span>
		</td>
</tr>
</table>

</div>

</div>

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
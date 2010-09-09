255
a:4:{s:8:"template";a:4:{s:17:"collaborators.tpl";b:1;s:26:"../shared/admin-header.tpl";b:1;s:28:"../shared/admin-messages.tpl";b:1;s:26:"../shared/admin-footer.tpl";b:1;}s:9:"timestamp";i:1284048385;s:7:"expires";i:1284051985;s:13:"cache_serials";a:0:{}}<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

	<title>Imaginary Beings - Assign collaborator to modules</title>

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
	<a href="/admin/admin-index.php"><img src="/admin/images/system/linnaeus_logo.png" id="lng-logo" />
	<img src="/admin/images/system/eti_logo.png" id="eti-logo" /></a>
</div>
<div id="page-container">

<div id="page-header-titles">
	<span id="page-header-title">Linnaeus NG Administration v0.1</span>
	<br />
	<div id="breadcrumbs">
				<span class="crumb"><a href="/admin/views/users/choose_project.php">Projects</a></span>
		<span class="crumb-arrow">&rarr;</span>
					<span class="crumb"><a href="/admin/admin-index.php">Imaginary Beings</a></span>
		<span class="crumb-arrow">&rarr;</span>
					<span class="crumb"><a href="/admin/views/projects">Project administration</a></span>
		<span class="crumb-arrow">&rarr;</span>
					<span id="crumb-current">Assign collaborator to modules</span>
		<span class="crumb-arrow">&nbsp;</span>
			</div>
</div>




<div id="page-main">
	<div class="text-block">

	Assign collaborators to work on modules:<br />

	<table>
			<tr>
					<td title="in use in your project" class="cell-module-in-use">&nbsp;</td>
			<td>
				<span class="cell-module-title-in-use" id="cell-1d">
							<span class="cell-module-title">Introduction</span>
				</span>
			</td>
			<td>
				<span onclick="moduleToggleModuleUserBlock(1);" class="modusers-block-toggle">
					<span id="cell-1n">2</span> 
					collaborators
				</span>
			</td>
		</tr>
		<tr id="users-1" class="modusers-block-hidden">
			<td colspan="3">
				<table>
														<tr>
						<td class="modusers-block-buffercell"></td>
											<td
							id="cell-1-15a"
							class="">
							System Administrator
						</td>
						<td>System administrator</td>
						<td
							title="add collaborator" 
							class="cell-moduser-inactive"
							id="cell-1-15b"
							onclick="moduleChangeModuleUserStatus(this,1,15,'add')">
						</td>
										</tr>
														<tr>
						<td class="modusers-block-buffercell"></td>
											<td
							id="cell-1-2a"
							class="">
							Jorge Luis Borges
						</td>
						<td>Lead expert</td>
						<td
							title="add collaborator" 
							class="cell-moduser-inactive"
							id="cell-1-2b"
							onclick="moduleChangeModuleUserStatus(this,1,2,'add')">
						</td>
										</tr>
														<tr>
						<td class="modusers-block-buffercell"></td>
											<td
							id="cell-1-14a"
							class="">
							Lead Expert
						</td>
						<td>Lead expert</td>
						<td
							title="add collaborator" 
							class="cell-moduser-inactive"
							id="cell-1-14b"
							onclick="moduleChangeModuleUserStatus(this,1,14,'add')">
						</td>
										</tr>
														<tr>
						<td class="modusers-block-buffercell"></td>
											<td
							id="cell-1-11a"
							class="">
							Gideon Gijswijt
						</td>
						<td>Editor</td>
						<td
							title="add collaborator" 
							class="cell-moduser-inactive"
							id="cell-1-11b"
							onclick="moduleChangeModuleUserStatus(this,1,11,'add')">
						</td>
										</tr>
														<tr>
						<td class="modusers-block-buffercell"></td>
											<td 
							id="cell-1-1a"
							class="cell-module-title-in-use">
							Maarten Schermer
						</td>
						<td>Expert</td>
						<td 
							title="remove collaborator" 
							class="cell-moduser-remove"
							id="cell-1-1b"
							onclick="moduleChangeModuleUserStatus(this,1,1,'remove')">
						</td>
										</tr>
							
				</table>
			</td>
		</tr>
			<tr>
					<td title="in use in your project" class="cell-module-in-use">&nbsp;</td>
			<td>
				<span class="cell-module-title-in-use" id="cell-3d">
							<span class="cell-module-title">Literature</span>
				</span>
			</td>
			<td>
				<span onclick="moduleToggleModuleUserBlock(3);" class="modusers-block-toggle">
					<span id="cell-3n">1</span> 
					collaborators
				</span>
			</td>
		</tr>
		<tr id="users-3" class="modusers-block-hidden">
			<td colspan="3">
				<table>
														<tr>
						<td class="modusers-block-buffercell"></td>
											<td
							id="cell-3-15a"
							class="">
							System Administrator
						</td>
						<td>System administrator</td>
						<td
							title="add collaborator" 
							class="cell-moduser-inactive"
							id="cell-3-15b"
							onclick="moduleChangeModuleUserStatus(this,3,15,'add')">
						</td>
										</tr>
														<tr>
						<td class="modusers-block-buffercell"></td>
											<td 
							id="cell-3-2a"
							class="cell-module-title-in-use">
							Jorge Luis Borges
						</td>
						<td>Lead expert</td>
						<td 
							title="remove collaborator" 
							class="cell-moduser-remove"
							id="cell-3-2b"
							onclick="moduleChangeModuleUserStatus(this,3,2,'remove')">
						</td>
										</tr>
														<tr>
						<td class="modusers-block-buffercell"></td>
											<td
							id="cell-3-14a"
							class="">
							Lead Expert
						</td>
						<td>Lead expert</td>
						<td
							title="add collaborator" 
							class="cell-moduser-inactive"
							id="cell-3-14b"
							onclick="moduleChangeModuleUserStatus(this,3,14,'add')">
						</td>
										</tr>
														<tr>
						<td class="modusers-block-buffercell"></td>
											<td
							id="cell-3-11a"
							class="">
							Gideon Gijswijt
						</td>
						<td>Editor</td>
						<td
							title="add collaborator" 
							class="cell-moduser-inactive"
							id="cell-3-11b"
							onclick="moduleChangeModuleUserStatus(this,3,11,'add')">
						</td>
										</tr>
														<tr>
						<td class="modusers-block-buffercell"></td>
											<td
							id="cell-3-1a"
							class="">
							Maarten Schermer
						</td>
						<td>Expert</td>
						<td
							title="add collaborator" 
							class="cell-moduser-inactive"
							id="cell-3-1b"
							onclick="moduleChangeModuleUserStatus(this,3,1,'add')">
						</td>
										</tr>
							
				</table>
			</td>
		</tr>
			<tr>
					<td title="in use in your project" class="cell-module-in-use">&nbsp;</td>
			<td>
				<span class="cell-module-title-in-use" id="cell-7d">
							<span class="cell-module-title">Picture key</span>
				</span>
			</td>
			<td>
				<span onclick="moduleToggleModuleUserBlock(7);" class="modusers-block-toggle">
					<span id="cell-7n">2</span> 
					collaborators
				</span>
			</td>
		</tr>
		<tr id="users-7" class="modusers-block-hidden">
			<td colspan="3">
				<table>
														<tr>
						<td class="modusers-block-buffercell"></td>
											<td
							id="cell-7-15a"
							class="">
							System Administrator
						</td>
						<td>System administrator</td>
						<td
							title="add collaborator" 
							class="cell-moduser-inactive"
							id="cell-7-15b"
							onclick="moduleChangeModuleUserStatus(this,7,15,'add')">
						</td>
										</tr>
														<tr>
						<td class="modusers-block-buffercell"></td>
											<td
							id="cell-7-2a"
							class="">
							Jorge Luis Borges
						</td>
						<td>Lead expert</td>
						<td
							title="add collaborator" 
							class="cell-moduser-inactive"
							id="cell-7-2b"
							onclick="moduleChangeModuleUserStatus(this,7,2,'add')">
						</td>
										</tr>
														<tr>
						<td class="modusers-block-buffercell"></td>
											<td
							id="cell-7-14a"
							class="">
							Lead Expert
						</td>
						<td>Lead expert</td>
						<td
							title="add collaborator" 
							class="cell-moduser-inactive"
							id="cell-7-14b"
							onclick="moduleChangeModuleUserStatus(this,7,14,'add')">
						</td>
										</tr>
														<tr>
						<td class="modusers-block-buffercell"></td>
											<td 
							id="cell-7-11a"
							class="cell-module-title-in-use">
							Gideon Gijswijt
						</td>
						<td>Editor</td>
						<td 
							title="remove collaborator" 
							class="cell-moduser-remove"
							id="cell-7-11b"
							onclick="moduleChangeModuleUserStatus(this,7,11,'remove')">
						</td>
										</tr>
														<tr>
						<td class="modusers-block-buffercell"></td>
											<td
							id="cell-7-1a"
							class="">
							Maarten Schermer
						</td>
						<td>Expert</td>
						<td
							title="add collaborator" 
							class="cell-moduser-inactive"
							id="cell-7-1b"
							onclick="moduleChangeModuleUserStatus(this,7,1,'add')">
						</td>
										</tr>
							
				</table>
			</td>
		</tr>
			<tr>
					<td title="in use in your project" class="cell-module-in-use">&nbsp;</td>
			<td>
				<span class="cell-module-title-in-use" id="cell-9d">
							<span class="cell-module-title">Map key</span>
				</span>
			</td>
			<td>
				<span onclick="moduleToggleModuleUserBlock(9);" class="modusers-block-toggle">
					<span id="cell-9n">0</span> 
					collaborators
				</span>
			</td>
		</tr>
		<tr id="users-9" class="modusers-block-hidden">
			<td colspan="3">
				<table>
														<tr>
						<td class="modusers-block-buffercell"></td>
											<td
							id="cell-9-15a"
							class="">
							System Administrator
						</td>
						<td>System administrator</td>
						<td
							title="add collaborator" 
							class="cell-moduser-inactive"
							id="cell-9-15b"
							onclick="moduleChangeModuleUserStatus(this,9,15,'add')">
						</td>
										</tr>
														<tr>
						<td class="modusers-block-buffercell"></td>
											<td
							id="cell-9-2a"
							class="">
							Jorge Luis Borges
						</td>
						<td>Lead expert</td>
						<td
							title="add collaborator" 
							class="cell-moduser-inactive"
							id="cell-9-2b"
							onclick="moduleChangeModuleUserStatus(this,9,2,'add')">
						</td>
										</tr>
														<tr>
						<td class="modusers-block-buffercell"></td>
											<td
							id="cell-9-14a"
							class="">
							Lead Expert
						</td>
						<td>Lead expert</td>
						<td
							title="add collaborator" 
							class="cell-moduser-inactive"
							id="cell-9-14b"
							onclick="moduleChangeModuleUserStatus(this,9,14,'add')">
						</td>
										</tr>
														<tr>
						<td class="modusers-block-buffercell"></td>
											<td
							id="cell-9-11a"
							class="">
							Gideon Gijswijt
						</td>
						<td>Editor</td>
						<td
							title="add collaborator" 
							class="cell-moduser-inactive"
							id="cell-9-11b"
							onclick="moduleChangeModuleUserStatus(this,9,11,'add')">
						</td>
										</tr>
														<tr>
						<td class="modusers-block-buffercell"></td>
											<td
							id="cell-9-1a"
							class="">
							Maarten Schermer
						</td>
						<td>Expert</td>
						<td
							title="add collaborator" 
							class="cell-moduser-inactive"
							id="cell-9-1b"
							onclick="moduleChangeModuleUserStatus(this,9,1,'add')">
						</td>
										</tr>
							
				</table>
			</td>
		</tr>
		</table>
	</div>

	<br />

	<div class="text-block">

	Assign collaborators to work on free modules:<br />

	<table>
				<tr id="row-f17">
					<td
				title="in use in your project" 
				class="cell-module-in-use" 
				id="cell-f17a">&nbsp;
				
			</td>
			<td>
				<span class="cell-module-title-in-use" id="cell-f17d">
							<span class="cell-module-title">Animal sounds</span>
				</span>
			</td>
			<td>
				<span 
					onclick="moduleToggleModuleUserBlock('f'+17);" 
					 class="modusers-block-toggle">
						<span id="cell-f17n">
							3
						</span> 
					collaborators
				</span>
			</td>
		</tr>
		<tr id="users-f17" class="modusers-block-hidden">
			<td colspan="3">
				<table>
														<tr>
						<td class="modusers-block-buffercell"></td>
											<td 
							id="cell-f17-15a"
							class="cell-module-title-in-use">
							System Administrator
							</td>
						<td>System administrator</td>
						<td 
							title="remove collaborator" 
							class="cell-moduser-remove"
							id="cell-f17-15b"
							onclick="moduleChangeModuleUserStatus(this,17,15,'remove')">
						</td>
										</tr>
														<tr>
						<td class="modusers-block-buffercell"></td>
											<td 
							id="cell-f17-2a"
							class="cell-module-title-in-use">
							Jorge Luis Borges
							</td>
						<td>Lead expert</td>
						<td 
							title="remove collaborator" 
							class="cell-moduser-remove"
							id="cell-f17-2b"
							onclick="moduleChangeModuleUserStatus(this,17,2,'remove')">
						</td>
										</tr>
														<tr>
						<td class="modusers-block-buffercell"></td>
											<td
							id="cell-f1-14a"
							class="">
							Lead Expert
						</td>
						<td>Lead expert</td>
						<td
							title="add collaborator" 
							class="cell-moduser-inactive"
							id="cell-f17-14b"
							onclick="moduleChangeModuleUserStatus(this,17,14,'add')">
						</td>
										</tr>
														<tr>
						<td class="modusers-block-buffercell"></td>
											<td
							id="cell-f1-11a"
							class="">
							Gideon Gijswijt
						</td>
						<td>Editor</td>
						<td
							title="add collaborator" 
							class="cell-moduser-inactive"
							id="cell-f17-11b"
							onclick="moduleChangeModuleUserStatus(this,17,11,'add')">
						</td>
										</tr>
														<tr>
						<td class="modusers-block-buffercell"></td>
											<td 
							id="cell-f17-1a"
							class="cell-module-title-in-use">
							Maarten Schermer
							</td>
						<td>Expert</td>
						<td 
							title="remove collaborator" 
							class="cell-moduser-remove"
							id="cell-f17-1b"
							onclick="moduleChangeModuleUserStatus(this,17,1,'remove')">
						</td>
										</tr>
							
				</table>
			</td>
		</tr>
		</table>
	
	</div>

</div>

</div ends="page-container">

<div id="footer-container">
	<div id="footer-menu">
		<a href="/admin/views/users/logout.php">Log out (logged in as Jorge Luis Borges)</a>
		<br />
	</div>
</div ends="footer-container">

</div ends="body-container"></body>
</html>
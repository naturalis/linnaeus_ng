205
a:4:{s:8:"template";a:3:{s:8:"list.tpl";b:1;s:26:"../shared/admin-header.tpl";b:1;s:26:"../shared/admin-footer.tpl";b:1;}s:9:"timestamp";i:1284395712;s:7:"expires";i:1284399312;s:13:"cache_serials";a:0:{}}<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

	<title>Imaginary Beings - Taxon list</title>

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
					<span class="crumb"><a href="/admin/views/species">Species module</a></span>
		<span class="crumb-arrow">&rarr;</span>
					<span id="crumb-current">Taxon list</span>
		<span class="crumb-arrow">&nbsp;</span>
			</div>
</div>




<div id="page-main">

<table>
<tr>
	<th onclick="allTableColumnSort('taxon');">Taxon</th>
		<td style="text-align:right; width:75px">
		(Dutch)	</td>
		<td style="text-align:right; width:75px">
		English *	</td>
		<td style="text-align:right; width:75px">
		Japanese	</td>
		

</tr>
<tr>
	<td>
		<a href="edit.php?id=23">Hydra Of Lerna</a>
	</td>
			<td style="text-align:right">
		<a href="edit.php?id=23&lan=24">2773</a>
	</td>
		<td style="text-align:right">
		<a href="edit.php?id=23&lan=26">6959</a>
	</td>
		<td style="text-align:right">
		<a href="edit.php?id=23&lan=50">3799</a>
	</td>
		
</tr>
</table>
<br />
<a href="edit.php">Add a new taxon</a>
</div>

<form method="post" action="" name="postForm" id="postForm">
<input type="hidden" name="key" id="key" value="taxon" />
<input type="hidden" name="dir" value="asc"  />
</form>

</div ends="page-container">

<div id="footer-container">
	<div id="footer-menu">
		<a href="/admin/views/users/logout.php">Log out (logged in as Jorge Luis Borges)</a>
		<br />
	</div>
</div ends="footer-container">

</div ends="body-container"></body>
</html>
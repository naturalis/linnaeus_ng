371
a:4:{s:8:"template";a:8:{s:10:"browse.tpl";b:1;s:19:"../shared/_head.tpl";b:1;s:25:"../shared/_body-start.tpl";b:1;s:24:"../shared/_main-menu.tpl";b:1;s:25:"../shared/_page-start.tpl";b:1;s:20:"../shared/header.tpl";b:1;s:22:"../shared/messages.tpl";b:1;s:20:"../shared/footer.tpl";b:1;}s:9:"timestamp";i:1298545086;s:7:"expires";i:1298548686;s:13:"cache_serials";a:0:{}}<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title>Imaginary Beings Of The Literary World</title>
	<style type="text/css" media="all">
		@import url("../../../app/style/imaginarybeings-basics.css");
		@import url("../../../app/style/imaginarybeings-glossary.css");
		@import url("../../../app/style/colorbox/colorbox.css");
	</style>
	<script type="text/javascript" src="../../../app/javascript/jquery-1.4.2.min.js"></script>
	<script type="text/javascript" src="../../../app/javascript/jquery.tools.min.js"></script>
	<script type="text/javascript" src="../../../app/javascript/main.js"></script>
	<script type="text/javascript" src="../../../app/javascript/colorbox/jquery.colorbox.js"></script>
</head>
<body><body id="body"><div id="body-container">
<div id="header-container">
	<div id="image">
	<a href="../../../app/views/linnaeus/"><img src="/admin/media/project/0002/imaginary-beings-logo%20%282%29.png" id="project-logo" /></a>
	</div>
	<div id="title">
	Imaginary Beings Of The Literary World
	</div>
</div><div id="menu-container">
	<div id="main-menu">
<span class="menu-item-active">Glossary</span>
<a class="menu-item" href="../literature/">Literature</a>
<a class="menu-item" href="../species/">Species module</a>
<a class="menu-item" href="../highertaxa/">Higher taxa</a>
<a class="menu-item" href="../key/">Dichotomous key</a>
<a class="menu-item" href="../matrixkey/">Matrix key</a>
<span class="menu-item" onclick="menuGoModule(17);">Animal sounds</span>
<span class="menu-item" onclick="menuGoModule(34);">Funny pictures</span>
	<form method="post" action="../module/" id="moduleForm"><input type="hidden" name="modId" id="modId" value="" /></form>
	</div>
	<div id="language-change">
	<form action="" method="post" id="languageForm">
	<select name="uiLanguage" onChange="$('#languageForm').submit();">
		<option value="26" selected="selected">English *</option>
		<option value="24">Dutch </option>
		<option value="50">Japanese </option>
		<option value="36">German </option>
		<option value="115">Urdu </option>
		<option value="119">Welsh </option>
	</select>
	</form>
	</div>
</div><div id="page-container">

<div id="page-main">
<form name="theForm" id="theForm" method="post" action="">
Click to browse:&nbsp;
<span class="alphabet-active-letter">b</span>
<span class="alphabet-letter" onclick="$('#letter').val('c');$('#theForm').submit();">c</span>
<span class="alphabet-letter" onclick="$('#letter').val('f');$('#theForm').submit();">f</span>
<span class="alphabet-letter" onclick="$('#letter').val('m');$('#theForm').submit();">m</span>
<span class="alphabet-letter" onclick="$('#letter').val('p');$('#theForm').submit();">p</span>
<span class="alphabet-letter" onclick="$('#letter').val('q');$('#theForm').submit();">q</span>
<span class="alphabet-letter" onclick="$('#letter').val('s');$('#theForm').submit();">s</span>
<span class="alphabet-letter" onclick="$('#letter').val('y');$('#theForm').submit();">y</span>
<input type="hidden" name="letter" id="letter" value="b"  />
</form>



<table>
	<tr class="tr-highlight">
		<th style="width:200px" onclick="allTableColumnSort('author_both');">authors</th>
		<th style="width:500px">definition</th>
		<th></th>
	</tr>
	<tr class="tr-highlight">
		<td>Bamboo</td>
		<td>Bamboo About this sound listen (help·info) is a group of perennial evergre...</td>
		<td>[<a href="edit.php?id=17">edit</a>]</td>
	</tr>
</table>
<form method="post" action="" name="sortForm" id="sortForm">
<input type="hidden" name="key" id="key" value="" />
<input type="hidden" name="letter" value="b"  />
<input type="hidden" name="dir" value=""  />
</form>
</div>

</div ends="page-container">
<div id="footer-container">
</div ends="footer-container">
</div ends="body-container">
</body>
</html>

395
a:4:{s:8:"template";a:9:{s:11:"compare.tpl";b:1;s:19:"../shared/_head.tpl";b:1;s:25:"../shared/_body-start.tpl";b:1;s:24:"../shared/_main-menu.tpl";b:1;s:25:"../shared/_page-start.tpl";b:1;s:20:"../shared/header.tpl";b:1;s:11:"_header.tpl";b:1;s:22:"../shared/messages.tpl";b:1;s:20:"../shared/footer.tpl";b:1;}s:9:"timestamp";i:1299572748;s:7:"expires";i:1299576348;s:13:"cache_serials";a:0:{}}<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title>Imaginary Beings Of The Literary World: Matrix "Field set": compare</title>
	<style type="text/css" media="all">
		@import url("../../../app/style/imaginarybeings-basics.css");
		@import url("../../../app/style/imaginarybeings-matrix.css");
		@import url("../../../app/style/colorbox/colorbox.css");
	</style>
	<script type="text/javascript" src="../../../app/javascript/jquery-1.4.2.min.js"></script>
	<script type="text/javascript" src="../../../app/javascript/jquery.tools.min.js"></script>
	<script type="text/javascript" src="../../../app/javascript/main.js"></script>
	<script type="text/javascript" src="../../../app/javascript/matrix.js"></script>
	<script type="text/javascript" src="../../../app/javascript/colorbox/jquery.colorbox.js"></script>
</head><body id="body"><form method="post" action="" id="theForm" onsubmit="return checkForm();"><div id="body-container">
<div id="header-container">
	<div id="image">
	<a href="../../../app/views/linnaeus/"><img src="/admin/media/project/0002/imaginary-beings-logo%20%282%29.png" id="project-logo" /></a>
	</div>
	<div id="title">
	Imaginary Beings Of The Literary World
	</div>
</div><div id="menu-container">
	<div id="main-menu">
<a class="menu-item" href="../glossary/">Glossary</a>
<a class="menu-item" href="../literature/">Literature</a>
<a class="menu-item" href="../species/">Species module</a>
<a class="menu-item" href="../highertaxa/">Higher taxa</a>
<a class="menu-item" href="../key/">Dichotomous key</a>
<a class="menu-item-active" href="../matrixkey/">Matrix key</a>
<span class="menu-item" onclick="goMenuModule(17);">Animal sounds</span>
<span class="menu-item" onclick="goMenuModule(34);">Funny pictures</span>
	</div>
	<div id="language-change">
		<input
			type="text"
			name="search"
			id="search"
			class="search-input-shaded"
			value="enter search term"
			onkeydown="setSearchKeyed(true);"
			onblur="setSearchKeyed(false);"
			onfocus="onSearchBoxSelect()" />
			<img src="../../media/system/search.gif" onclick="doSearch();" />
		<select id="languageSelect" onchange="doLanguageChange()">
				<option value="26" selected="selected">English *</option>
				<option value="24">Dutch </option>
				<option value="50">Japanese </option>
				<option value="36">German </option>
				<option value="115">Urdu </option>
				<option value="119">Welsh </option>
			</select>
	</div>
</div><div id="page-container">
<div id="matrix-header">
	<div id="current">Using matrix "Field set".</div>
	<div id="switch">(Click <a href="matrices.php">here</a> to change to another matrix)</div></div>

<div id="page-main">
	<div id="compare">
		<p>
			<select id="taxon-list-1">
			<option disabled="disabled" selected="selected" value="">select a taxon</option>
						<option value="1">Animalia</option>
						<option value="4">Carnivora</option>
						<option value="9">Ursus thibetanus laniger</option>
						<option value="13">Ursus thibetanus mupinensis</option>
						<option value="170">Oetmammoet</option>
						</select>
		</p>
		<p>
			<select id="taxon-list-2">
			<option disabled="disabled" selected="selected" value="">select a taxon</option>
						<option value="1">Animalia</option>
						<option value="4">Carnivora</option>
						<option value="9">Ursus thibetanus laniger</option>
						<option value="13">Ursus thibetanus mupinensis</option>
						<option value="170">Oetmammoet</option>
						</select>
		</p>
		<p>
		<input type="button" onclick="goCompare()" value="compare taxa" />
		</p>
		<p>
			<table id="states" class="invisible">
				<tr><td style="width:300px">Unique states in <span id="taxon-1"></span>:</td><td id="count-1"></td><td></td></tr>
				<tr><td>Unique states in <span id="taxon-2"></span>:</td><td id="count-2"></td><td></td></tr>
				<tr><td>States present in both:</td><td id="count-both"></td><td></td></tr>
				<tr><td>States present in neither:</td><td id="count-neither"></td><td></td></tr>
				<tr><td>Number of available states:</td><td id="count-total"></td><td></td></tr>
				<tr><td>Taxonomic distance:</td><td id="coefficient"></td><td id="formula" style="padding-left:5px"></td></tr>
			</table>
		</p>
	</div>
</div>


<script type="text/JavaScript">
$(document).ready(function(){


});
</script>



</div ends="page-container">
<div id="footer-container">
</div ends="footer-container">
</div ends="body-container">
</form>

<script type="text/JavaScript">
$(document).ready(function(){

	$('#body-container').height($(document).height());
	
addRequestVar('0','')

});
</script>

</body>
</html>


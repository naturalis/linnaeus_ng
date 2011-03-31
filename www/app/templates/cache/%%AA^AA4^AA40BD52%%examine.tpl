338
a:4:{s:8:"template";a:7:{s:11:"examine.tpl";b:1;s:19:"../shared/_head.tpl";b:1;s:25:"../shared/_body-start.tpl";b:1;s:24:"../shared/_main-menu.tpl";b:1;s:25:"../shared/_page-start.tpl";b:1;s:20:"../shared/header.tpl";b:1;s:20:"../shared/footer.tpl";b:1;}s:9:"timestamp";i:1301573622;s:7:"expires";i:1301577222;s:13:"cache_serials";a:0:{}}<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title>Imaginary Beings Of The Literary World: Choose a species</title>
	<style type="text/css" media="all">
		@import url("../../../app/style/0002/basics.css");
		@import url("../../../app/style/0002/map.css");
	</style>
	<script type="text/javascript" src="../../../app/javascript/jquery-1.4.2.min.js"></script>
	<script type="text/javascript" src="../../../app/javascript/jquery.tools.min.js"></script>
	<script type="text/javascript" src="../../../admin/javascript/sprintf-0.7-beta1.js"></script>
	
	<script type="text/javascript" src="../../../app/javascript/main.js"></script>
	<script type="text/javascript" src="../../../app/javascript/mapkey.js"></script>
	<script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=false"></script>
</head><body id="body"><form method="post" action="" id="theForm" onsubmit="return checkForm();"><div id="body-container">
<div id="header-container">
	<div id="image">
	<a href="../../../app/views/linnaeus/"><img src="../../../admin/media/project/0002/imaginary-beings-logo%20%282%29.png" id="project-logo" /></a>
	</div>
	<div id="title">
	Imaginary Beings Of The Literary World	</div>
</div><div id="menu-container">
	<div id="main-menu">
<a class="menu-item" href="../glossary/">Glossary</a>
<a class="menu-item" href="../literature/">Literature</a>
<a class="menu-item" href="../species/">Species module</a>
<a class="menu-item" href="../highertaxa/">Higher taxa</a>
<a class="menu-item" href="../key/">Dichotomous key</a>
<a class="menu-item" href="../matrixkey/">Matrix key</a>
<a class="menu-item-active" href="../mapkey/">Map key</a>
<span class="menu-item" onclick="goMenuModule(17);">Animal sounds</span>
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

<div id="page-main">
	<div id="index">
		<p>
		Click a species to examine:		</p>
		<table>
		<tr>
			<th>Taxon</th>
			<th>Number of geo entries</td>
		</tr>
				<tr class="highlight">
			<td class="a" onclick="goMap(57)" style="width:250px;">
				Melursus ursinus
							</td>
			<td style="text-align:right">
				9			</td>
		</tr>
				<tr class="highlight">
			<td class="a" onclick="goMap(58)" style="width:250px;">
				Melursus ursinus inornatus
							</td>
			<td style="text-align:right">
				5			</td>
		</tr>
				<tr class="highlight">
			<td class="a" onclick="goMap(59)" style="width:250px;">
				Melursus ursinus ursinus
							</td>
			<td style="text-align:right">
				0			</td>
		</tr>
				<tr class="highlight">
			<td class="a" onclick="goMap(55)" style="width:250px;">
				Tremarctos ornatus
				<span class="hybrid-marker" title="hybrid">X</span>			</td>
			<td style="text-align:right">
				5			</td>
		</tr>
				<tr class="highlight">
			<td class="a" onclick="goMap(7)" style="width:250px;">
				Ursus luteolus
				<span class="hybrid-marker" title="hybrid">X</span>			</td>
			<td style="text-align:right">
				9			</td>
		</tr>
				<tr class="highlight">
			<td class="a" onclick="goMap(8)" style="width:250px;">
				Ursus thibetanus
				<span class="hybrid-marker" title="hybrid">X</span>			</td>
			<td style="text-align:right">
				0			</td>
		</tr>
				<tr class="highlight">
			<td class="a" onclick="goMap(9)" style="width:250px;">
				Ursus thibetanus laniger
				<span class="hybrid-marker" title="hybrid">X</span>			</td>
			<td style="text-align:right">
				2			</td>
		</tr>
				<tr class="highlight">
			<td class="a" onclick="goMap(10)" style="width:250px;">
				Ursus thibetanus thibetanus
				<span class="hybrid-marker" title="hybrid">X</span>			</td>
			<td style="text-align:right">
				1			</td>
		</tr>
				<tr class="highlight">
			<td class="a" onclick="goMap(11)" style="width:250px;">
				Ursus thibetanus gedrosianus
				<span class="hybrid-marker" title="hybrid">X</span>			</td>
			<td style="text-align:right">
				1			</td>
		</tr>
				<tr class="highlight">
			<td class="a" onclick="goMap(12)" style="width:250px;">
				Ursus thibetanus ussuricus
				<span class="hybrid-marker" title="hybrid">X</span>			</td>
			<td style="text-align:right">
				0			</td>
		</tr>
				<tr class="highlight">
			<td class="a" onclick="goMap(13)" style="width:250px;">
				Ursus thibetanus mupinensis
				<span class="hybrid-marker" title="hybrid">X</span>			</td>
			<td style="text-align:right">
				0			</td>
		</tr>
				<tr class="highlight">
			<td class="a" onclick="goMap(14)" style="width:250px;">
				Ursus thibetanus formosanus
				<span class="hybrid-marker" title="hybrid">X</span>			</td>
			<td style="text-align:right">
				0			</td>
		</tr>
				<tr class="highlight">
			<td class="a" onclick="goMap(15)" style="width:250px;">
				Ursus thibetanus japonicus
				<span class="hybrid-marker" title="hybrid">X</span>			</td>
			<td style="text-align:right">
				0			</td>
		</tr>
				<tr class="highlight">
			<td class="a" onclick="goMap(16)" style="width:250px;">
				Ursus maritimus
							</td>
			<td style="text-align:right">
				39			</td>
		</tr>
				<tr class="highlight">
			<td class="a" onclick="goMap(17)" style="width:250px;">
				Ursus maritimus marinus
				<span class="hybrid-marker" title="hybrid">X</span>			</td>
			<td style="text-align:right">
				0			</td>
		</tr>
				<tr class="highlight">
			<td class="a" onclick="goMap(18)" style="width:250px;">
				Ursus maritimus maritimus
				<span class="hybrid-marker" title="hybrid">X</span>			</td>
			<td style="text-align:right">
				0			</td>
		</tr>
				<tr class="highlight">
			<td class="a" onclick="goMap(19)" style="width:250px;">
				Ursus arctos
							</td>
			<td style="text-align:right">
				1			</td>
		</tr>
				<tr class="highlight">
			<td class="a" onclick="goMap(20)" style="width:250px;">
				Ursus arctos nelsoni
				<span class="hybrid-marker" title="hybrid">X</span>			</td>
			<td style="text-align:right">
				0			</td>
		</tr>
				<tr class="highlight">
			<td class="a" onclick="goMap(21)" style="width:250px;">
				Ursus arctos beringianus
				<span class="hybrid-marker" title="hybrid">X</span>			</td>
			<td style="text-align:right">
				0			</td>
		</tr>
				<tr class="highlight">
			<td class="a" onclick="goMap(22)" style="width:250px;">
				Ursus arctos pruinosus
				<span class="hybrid-marker" title="hybrid">X</span>			</td>
			<td style="text-align:right">
				6			</td>
		</tr>
				</table>
	</div>
	<div id="navigation">
						<span class="a" onclick="goNavigate(20);">next ></span>
			</div>




</div>

</div ends="page-container">
<div id="footer-container">
</div ends="footer-container">
</div ends="body-container">
</form>

<script type="text/JavaScript">
$(document).ready(function(){

	$('#body-container').height($(document).height());
	
addRequestVar('0','')

})

</script>

</body>
</html>
373
a:4:{s:8:"template";a:8:{s:12:"matrices.tpl";b:1;s:19:"../shared/_head.tpl";b:1;s:25:"../shared/_body-start.tpl";b:1;s:24:"../shared/_main-menu.tpl";b:1;s:25:"../shared/_page-start.tpl";b:1;s:20:"../shared/header.tpl";b:1;s:22:"../shared/messages.tpl";b:1;s:20:"../shared/footer.tpl";b:1;}s:9:"timestamp";i:1302616239;s:7:"expires";i:1302619839;s:13:"cache_serials";a:0:{}}<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title>Imaginary Beings Of The Literary World: Matrices</title>
	<style type="text/css" media="all">
		@import url("../../../app/style/0002/basics.css");
		@import url("../../../app/style/0002/matrix.css");
		@import url("../../../app/style/0002/colorbox/colorbox.css");
		@import url("../../../app/style/0002/dialog/jquery.modaldialog.css");
	</style>
	<script type="text/javascript" src="../../../app/javascript/jquery-1.4.2.min.js"></script>
	<script type="text/javascript" src="../../../app/javascript/jquery.tools.min.js"></script>
	<script type="text/javascript" src="../../../admin/javascript/sprintf-0.7-beta1.js"></script>
	
	<script type="text/javascript" src="../../../app/javascript/main.js"></script>
	<script type="text/javascript" src="../../../app/javascript/matrix.js"></script>
	<script type="text/javascript" src="../../../app/javascript/colorbox/jquery.colorbox.js"></script>
	<script type="text/javascript" src="../../../app/javascript/dialog/jquery.modaldialog.js"></script>
</head><body id="body"><form method="post" action="" id="theForm" onsubmit="return checkForm();"><div id="body-container"><div id="menu-container">
	<div id="main-menu">
<a class="menu-item" href="../glossary/">Glossary</a>
<span class="menu-separator">|</span><a class="menu-item" href="../literature/">Literature</a>
<span class="menu-separator">|</span><a class="menu-item" href="../species/">Species module</a>
<span class="menu-separator">|</span><a class="menu-item" href="../highertaxa/">Higher taxa</a>
<span class="menu-separator">|</span><a class="menu-item" href="../key/">Dichotomous key</a>
<span class="menu-separator">|</span><a class="menu-item-active" href="../matrixkey/">Matrix key</a>
<span class="menu-separator">|</span><a class="menu-item" href="../mapkey/">Map key</a>
<span class="menu-separator">|</span><span class="menu-item" onclick="goMenuModule(17);">Animal sounds</span>
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
<p>
Choose a matrix to use:</p>
	<table>
		<tr class="highlight">
		<td class="a" style="width:200px" onclick="goMatrix(7)">Use "Field set"</td>
	</tr>
		<tr class="highlight">
		<td class="a" style="width:200px" onclick="goMatrix(8)">Use "Laboratory set"</td>
	</tr>
		</table>
</div>

<div id="back-to-search">
<span id="back-link" onclick="window.open('../linnaeus/redosearch.php','_self')">back to search results</span>
</div>
</div ends="page-container">
<div id="footer-container">
</div ends="footer-container">
</div ends="body-container">
<div id="hint-balloon" onmouseout="glossTextOut()" 
	style="
	background-color:#FFFF99;
	border:1px solid #bbbb00;
	width:225px;height:100px;
	padding:3px;
	font-size:9px;
	display:none;
	overflow:hidden;
	cursor:pointer;
	position:absolute;
	top:0px;
	left:0px;
	">
</div>
</form>

<script type="text/JavaScript">
$(document).ready(function(){

	$('#body-container').height($(document).height());
	
addRequestVar('0','')

})

</script>

</body>
</html>
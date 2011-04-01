362
a:4:{s:8:"template";a:8:{s:12:"identify.tpl";b:1;s:19:"../shared/_head.tpl";b:1;s:25:"../shared/_body-start.tpl";b:1;s:24:"../shared/_main-menu.tpl";b:1;s:25:"../shared/_page-start.tpl";b:1;s:20:"../shared/header.tpl";b:1;s:11:"_header.tpl";b:1;s:20:"../shared/footer.tpl";b:1;}s:9:"timestamp";i:1301670143;s:7:"expires";i:1301673743;s:13:"cache_serials";a:0:{}}<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title>Imaginary Beings Of The Literary World: Matrix "Field set": identify</title>
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
<a class="menu-item-active" href="../matrixkey/">Matrix key</a>
<a class="menu-item" href="../mapkey/">Map key</a>
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
<div id="matrix-header">
	<div id="current">Using matrix "Field set".</div>
	<div id="switch">(Click <a href="matrices.php">here</a> to change to another matrix)</div></div>


<div id="page-main">
	<div id="pane-left">
		<div id="char-states">
			Characters<br />
			<select size="5" id="characteristics" onclick="goCharacteristic()" ondblclick="addSelected(this)" >
									<option value="6">a distribution</option>
												<option value="8">a text</option>
												<option value="13">a media</option>
												<option value="14">a range</option>
												<option value="29">Vnurk!</option>
												<option value="30">Bwieeewaaah</option>
												<option value="32">Bora Bora Bora</option>
												<option value="35">eeeeek</option>
												<option value="37">gewicht</option>
																					</select>
			<br />
			States<br />
			<select size="5" id="states" onclick="goState()" ondblclick="addSelected(this)">
			</select>
		</div>
		<div id="info">
		(info)
		</div>
		<div id="buttons">
			<input type="button" onclick="addSelected(this)" value="add" />
			<input type="button" onclick="deleteSelected()" value="delete" />
			<input type="button" onclick="clearSelected()" value="clear all" />
		</div>
		<div id="choices">
			Selected combination of characters<br />
			<select size="25" id="selected">
			</select>		
		</div>
	</div>
	<div id="pane-right">
		<div id="scores-taxa">
			Result of this combination of characters<br />
			<select size="5" id="scores">
						<option ondblclick="goTaxon(1)" value="1">Animalia</option>
						<option ondblclick="goTaxon(4)" value="4">Carnivora</option>
						<option ondblclick="goTaxon(9)" value="9">Ursus thibetanus laniger X</option>
						<option ondblclick="goTaxon(13)" value="13">Ursus thibetanus mupinensis X</option>
						<option ondblclick="goTaxon(170)" value="170">Oetmammoet</option>
						</select>
		</div>		
	</div>

</div>


<script type="text/JavaScript">
$(document).ready(function(){

	storeCharacteristic(6,'a distribution','distribution');
	storeCharacteristic(8,'a text','text');
	storeCharacteristic(13,'a media','media');
	storeCharacteristic(14,'a range','range');
	storeCharacteristic(29,'Vnurk!','media');
	storeCharacteristic(30,'Bwieeewaaah','range');
	storeCharacteristic(32,'Bora Bora Bora','text');
	storeCharacteristic(35,'eeeeek','text');
	storeCharacteristic(37,'gewicht','distribution');
	storeCharacteristic(38,'','text');
	storeCharacteristic(39,'','text');
	imagePath = '../../../admin/media/project/0002/';

});
</script>


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
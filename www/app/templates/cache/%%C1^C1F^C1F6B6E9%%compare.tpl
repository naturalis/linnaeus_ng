395
a:4:{s:8:"template";a:9:{s:11:"compare.tpl";b:1;s:19:"../shared/_head.tpl";b:1;s:25:"../shared/_body-start.tpl";b:1;s:24:"../shared/_main-menu.tpl";b:1;s:25:"../shared/_page-start.tpl";b:1;s:20:"../shared/header.tpl";b:1;s:11:"_header.tpl";b:1;s:22:"../shared/messages.tpl";b:1;s:20:"../shared/footer.tpl";b:1;}s:9:"timestamp";i:1302549840;s:7:"expires";i:1302553440;s:13:"cache_serials";a:0:{}}<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title>TanBIF species: Matrix "Tanzania": compare</title>
	<style type="text/css" media="all">
		@import url("../../../app/style/0058/basics.css");
		@import url("../../../app/style/0058/matrix.css");
		@import url("../../../app/style/0058/colorbox/colorbox.css");
		@import url("../../../app/style/0058/dialog/jquery.modaldialog.css");
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
	<div id="title">
	<a href="../../../app/views/linnaeus/">TanBIF species</a>	</div>
</div><div id="menu-container">
	<div id="main-menu">
<a class="menu-item" href="../literature/">Literature</a>
<a class="menu-item" href="../species/">Species module</a>
<a class="menu-item" href="../highertaxa/">Higher taxa</a>
<a class="menu-item" href="../key/">Dichotomous key</a>
<a class="menu-item-active" href="../matrixkey/">Matrix key</a>
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
			</select>
	</div>
</div><div id="page-container">
<div id="matrix-header">
	<div id="current">Using matrix "Tanzania".</div>
	</div>

<div id="page-main">
	<div id="compare">
		<p>
			<select id="taxon-list-1">
			<option disabled="disabled" selected="selected" value="">select a taxon</option>
						<option value="18030">Acacia xanthophloea</option>
						<option value="18037">Acinonyx jubatus</option>
						<option value="18046">Adansonia digitata</option>
						<option value="18148">Connochaetes taurinus</option>
						<option value="18157">Crocodylus niloticus</option>
						<option value="18160">Crocuta crocuta</option>
						<option value="18173">Diceros bicornis</option>
						<option value="18189">Equus burchellii</option>
						<option value="18197">Gazella thomsonii</option>
						<option value="18203">Giraffa camelopardalis</option>
						<option value="18208">Gyps rueppellii</option>
						<option value="18228">Hippopotamus amphibius</option>
						<option value="18270">Lates niloticus</option>
						<option value="18280">Loxodonta africana</option>
						<option value="18290">Mecistops cataphractus</option>
						<option value="18343">Panthera pardus</option>
						<option value="18352">Papio anubis</option>
						<option value="18362">Phacochoerus africanus</option>
						<option value="18423">Struthio camelus</option>
						<option value="18427">Syncerus caffer</option>
						</select>
		</p>
		<p>
			<select id="taxon-list-2">
			<option disabled="disabled" selected="selected" value="">select a taxon</option>
						<option value="18030">Acacia xanthophloea</option>
						<option value="18037">Acinonyx jubatus</option>
						<option value="18046">Adansonia digitata</option>
						<option value="18148">Connochaetes taurinus</option>
						<option value="18157">Crocodylus niloticus</option>
						<option value="18160">Crocuta crocuta</option>
						<option value="18173">Diceros bicornis</option>
						<option value="18189">Equus burchellii</option>
						<option value="18197">Gazella thomsonii</option>
						<option value="18203">Giraffa camelopardalis</option>
						<option value="18208">Gyps rueppellii</option>
						<option value="18228">Hippopotamus amphibius</option>
						<option value="18270">Lates niloticus</option>
						<option value="18280">Loxodonta africana</option>
						<option value="18290">Mecistops cataphractus</option>
						<option value="18343">Panthera pardus</option>
						<option value="18352">Papio anubis</option>
						<option value="18362">Phacochoerus africanus</option>
						<option value="18423">Struthio camelus</option>
						<option value="18427">Syncerus caffer</option>
						</select>
		</p>
		<p>
		<input type="button" onclick="goCompare()" value="compare taxa" />
		</p>
		<p>
			<table id="states" class="invisible">
				<tr class="highlight"><td style="width:300px">Unique states in <span id="taxon-1"></span>:</td><td id="count-1"></td><td></td></tr>
				<tr class="highlight"><td>Unique states in <span id="taxon-2"></span>:</td><td id="count-2"></td><td></td></tr>
				<tr class="highlight"><td>States present in both:</td><td id="count-both"></td><td></td></tr>
				<tr class="highlight"><td>States present in neither:</td><td id="count-neither"></td><td></td></tr>
				<tr class="highlight"><td>Number of available states:</td><td id="count-total"></td><td></td></tr>
				<tr class="highlight"><td>Taxonomic distance:</td><td id="coefficient"></td><td id="formula" style="padding-left:5px"></td></tr>
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
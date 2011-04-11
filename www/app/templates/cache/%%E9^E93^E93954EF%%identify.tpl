362
a:4:{s:8:"template";a:8:{s:12:"identify.tpl";b:1;s:19:"../shared/_head.tpl";b:1;s:25:"../shared/_body-start.tpl";b:1;s:24:"../shared/_main-menu.tpl";b:1;s:25:"../shared/_page-start.tpl";b:1;s:20:"../shared/header.tpl";b:1;s:11:"_header.tpl";b:1;s:20:"../shared/footer.tpl";b:1;}s:9:"timestamp";i:1302549755;s:7:"expires";i:1302553355;s:13:"cache_serials";a:0:{}}<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title>TanBIF species: Matrix "Tanzania": identify</title>
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
	<div id="pane-left">
		<div id="char-states">
			Characters<br />
			<select size="5" id="characteristics" onclick="goCharacteristic()" ondblclick="addSelected(this)" >
									<option value="252">Habitat</option>
												<option value="253">Main Colour</option>
												<option value="254">Pattern</option>
												<option value="255">Size</option>
												<option value="256">Length of Legs : Body Height</option>
												<option value="257">Antlers/Horns</option>
												<option value="258">Scales</option>
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
						<option ondblclick="goTaxon(18030)" value="18030">Acacia xanthophloea</option>
						<option ondblclick="goTaxon(18037)" value="18037">Acinonyx jubatus</option>
						<option ondblclick="goTaxon(18046)" value="18046">Adansonia digitata</option>
						<option ondblclick="goTaxon(18148)" value="18148">Connochaetes taurinus</option>
						<option ondblclick="goTaxon(18157)" value="18157">Crocodylus niloticus</option>
						<option ondblclick="goTaxon(18160)" value="18160">Crocuta crocuta</option>
						<option ondblclick="goTaxon(18173)" value="18173">Diceros bicornis</option>
						<option ondblclick="goTaxon(18189)" value="18189">Equus burchellii</option>
						<option ondblclick="goTaxon(18197)" value="18197">Gazella thomsonii</option>
						<option ondblclick="goTaxon(18203)" value="18203">Giraffa camelopardalis</option>
						<option ondblclick="goTaxon(18208)" value="18208">Gyps rueppellii</option>
						<option ondblclick="goTaxon(18228)" value="18228">Hippopotamus amphibius</option>
						<option ondblclick="goTaxon(18270)" value="18270">Lates niloticus</option>
						<option ondblclick="goTaxon(18280)" value="18280">Loxodonta africana</option>
						<option ondblclick="goTaxon(18290)" value="18290">Mecistops cataphractus</option>
						<option ondblclick="goTaxon(18343)" value="18343">Panthera pardus</option>
						<option ondblclick="goTaxon(18352)" value="18352">Papio anubis</option>
						<option ondblclick="goTaxon(18362)" value="18362">Phacochoerus africanus</option>
						<option ondblclick="goTaxon(18423)" value="18423">Struthio camelus</option>
						<option ondblclick="goTaxon(18427)" value="18427">Syncerus caffer</option>
						</select>
		</div>		
	</div>

</div>


<script type="text/JavaScript">
$(document).ready(function(){

	storeCharacteristic(252,'Habitat','text');
	storeCharacteristic(253,'Main Colour','text');
	storeCharacteristic(254,'Pattern','media');
	storeCharacteristic(255,'Size','text');
	storeCharacteristic(256,'Length of Legs : Body Height','text');
	storeCharacteristic(257,'Antlers/Horns','text');
	storeCharacteristic(258,'Scales','text');
	imagePath = '../../../admin/media/project/0058/';

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
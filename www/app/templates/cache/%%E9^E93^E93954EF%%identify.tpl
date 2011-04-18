675
a:4:{s:8:"template";a:10:{s:12:"identify.tpl";b:1;s:19:"../shared/_head.tpl";b:1;s:25:"../shared/_body-start.tpl";b:1;s:109:"C:/Users/maarten/htdocs/linnaeus ng/linnaeus_ng/www/app/templates/templates/shared/0064/_header-container.tpl";b:1;s:102:"C:/Users/maarten/htdocs/linnaeus ng/linnaeus_ng/www/app/templates/templates/shared/0064/_main-menu.tpl";b:1;s:25:"../shared/_page-start.tpl";b:1;s:20:"../shared/header.tpl";b:1;s:11:"_header.tpl";b:1;s:99:"C:/Users/maarten/htdocs/linnaeus ng/linnaeus_ng/www/app/templates/templates/shared/0064/_footer.tpl";b:1;s:20:"../shared/footer.tpl";b:1;}s:9:"timestamp";i:1303118876;s:7:"expires";i:1303122476;s:13:"cache_serials";a:0:{}}<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title>TanBIF species: Matrix "Tanzania": identify</title>
	<style type="text/css" media="all">
		@import url("../../../app/style/0064/basics.css");
		@import url("../../../app/style/0064/matrix.css");
		@import url("../../../app/style/0064/colorbox/colorbox.css");
		@import url("../../../app/style/0064/dialog/jquery.modaldialog.css");
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
	<a href="../../../app/views/linnaeus/"><img src="../../../admin/media/project/0064/banner_logo_EN.jpg" id="project-logo" /></a>
	</div>
</div>

<div id="tanbif-menu">
<a href="/index.php" title="Home page"><span class="mainmenuitem" alt="Home page">Home page</span></a>
<span class="mainmenuseparator">|</span>
<a href="/search.php" title="Search"><span class="mainmenuitem" alt="Search">Search</span></a>
<span class="mainmenuseparator">|</span>
<a href="/app/views/species/" title="Browse species"><span class="mainmenuitem" alt="Browse species">Browse species</span></a>
<span class="mainmenuseparator">|</span>
<a href="/app/views/matrixkey/" title="Identify"><span class="mainmenuitem_selected" alt="Identify">Identify</span></a>
<span class="mainmenuseparator">|</span>
<a href="/news.php" title="Biodiversity news"><span class="mainmenuitem" alt="Biodiversity news">Biodiversity news</span></a>
<span class="mainmenuseparator">|</span>
<a href="/forum/index.php" title="Forum"><span class="mainmenuitem" alt="Forum">Forum</span></a>
<span class="mainmenuseparator">|</span>
<a href="/gallery.php" title="Gallery"><span class="mainmenuitem" alt="Gallery">Gallery</span></a>
<span class="mainmenuseparator">|</span>
<a href="/contentpage.php?cat=bio-facts" title="Bio facts"><span class="mainmenuitem" alt="Bio facts">Bio facts</span></a>
<span class="mainmenuseparator">|</span>
<a href="/contentpage.php?cat=partners" title="Partners"><span class="mainmenuitem" alt="Partners">Partners</span></a>
<span class="mainmenuseparator">|</span>
<a href="/contentpage.php?cat=about-tanbif" title="About TanBIF"><span class="mainmenuitem" alt="About TanBIF">About TanBIF</a>
<span class="mainmenuseparator">|</span>
<a href="/gbifwidget.php" title="GBIF Widget"><span class="mainmenuitem" alt="GBIF Widget">GBIF Widget</span></a>
<span class="mainmenuseparator"></span>
</div>
	<div id="menu-container">
	<div id="main-menu">
<div class="menu-item-container">
<a class="menu-item" href="../literature/">Literature</a><br />
</div>
<div class="menu-item-container">
<a class="menu-item" href="../species/">Species module</a><br />
</div>
<div class="menu-item-container">
<a class="menu-item" href="../highertaxa/">Higher taxa</a><br />
</div>
<div class="menu-item-container">
<a class="menu-item" href="../key/">Dichotomous key</a><br />
</div>
<div class="menu-item-container-active">
<span class="menu-active-indicator"><a class="menu-item-active" href="../matrixkey/">Matrix key</a></span><br />
</div>
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
</div>
<div id="page-container">
<div id="matrix-header">
	<div id="current">Using matrix "Tanzania".</div>
	</div>


<div id="page-main">
	<div id="pane-left">
		<div id="char-states">
			Characters<br />
			<select size="5" id="characteristics" onclick="goCharacteristic()" ondblclick="addSelected(this)" >
									<option value="336">Habitat</option>
												<option value="337">Main Colour</option>
												<option value="338">Pattern</option>
												<option value="339">Size</option>
												<option value="340">Length of Legs : Body Height</option>
												<option value="341">Antlers/Horns</option>
												<option value="342">Scales</option>
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
						<option ondblclick="goTaxon(20636)" value="20636">Acacia xanthophloea</option>
						<option ondblclick="goTaxon(20643)" value="20643">Acinonyx jubatus</option>
						<option ondblclick="goTaxon(20652)" value="20652">Adansonia digitata</option>
						<option ondblclick="goTaxon(20754)" value="20754">Connochaetes taurinus</option>
						<option ondblclick="goTaxon(20763)" value="20763">Crocodylus niloticus</option>
						<option ondblclick="goTaxon(20766)" value="20766">Crocuta crocuta</option>
						<option ondblclick="goTaxon(20779)" value="20779">Diceros bicornis</option>
						<option ondblclick="goTaxon(20795)" value="20795">Equus burchellii</option>
						<option ondblclick="goTaxon(20803)" value="20803">Gazella thomsonii</option>
						<option ondblclick="goTaxon(20809)" value="20809">Giraffa camelopardalis</option>
						<option ondblclick="goTaxon(20814)" value="20814">Gyps rueppellii</option>
						<option ondblclick="goTaxon(20834)" value="20834">Hippopotamus amphibius</option>
						<option ondblclick="goTaxon(20876)" value="20876">Lates niloticus</option>
						<option ondblclick="goTaxon(20886)" value="20886">Loxodonta africana</option>
						<option ondblclick="goTaxon(20896)" value="20896">Mecistops cataphractus</option>
						<option ondblclick="goTaxon(20949)" value="20949">Panthera pardus</option>
						<option ondblclick="goTaxon(20958)" value="20958">Papio anubis</option>
						<option ondblclick="goTaxon(20968)" value="20968">Phacochoerus africanus</option>
						<option ondblclick="goTaxon(21029)" value="21029">Struthio camelus</option>
						<option ondblclick="goTaxon(21033)" value="21033">Syncerus caffer</option>
						</select>
		</div>		
	</div>

</div>


<script type="text/JavaScript">
$(document).ready(function(){

	storeCharacteristic(336,'Habitat','text');
	storeCharacteristic(337,'Main Colour','text');
	storeCharacteristic(338,'Pattern','media');
	storeCharacteristic(339,'Size','text');
	storeCharacteristic(340,'Length of Legs : Body Height','text');
	storeCharacteristic(341,'Antlers/Horns','text');
	storeCharacteristic(342,'Scales','text');
	imagePath = '../../../admin/media/project/0064/';

});
</script>


	</div ends="page-container">
<div id="footer-container">

					<table border="0" cellspacing="0" cellpadding="0" width="940">
						<tr>

							<td valign="top">
								<p class="footerlinks">
								<a style="color:inherit" href="/tanbif/contentpage.php?cat=about-tanbif&pag=contact">Contact TanBIF</a> | <a style="color:inherit" href="/tanbif/contentpage.php?cat=about-tanbif&pag=help-desk">Help desk</a> | <a style="color:inherit" href="/tanbif/contentpage.php?cat=about-tanbif&pag=disclaimer">Disclaimer</a> | <a style="color:inherit" href="/tanbif/contentpage.php?cat=about-tanbif&pag=credits">Credits</a>
								</p>

							</td>
							<td valign="top" align="right">
								<p class="footerlinks">Site developed by <a href="http://www.eti.uva.nl" target="_blank">ETI BioInformatics</a> with the BioPortal&trade; Toolkit
								</p>
							</td>
						</tr>
					</table>



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

addRequestVar('0','')

})

</script>

</body>
</html>
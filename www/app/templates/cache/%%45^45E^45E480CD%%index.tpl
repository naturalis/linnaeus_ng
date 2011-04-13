647
a:4:{s:8:"template";a:9:{s:9:"index.tpl";b:1;s:19:"../shared/_head.tpl";b:1;s:25:"../shared/_body-start.tpl";b:1;s:109:"C:/Users/maarten/htdocs/linnaeus ng/linnaeus_ng/www/app/templates/templates/shared/0064/_header-container.tpl";b:1;s:102:"C:/Users/maarten/htdocs/linnaeus ng/linnaeus_ng/www/app/templates/templates/shared/0064/_main-menu.tpl";b:1;s:25:"../shared/_page-start.tpl";b:1;s:20:"../shared/header.tpl";b:1;s:99:"C:/Users/maarten/htdocs/linnaeus ng/linnaeus_ng/www/app/templates/templates/shared/0064/_footer.tpl";b:1;s:20:"../shared/footer.tpl";b:1;}s:9:"timestamp";i:1302699940;s:7:"expires";i:1302703540;s:13:"cache_serials";a:0:{}}<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title>TanBIF species: Dichotomous key: step 1: "Choose key type"</title>
	<style type="text/css" media="all">
		@import url("../../../app/style/0064/basics.css");
		@import url("../../../app/style/0064/key.css");
		@import url("../../../app/style/0064/colorbox/colorbox.css");
	</style>
	<script type="text/javascript" src="../../../app/javascript/jquery-1.4.2.min.js"></script>
	<script type="text/javascript" src="../../../app/javascript/jquery.tools.min.js"></script>
	<script type="text/javascript" src="../../../admin/javascript/sprintf-0.7-beta1.js"></script>
	
	<script type="text/javascript" src="../../../app/javascript/main.js"></script>
	<script type="text/javascript" src="../../../app/javascript/key.js"></script>
	<script type="text/javascript" src="../../../app/javascript/colorbox/jquery.colorbox.js"></script>
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
<a href="/app/views/matrixkey/" title="Identify"><span class="mainmenuitem" alt="Identify">Identify</span></a>
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
<div class="menu-item-container-active">
<span class="menu-active-indicator"><a class="menu-item-active" href="../key/">Dichotomous key</a></span><br />
</div>
<div class="menu-item-container">
<a class="menu-item" href="../matrixkey/">Matrix key</a><br />
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

<div id="path">
	<div id="concise">
	<span onclick="keyToggleFullPath()" id="toggle">Path:</span>
						1. <span class="item" onclick="keyDoStep(2379)">Choose key type</span>
				</div>
	<div id="path-full" class="full-invisible">
	<table>
			<tr>
			<td class="number-cell">1. </td>
			<td><span class="item" onclick="keyDoStep(2379)">Choose key type</span></td>
		</tr>
		</table>
	</div>
</div>

<div id="taxa" style="overflow-y:scroll;">
<span id="header">20 possible taxa remaining:</span><br/>
<span class="a" style="padding-left:3px" onclick="goTaxon(20636)">
	Acacia xanthophloea
	</span><br />
<span class="a" style="padding-left:3px" onclick="goTaxon(20652)">
	Adansonia digitata
	</span><br />
<span class="a" style="padding-left:3px" onclick="goTaxon(21029)">
	Struthio camelus
	</span><br />
<span class="a" style="padding-left:3px" onclick="goTaxon(20814)">
	Gyps rueppellii
	</span><br />
<span class="a" style="padding-left:3px" onclick="goTaxon(20763)">
	Crocodylus niloticus
	</span><br />
<span class="a" style="padding-left:3px" onclick="goTaxon(20896)">
	Mecistops cataphractus
	</span><br />
<span class="a" style="padding-left:3px" onclick="goTaxon(20766)">
	Crocuta crocuta
	</span><br />
<span class="a" style="padding-left:3px" onclick="goTaxon(20958)">
	Papio anubis
	</span><br />
<span class="a" style="padding-left:3px" onclick="goTaxon(20968)">
	Phacochoerus africanus
	</span><br />
<span class="a" style="padding-left:3px" onclick="goTaxon(20949)">
	Panthera pardus
	</span><br />
<span class="a" style="padding-left:3px" onclick="goTaxon(20754)">
	Connochaetes taurinus
	</span><br />
<span class="a" style="padding-left:3px" onclick="goTaxon(20643)">
	Acinonyx jubatus
	</span><br />
<span class="a" style="padding-left:3px" onclick="goTaxon(20803)">
	Gazella thomsonii
	</span><br />
<span class="a" style="padding-left:3px" onclick="goTaxon(21033)">
	Syncerus caffer
	</span><br />
<span class="a" style="padding-left:3px" onclick="goTaxon(20834)">
	Hippopotamus amphibius
	</span><br />
<span class="a" style="padding-left:3px" onclick="goTaxon(20779)">
	Diceros bicornis
	</span><br />
<span class="a" style="padding-left:3px" onclick="goTaxon(20886)">
	Loxodonta africana
	</span><br />
<span class="a" style="padding-left:3px" onclick="goTaxon(20795)">
	Equus burchellii
	</span><br />
<span class="a" style="padding-left:3px" onclick="goTaxon(20809)">
	Giraffa camelopardalis
	</span><br />
<span class="a" style="padding-left:3px" onclick="goTaxon(20876)">
	Lates niloticus
	</span><br />
</div>
	
<div id="page-main">
	<div id="step">
		<div id="question">
			<div id="head">
				<span id="step-nr">1</span>.
				<span id="step-title">Choose key type</span>
			</div>
			<div id="content">Choose between picture key and text key</div>
		</div>
		<div id="choices">

			<div class="choice">
						<span class="marker">a</span>.
					<span class="text">Text key</span>
					<br />
					<span class="target">
											<span class="arrow">&rarr;</span>
						<span class="target-step" onclick="keyDoChoice(3500)">Step 2: 1</span>
										</span>

			</div>
			<div class="choice">
						<span class="marker">b</span>.
					<span class="text">Picture key</span>
					<br />
					<span class="target">
											<span class="arrow">&rarr;</span>
						<span class="target-step" onclick="keyDoChoice(3501)">Step 22: Wildlife Key Tanzania</span>
										</span>

			</div>
		</div>
	</div>
</div>

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
559
a:4:{s:8:"template";a:9:{s:9:"index.tpl";b:1;s:19:"../shared/_head.tpl";b:1;s:25:"../shared/_body-start.tpl";b:1;s:109:"C:/Users/maarten/htdocs/linnaeus ng/linnaeus_ng/www/app/templates/templates/shared/0064/_header-container.tpl";b:1;s:102:"C:/Users/maarten/htdocs/linnaeus ng/linnaeus_ng/www/app/templates/templates/shared/0064/_main-menu.tpl";b:1;s:25:"../shared/_page-start.tpl";b:1;s:20:"../shared/header.tpl";b:1;s:11:"_header.tpl";b:1;s:20:"../shared/footer.tpl";b:1;}s:9:"timestamp";i:1302622395;s:7:"expires";i:1302625995;s:13:"cache_serials";a:0:{}}<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title>TanBIF species: Matrix "Tanzania"</title>
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
<a href="/index.php" title="Home page" style="text-decoration: none; color: rgb(66, 80, 155);"><span class="mainmenuitem_selected" alt="Home page">Home page</span></a><span class="mainmenuseparator">|</span><a href="/search.php" title="Search" style="text-decoration: none; color: rgb(66, 80, 155);"><span class="mainmenuitem" alt="Search">Search</span></a><span class="mainmenuseparator">|</span><a href="/lin2/tanbif_linnaeus.php?menuentry=zoeken" title="Browse species" style="text-decoration: none; color: rgb(66, 80, 155);"><span class="mainmenuitem" alt="Browse species">Browse species</span></a><span class="mainmenuseparator">|</span><a href="/lin2/tanbif_linnaeus.php?menuentry=sleutel" title="Identify" style="text-decoration: none; color: rgb(66, 80, 155);"><span class="mainmenuitem" alt="Identify">Identify</span></a><span class="mainmenuseparator">|</span><a href="/news.php" title="Biodiversity news" style="text-decoration: none; color: rgb(66, 80, 155);"><span class="mainmenuitem" alt="Biodiversity news">Biodiversity news</span></a><span class="mainmenuseparator">|</span><a href="/forum/index.php" title="Forum" style="text-decoration: none; color: rgb(66, 80, 155);"><span class="mainmenuitem" alt="Forum">Forum</span></a><span class="mainmenuseparator">|</span><a href="/gallery.php" title="Gallery" style="text-decoration: none; color: rgb(66, 80, 155);"><span class="mainmenuitem" alt="Gallery">Gallery</span></a><span class="mainmenuseparator">|</span><a href="/contentpage.php?cat=bio-facts" title="Bio facts" style="text-decoration: none; color: rgb(66, 80, 155);"><span class="mainmenuitem" alt="Bio facts">Bio facts</span></a><span class="mainmenuseparator">|</span><a href="/contentpage.php?cat=partners" title="Partners" style="text-decoration: none; color: rgb(66, 80, 155);"><span class="mainmenuitem" alt="Partners">Partners</span></a><span class="mainmenuseparator">|</span><a href="/contentpage.php?cat=about-tanbif" title="About TanBIF" style="text-decoration: none; color: rgb(66, 80, 155);"><span class="mainmenuitem" alt="About TanBIF">About TanBIF</a><span class="mainmenuseparator">|</span><a href="/gbifwidget.php" title="GBIF Widget" style="text-decoration: none; color: rgb(66, 80, 155);"><span class="mainmenuitem" alt="GBIF Widget">GBIF Widget</span></a><span class="mainmenuseparator"></span>
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
	<div id="menu">
		<table>
			<tr><td><a href="identify.php">Identify</a></td></tr>
			<tr><td><a href="examine.php">Examine</a></td></tr>
			<tr><td><a href="compare.php">Compare</a></td></tr>
		</table>
	</div>
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
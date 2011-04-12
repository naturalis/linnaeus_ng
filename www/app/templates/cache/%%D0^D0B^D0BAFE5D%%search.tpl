450
a:4:{s:8:"template";a:8:{s:10:"search.tpl";b:1;s:19:"../shared/_head.tpl";b:1;s:25:"../shared/_body-start.tpl";b:1;s:102:"C:/Users/maarten/htdocs/linnaeus ng/linnaeus_ng/www/app/templates/templates/shared/0064/_main-menu.tpl";b:1;s:25:"../shared/_page-start.tpl";b:1;s:20:"../shared/header.tpl";b:1;s:22:"../shared/messages.tpl";b:1;s:20:"../shared/footer.tpl";b:1;}s:9:"timestamp";i:1302617019;s:7:"expires";i:1302620619;s:13:"cache_serials";a:0:{}}<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title></title>
	<style type="text/css" media="all">
		@import url("../../../app/style/0064/basics.css");
		@import url("../../../app/style/0064/search.css");
	</style>
	<script type="text/javascript" src="../../../app/javascript/jquery-1.4.2.min.js"></script>
	<script type="text/javascript" src="../../../app/javascript/jquery.tools.min.js"></script>
	<script type="text/javascript" src="../../../admin/javascript/sprintf-0.7-beta1.js"></script>
	
	<script type="text/javascript" src="../../../app/javascript/main.js"></script>
	<script type="text/javascript" src="../../../app/javascript/speciesdetailpage10.js"></script>
</head><body id="body"><form method="post" action="" id="theForm" onsubmit="return checkForm();"><div id="body-container"><div id="menu-container">
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
			value="mammal"
			onkeydown="setSearchKeyed(true);"
			onblur="setSearchKeyed(false);"
			onfocus="onSearchBoxSelect()" />
			<img src="../../media/system/search.gif" onclick="doSearch();" />
		<select id="languageSelect" onchange="doLanguageChange()">
				<option value="26" selected="selected">English *</option>
			</select>
	</div>
</div><div id="page-container">

<div id="page-main">
<div id="results">
<div id="header">
	Your search for "mammal" produced 27 results:</div>

<div class="set">
											</div>


<div class="set">
			</div>

<div class="set">
						It is not possible to jump directly to a specific step or choice of the dichotomous key. Click <a href="../key/">here</a> to start the key from the start.</div>





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
	onSearchBoxSelect('mammal');
addRequestVar('search','mammal')

})

</script>

</body>
</html>
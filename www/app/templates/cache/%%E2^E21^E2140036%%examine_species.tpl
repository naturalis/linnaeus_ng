346
a:4:{s:8:"template";a:7:{s:19:"examine_species.tpl";b:1;s:19:"../shared/_head.tpl";b:1;s:25:"../shared/_body-start.tpl";b:1;s:24:"../shared/_main-menu.tpl";b:1;s:25:"../shared/_page-start.tpl";b:1;s:20:"../shared/header.tpl";b:1;s:20:"../shared/footer.tpl";b:1;}s:9:"timestamp";i:1301580167;s:7:"expires";i:1301583767;s:13:"cache_serials";a:0:{}}<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title>Imaginary Beings Of The Literary World: Displaying "Ursus arctos"</title>
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

	<div id="map_canvas"></div>
	<div id="map_options">
		<b>Ursus arctos</b><br/>
		Coordinates: <span id="coordinates">(-1,-1)</span><br />
		<hr style="height:1px;color:#999" />
		<table>
							<tr style="vertical-align:top">
				<td style="width:25px;border:1px solid black;background-color:#FFA200"></td>
				<td style="width:5px;"></td>
				<td style="width:215px;">Occurrence (1)</td>
				<td style="width:25px;" hidden="0" onclick="doMapTypeToggle(this,24)" class="a">hide</td>
			</tr>
			<tr><td colspan="4" style="height:1px;"></td></tr>
																				</table>
		<hr style="height:1px;color:#999" />
		<span id="back" onclick="goMap(null,'examine.php')">back to species list</span>

		
	</div>

</div>


<script type="text/JavaScript">
$(document).ready(function(){


	initMap();
		map.fitBounds(new google.maps.LatLngBounds(new google.maps.LatLng(51.33222485976149,  3.428955078125), new google.maps.LatLng(53.404521764119124,  7.537841796875)));
	


	var nodes0 = Array();
		nodes0[0] = [51.44876946513085,  3.505859375];
		nodes0[1] = [51.5718462228036,  3.428955078125];
		nodes0[2] = [51.61961963870789,  3.494873046875];
		nodes0[3] = [52.087843828712,  4.1650390625];
		nodes0[4] = [52.531160687276845,  4.505615234375];
		nodes0[5] = [52.96343024177537,  4.769287109375];
		nodes0[6] = [53.09557448015189,  4.769287109375];
		nodes0[7] = [53.19441710468082,  5.164794921875];
		nodes0[8] = [53.404521764119124,  6.25244140625];
		nodes0[9] = [53.075778651429154,  7.537841796875];
		nodes0[10] = [52.60461559308817,  7.21923828125];
		nodes0[11] = [52.38388157895273,  7.21923828125];
		nodes0[12] = [52.162038414959135,  6.966552734375];
		nodes0[13] = [52.12832891625864,  6.856689453125];
		nodes0[14] = [51.80341808972336,  6.669921875];
		nodes0[15] = [51.48299080794233,  6.483154296875];
		nodes0[16] = [51.33222485976149,  6.109619140625];
		nodes0[17] = [51.35967382928726,  5.59326171875];
		nodes0[18] = [51.476148592496486,  5.582275390625];
		nodes0[19] = [51.599151470592936,  5.0439453125];
		nodes0[20] = [51.59232669700684,  4.879150390625];
		drawPolygon(nodes0,null,{
		name: 'Ursus arctos',
		addMarker: true,
		addDelete: false,
		occurrenceId: 846,
		colour:'FFA200',
		typeId:24
	});



});
</script>





</div>

</div ends="page-container">
<div id="footer-container">
</div ends="footer-container">
</div ends="body-container">
</form>

<script type="text/JavaScript">
$(document).ready(function(){

	$('#body-container').height($(document).height());
	
addRequestVar('search','enter search term')
addRequestVar('id','19')

})

</script>

</body>
</html>
346
a:4:{s:8:"template";a:7:{s:19:"examine_species.tpl";b:1;s:19:"../shared/_head.tpl";b:1;s:25:"../shared/_body-start.tpl";b:1;s:24:"../shared/_main-menu.tpl";b:1;s:25:"../shared/_page-start.tpl";b:1;s:20:"../shared/header.tpl";b:1;s:20:"../shared/footer.tpl";b:1;}s:9:"timestamp";i:1301573661;s:7:"expires";i:1301577261;s:13:"cache_serials";a:0:{}}<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title>Imaginary Beings Of The Literary World: Displaying "Ursus maritimus"</title>
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
		<b>Ursus maritimus</b><br/>
		Coordinates: <span id="coordinates">(-1,-1)</span><br />
		<hr style="height:1px;color:#999" />
		<table>
							<tr style="vertical-align:top">
				<td style="width:25px;border:1px solid black;background-color:#FFA200"></td>
				<td style="width:5px;"></td>
				<td style="width:215px;">Occurrence (39)</td>
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
		map.fitBounds(new google.maps.LatLngBounds(new google.maps.LatLng(17.2535044901, -74.0485676627), new google.maps.LatLng(32.5991771506, -54.3058812065)));
	


	placeMarker([25.8711963576,-71.1485676627],{
		name: 'Ursus maritimus',
		addMarker: true,
		addDelete: false,
		occurrenceId: 681,
		colour:'FFA200',
		typeId:24
	});


	placeMarker([24.4711963576,-74.0485676627],{
		name: 'Ursus maritimus',
		addMarker: true,
		addDelete: false,
		occurrenceId: 715,
		colour:'FFA200',
		typeId:24
	});


	var nodes2 = Array();
		nodes2[0] = [27.1940131851, -66.5452791588];
		nodes2[1] = [24.2762068202, -65.8352040995];
		nodes2[2] = [25.7700426918, -64.9020779883];
		nodes2[3] = [28.7355680056, -66.2735326378];
		nodes2[4] = [29.052437999, -67.5891964517];
		drawPolygon(nodes2,null,{
		name: 'Ursus maritimus',
		addMarker: true,
		addDelete: false,
		occurrenceId: 704,
		colour:'FFA200',
		typeId:24
	});



	placeMarker([27.3966081014,-68.3555521190],{
		name: 'Ursus maritimus',
		addMarker: true,
		addDelete: false,
		occurrenceId: 677,
		colour:'FFA200',
		typeId:24
	});


	placeMarker([21.6577215958,-72.1882913105],{
		name: 'Ursus maritimus',
		addMarker: true,
		addDelete: false,
		occurrenceId: 703,
		colour:'FFA200',
		typeId:24
	});


	placeMarker([22.4998154217,-68.2705621049],{
		name: 'Ursus maritimus',
		addMarker: true,
		addDelete: false,
		occurrenceId: 698,
		colour:'FFA200',
		typeId:24
	});


	placeMarker([24.8711963576,-72.1485676627],{
		name: 'Ursus maritimus',
		addMarker: true,
		addDelete: false,
		occurrenceId: 694,
		colour:'FFA200',
		typeId:24
	});


	placeMarker([19.8847317394,-66.7406957944],{
		name: 'Ursus maritimus',
		addMarker: true,
		addDelete: false,
		occurrenceId: 699,
		colour:'FFA200',
		typeId:24
	});


	placeMarker([27.1056855703,-59.4246021224],{
		name: 'Ursus maritimus',
		addMarker: true,
		addDelete: false,
		occurrenceId: 700,
		colour:'FFA200',
		typeId:24
	});


	placeMarker([25.0966081014,-70.6555521190],{
		name: 'Ursus maritimus',
		addMarker: true,
		addDelete: false,
		occurrenceId: 690,
		colour:'FFA200',
		typeId:24
	});


	placeMarker([30.8326150796,-66.9929531668],{
		name: 'Ursus maritimus',
		addMarker: true,
		addDelete: false,
		occurrenceId: 675,
		colour:'FFA200',
		typeId:24
	});


	placeMarker([29.5351096373,-64.5429804641],{
		name: 'Ursus maritimus',
		addMarker: true,
		addDelete: false,
		occurrenceId: 712,
		colour:'FFA200',
		typeId:24
	});


	var nodes12 = Array();
		nodes12[0] = [29.4709270728, -58.5759206723];
		nodes12[1] = [29.0582697748, -58.5557157772];
		nodes12[2] = [29.1302747974, -58.6019854202];
		nodes12[3] = [31.3110362221, -59.6204825522];
		nodes12[4] = [31.1403018861, -60.9114003352];
		nodes12[5] = [32.5991771506, -62.6103796109];
		nodes12[6] = [30.7241428791, -62.7609177176];
		nodes12[7] = [30.2872441866, -64.1724076373];
		nodes12[8] = [27.8705419104, -62.3115726307];
		drawPolygon(nodes12,null,{
		name: 'Ursus maritimus',
		addMarker: true,
		addDelete: false,
		occurrenceId: 682,
		colour:'FFA200',
		typeId:24
	});



	placeMarker([25.6046588661,-68.7754826618],{
		name: 'Ursus maritimus',
		addMarker: true,
		addDelete: false,
		occurrenceId: 695,
		colour:'FFA200',
		typeId:24
	});


	placeMarker([23.9065268726,-68.3988461393],{
		name: 'Ursus maritimus',
		addMarker: true,
		addDelete: false,
		occurrenceId: 713,
		colour:'FFA200',
		typeId:24
	});


	placeMarker([24.6046588661,-69.7754826618],{
		name: 'Ursus maritimus',
		addMarker: true,
		addDelete: false,
		occurrenceId: 702,
		colour:'FFA200',
		typeId:24
	});


	placeMarker([19.4847317394,-68.6406957944],{
		name: 'Ursus maritimus',
		addMarker: true,
		addDelete: false,
		occurrenceId: 720,
		colour:'FFA200',
		typeId:24
	});


	placeMarker([19.1535044901,-63.5384721354],{
		name: 'Ursus maritimus',
		addMarker: true,
		addDelete: false,
		occurrenceId: 696,
		colour:'FFA200',
		typeId:24
	});


	placeMarker([19.5577215958,-71.5882913105],{
		name: 'Ursus maritimus',
		addMarker: true,
		addDelete: false,
		occurrenceId: 718,
		colour:'FFA200',
		typeId:24
	});


	var nodes19 = Array();
		nodes19[0] = [23.9836490014, -59.479874752];
		nodes19[1] = [26.6499249095, -54.3058812065];
		nodes19[2] = [29.4683470753, -57.117904168];
		nodes19[3] = [29.6588115308, -59.5588565519];
		nodes19[4] = [28.5844777035, -61.6784868044];
		nodes19[5] = [24.7641322086, -64.1974847507];
		nodes19[6] = [23.315631146, -65.1764959919];
		nodes19[7] = [21.3047564067, -62.1150871942];
		nodes19[8] = [23.1508266633, -58.9798246383];
		nodes19[9] = [23.9446000449, -59.5391541484];
		drawPolygon(nodes19,null,{
		name: 'Ursus maritimus',
		addMarker: true,
		addDelete: false,
		occurrenceId: 721,
		colour:'FFA200',
		typeId:24
	});



	placeMarker([17.2535044901,-61.2384721354],{
		name: 'Ursus maritimus',
		addMarker: true,
		addDelete: false,
		occurrenceId: 717,
		colour:'FFA200',
		typeId:24
	});


	placeMarker([28.2351096373,-65.8429804641],{
		name: 'Ursus maritimus',
		addMarker: true,
		addDelete: false,
		occurrenceId: 678,
		colour:'FFA200',
		typeId:24
	});


	placeMarker([25.9940451364,-66.6889293583],{
		name: 'Ursus maritimus',
		addMarker: true,
		addDelete: false,
		occurrenceId: 714,
		colour:'FFA200',
		typeId:24
	});


	placeMarker([29.8326150796,-67.9929531668],{
		name: 'Ursus maritimus',
		addMarker: true,
		addDelete: false,
		occurrenceId: 688,
		colour:'FFA200',
		typeId:24
	});


	placeMarker([21.4998154217,-69.2705621049],{
		name: 'Ursus maritimus',
		addMarker: true,
		addDelete: false,
		occurrenceId: 685,
		colour:'FFA200',
		typeId:24
	});


	placeMarker([20.6577215958,-71.1882913105],{
		name: 'Ursus maritimus',
		addMarker: true,
		addDelete: false,
		occurrenceId: 697,
		colour:'FFA200',
		typeId:24
	});


	placeMarker([20.3998154217,-70.3705621049],{
		name: 'Ursus maritimus',
		addMarker: true,
		addDelete: false,
		occurrenceId: 706,
		colour:'FFA200',
		typeId:24
	});


	placeMarker([28.1056855703,-58.4246021224],{
		name: 'Ursus maritimus',
		addMarker: true,
		addDelete: false,
		occurrenceId: 687,
		colour:'FFA200',
		typeId:24
	});


	placeMarker([25.9940451364,-62.1549293583],{
		name: 'Ursus maritimus',
		addMarker: true,
		addDelete: false,
		occurrenceId: 680,
		colour:'FFA200',
		typeId:24
	});


	placeMarker([21.6577215958,-70.1882913105],{
		name: 'Ursus maritimus',
		addMarker: true,
		addDelete: false,
		occurrenceId: 684,
		colour:'FFA200',
		typeId:24
	});


	placeMarker([23.6940451364,-64.4549293583],{
		name: 'Ursus maritimus',
		addMarker: true,
		addDelete: false,
		occurrenceId: 693,
		colour:'FFA200',
		typeId:24
	});


	placeMarker([24.3065268726,-67.9988461393],{
		name: 'Ursus maritimus',
		addMarker: true,
		addDelete: false,
		occurrenceId: 692,
		colour:'FFA200',
		typeId:24
	});


	var nodes32 = Array();
		nodes32[0] = [26.8170937243, -64.8055224316];
		nodes32[1] = [25.8406013517, -65.780283421];
		nodes32[2] = [24.1686760108, -64.5210249795];
		nodes32[3] = [24.5599479188, -62.9306517636];
		nodes32[4] = [25.3682535062, -63.9735370812];
		nodes32[5] = [26.2344869757, -63.6087776749];
		drawPolygon(nodes32,null,{
		name: 'Ursus maritimus',
		addMarker: true,
		addDelete: false,
		occurrenceId: 701,
		colour:'FFA200',
		typeId:24
	});



	placeMarker([21.4535044901,-61.2384721354],{
		name: 'Ursus maritimus',
		addMarker: true,
		addDelete: false,
		occurrenceId: 683,
		colour:'FFA200',
		typeId:24
	});


	placeMarker([23.3065268726,-68.9988461393],{
		name: 'Ursus maritimus',
		addMarker: true,
		addDelete: false,
		occurrenceId: 679,
		colour:'FFA200',
		typeId:24
	});


	placeMarker([27.2351096373,-66.8429804641],{
		name: 'Ursus maritimus',
		addMarker: true,
		addDelete: false,
		occurrenceId: 691,
		colour:'FFA200',
		typeId:24
	});


	placeMarker([22.1847317394,-64.4406957944],{
		name: 'Ursus maritimus',
		addMarker: true,
		addDelete: false,
		occurrenceId: 686,
		colour:'FFA200',
		typeId:24
	});


	placeMarker([23.3706588661,-69.8754826618],{
		name: 'Ursus maritimus',
		addMarker: true,
		addDelete: false,
		occurrenceId: 716,
		colour:'FFA200',
		typeId:24
	});


	placeMarker([24.4847317394,-62.1406957944],{
		name: 'Ursus maritimus',
		addMarker: true,
		addDelete: false,
		occurrenceId: 707,
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
addRequestVar('id','16')

})

</script>

</body>
</html>
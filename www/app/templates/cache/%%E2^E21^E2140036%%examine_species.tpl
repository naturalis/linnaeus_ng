346
a:4:{s:8:"template";a:7:{s:19:"examine_species.tpl";b:1;s:19:"../shared/_head.tpl";b:1;s:25:"../shared/_body-start.tpl";b:1;s:24:"../shared/_main-menu.tpl";b:1;s:25:"../shared/_page-start.tpl";b:1;s:20:"../shared/header.tpl";b:1;s:20:"../shared/footer.tpl";b:1;}s:9:"timestamp";i:1301490774;s:7:"expires";i:1301494374;s:13:"cache_serials";a:0:{}}<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title>Imaginary Beings Of The Literary World: Displaying "Ursus luteolus"</title>
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
		<b>Ursus luteolus</b><br/>
		Coordinates: <span id="coordinates">(-1,-1)</span><br />
		<hr style="height:1px;color:#999" />
		<table>
											<tr style="vertical-align:top">
				<td style="width:25px;border:1px solid black;background-color:#DE0909"></td>
				<td style="width:5px;"></td>
				<td style="width:215px;">Occurrences before 1900 (1)</td>
				<td style="width:25px;" hidden="0" onclick="doMapTypeToggle(25,this)" class="a">hide</td>
			</tr>
			<tr><td colspan="4" style="height:1px;"></td></tr>
									<tr style="vertical-align:top">
				<td style="width:25px;border:1px solid black;background-color:#42CF55"></td>
				<td style="width:5px;"></td>
				<td style="width:215px;">Favourite hangouts (2)</td>
				<td style="width:25px;" hidden="0" onclick="doMapTypeToggle(26,this)" class="a">hide</td>
			</tr>
			<tr><td colspan="4" style="height:1px;"></td></tr>
									<tr style="vertical-align:top">
				<td style="width:25px;border:1px solid black;background-color:#2D3DA1"></td>
				<td style="width:5px;"></td>
				<td style="width:215px;">Claimed territory (6)</td>
				<td style="width:25px;" hidden="0" onclick="doMapTypeToggle(27,this)" class="a">hide</td>
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
		map.fitBounds(new google.maps.LatLngBounds(new google.maps.LatLng(49.14028568238951,  1.11083984375), new google.maps.LatLng(54.61175023571463,  10.47119140625)));
	


	var nodes0 = Array();
		nodes0[0] = [50.431162671908744,  3.26416015625];
		nodes0[1] = [51.687780229555536,  3.9453125];
		nodes0[2] = [51.66052829321105,  5.68115234375];
		nodes0[3] = [50.849203113122755,  6.0107421875];
		nodes0[4] = [51.20850096634179,  5.32958984375];
		nodes0[5] = [51.49667215955402,  4.45068359375];
		nodes0[6] = [51.263530490972535,  4.27490234375];
		nodes0[7] = [50.80756634084576,  4.1650390625];
		nodes0[8] = [50.1926207624341,  4.12109375];
		nodes0[9] = [49.7545480475797,  3.96728515625];
		nodes0[10] = [49.72614827877714,  2.978515625];
		nodes0[11] = [50.03763202833471,  2.0556640625];
		nodes0[12] = [50.47313411776255,  1.7041015625];
		nodes0[13] = [50.91851522534097,  1.68212890625];
		nodes0[14] = [51.742234918928645,  1.220703125];
		nodes0[15] = [52.844154042156504,  1.77001953125];
		nodes0[16] = [53.82812064521293,  1.81396484375];
		nodes0[17] = [54.53533056824021,  1.11083984375];
		nodes0[18] = [54.61175023571463,  1.90185546875];
		nodes0[19] = [54.44599310078137,  2.71484375];
		nodes0[20] = [54.09957419343013,  3.06640625];
		nodes0[21] = [53.00311602934493,  2.1875];
		nodes0[22] = [52.29662134775002,  2.12158203125];
		nodes0[23] = [51.49667215955402,  1.85791015625];
		nodes0[24] = [50.83532831527039,  2.1435546875];
		drawPolygon(nodes0,null,{
		name: 'Ursus luteolus',
		addMarker: true,
		addDelete: false,
		occurrenceId: 742,
		colour:'2D3DA1',
		typeId:27
	});



	placeMarker([52.1350728575,7.0874023438],{
		name: 'Ursus luteolus',
		addMarker: true,
		addDelete: false,
		occurrenceId: 736,
		colour:'42CF55',
		typeId:26
	});


	placeMarker([51.5103494058,9.8559570312],{
		name: 'Ursus luteolus',
		addMarker: true,
		addDelete: false,
		occurrenceId: 737,
		colour:'2D3DA1',
		typeId:27
	});


	placeMarker([51.5103494058,8.5595703125],{
		name: 'Ursus luteolus',
		addMarker: true,
		addDelete: false,
		occurrenceId: 738,
		colour:'2D3DA1',
		typeId:27
	});


	placeMarker([51.6468961755,6.9555664062],{
		name: 'Ursus luteolus',
		addMarker: true,
		addDelete: false,
		occurrenceId: 739,
		colour:'2D3DA1',
		typeId:27
	});


	var nodes5 = Array();
		nodes5[0] = [50.68243312581954,  9.98779296875];
		nodes5[1] = [51.51034940583583,  6.42822265625];
		nodes5[2] = [49.6835174547195,  8.1201171875];
		nodes5[3] = [50.44515729105729,  8.40576171875];
		nodes5[4] = [50.29098884243417,  9.59228515625];
		nodes5[5] = [49.82547475768626,  9.39453125];
		nodes5[6] = [49.412653043190694,  8.88916015625];
		nodes5[7] = [49.65507612079224,  8.66943359375];
		nodes5[8] = [49.45552097034248,  8.515625];
		nodes5[9] = [49.14028568238951,  8.88916015625];
		nodes5[10] = [49.14028568238951,  9.81201171875];
		nodes5[11] = [49.981148067345835,  10.47119140625];
		drawPolygon(nodes5,null,{
		name: 'Ursus luteolus',
		addMarker: true,
		addDelete: false,
		occurrenceId: 741,
		colour:'2D3DA1',
		typeId:27
	});



	placeMarker([52.1350728575,9.5263671875],{
		name: 'Ursus luteolus',
		addMarker: true,
		addDelete: false,
		occurrenceId: 735,
		colour:'42CF55',
		typeId:26
	});


	placeMarker([52.4977314143,8.7353515625],{
		name: 'Ursus luteolus',
		addMarker: true,
		addDelete: false,
		occurrenceId: 734,
		colour:'DE0909',
		typeId:25
	});


	var nodes8 = Array();
		nodes8[0] = [52.18898764354807,  7.8564453125];
		nodes8[1] = [52.094593896016406,  4.1650390625];
		nodes8[2] = [49.86798093805886,  6.38427734375];
		drawPolygon(nodes8,null,{
		name: 'Ursus luteolus',
		addMarker: true,
		addDelete: false,
		occurrenceId: 740,
		colour:'2D3DA1',
		typeId:27
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
addRequestVar('id','7')

})

</script>

</body>
</html>
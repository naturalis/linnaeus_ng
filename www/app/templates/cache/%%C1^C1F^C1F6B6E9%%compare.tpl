338
a:4:{s:8:"template";a:7:{s:11:"compare.tpl";b:1;s:19:"../shared/_head.tpl";b:1;s:25:"../shared/_body-start.tpl";b:1;s:24:"../shared/_main-menu.tpl";b:1;s:25:"../shared/_page-start.tpl";b:1;s:20:"../shared/header.tpl";b:1;s:20:"../shared/footer.tpl";b:1;}s:9:"timestamp";i:1301495316;s:7:"expires";i:1301498916;s:13:"cache_serials";a:0:{}}<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title>Imaginary Beings Of The Literary World: Comparing taxa "Ursus luteolus" and "Melursus ursinus"</title>
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

<style>
.taxon-select {
	font-size:inherit;
	height:25px;
}
</style>


<div id="page-main">

	<div id="map_canvas"></div>
	<div id="map_options">

		<form method="post" action="">
		<p>
		Taxon A:
		<select name="idA" class="taxon-select">	
		<option value="" >--choose taxon--</option>
				<option value="57" >Melursus ursinus</option>
				<option value="58" >Melursus ursinus inornatus</option>
				<option value="59" >Melursus ursinus ursinus</option>
				<option value="55" >Tremarctos ornatus</option>
				<option value="7" selected="selected">Ursus luteolus</option>
				<option value="8" >Ursus thibetanus</option>
				<option value="9" >Ursus thibetanus laniger</option>
				<option value="10" >Ursus thibetanus thibetanus</option>
				<option value="11" >Ursus thibetanus gedrosianus</option>
				<option value="12" >Ursus thibetanus ussuricus</option>
				<option value="13" >Ursus thibetanus mupinensis</option>
				<option value="14" >Ursus thibetanus formosanus</option>
				<option value="15" >Ursus thibetanus japonicus</option>
				<option value="16" >Ursus maritimus</option>
				<option value="17" >Ursus maritimus marinus</option>
				<option value="18" >Ursus maritimus maritimus</option>
				<option value="19" >Ursus arctos</option>
				<option value="20" >Ursus arctos nelsoni</option>
				<option value="21" >Ursus arctos beringianus</option>
				<option value="22" >Ursus arctos pruinosus</option>
				<option value="23" >Ursus arctos horribilis</option>
				<option value="24" >Ursus arctos isabellinus</option>
				<option value="25" >Ursus arctos syriacus</option>
				<option value="26" >Ursus arctos stikeenensis</option>
				<option value="27" >Ursus arctos sitkensis</option>
				<option value="28" >Ursus arctos middendorffi</option>
				<option value="29" >Ursus arctos lasiotus</option>
				<option value="30" >Ursus arctos gyas</option>
				<option value="31" >Ursus arctos dalli</option>
				<option value="32" >Ursus arctos crowtheri</option>
				<option value="33" >Ursus arctos collaris</option>
				<option value="34" >Ursus arctos californicus</option>
				<option value="35" >Ursus arctos alascensis</option>
				<option value="36" >Ursus arctos arctos</option>
				<option value="37" >Ursus americanus</option>
				<option value="39" >Ursus americanus amblyceps</option>
				<option value="40" >Ursus americanus americanus</option>
				<option value="41" >Ursus americanus eremicus</option>
				<option value="42" >Ursus americanus emmonsii</option>
				<option value="43" >Ursus americanus carlottae</option>
				<option value="44" >Ursus americanus californiensis</option>
				<option value="45" >Ursus americanus altifrontalis</option>
				<option value="46" >Ursus americanus luteolus</option>
				<option value="47" >Ursus americanus perniger</option>
				<option value="48" >Ursus americanus machetes</option>
				<option value="49" >Ursus americanus floridanus</option>
				<option value="50" >Ursus americanus hamiltoni</option>
				<option value="51" >Ursus americanus kermodei</option>
				<option value="52" >Ursus americanus pugnax</option>
				<option value="53" >Ursus americanus vancouveri</option>
				<option value="171" >Proto knurft</option>
				<option value="73" >Pseudo knurft</option>
				<option value="170" >Oetmammoet</option>
				</select>	
		</p>
		<p>
		Taxon B:
		<select name="idB" class="taxon-select">	
		<option value="" >--choose taxon--</option>
				<option value="57" selected="selected">Melursus ursinus</option>
				<option value="58" >Melursus ursinus inornatus</option>
				<option value="59" >Melursus ursinus ursinus</option>
				<option value="55" >Tremarctos ornatus</option>
				<option value="7" >Ursus luteolus</option>
				<option value="8" >Ursus thibetanus</option>
				<option value="9" >Ursus thibetanus laniger</option>
				<option value="10" >Ursus thibetanus thibetanus</option>
				<option value="11" >Ursus thibetanus gedrosianus</option>
				<option value="12" >Ursus thibetanus ussuricus</option>
				<option value="13" >Ursus thibetanus mupinensis</option>
				<option value="14" >Ursus thibetanus formosanus</option>
				<option value="15" >Ursus thibetanus japonicus</option>
				<option value="16" >Ursus maritimus</option>
				<option value="17" >Ursus maritimus marinus</option>
				<option value="18" >Ursus maritimus maritimus</option>
				<option value="19" >Ursus arctos</option>
				<option value="20" >Ursus arctos nelsoni</option>
				<option value="21" >Ursus arctos beringianus</option>
				<option value="22" >Ursus arctos pruinosus</option>
				<option value="23" >Ursus arctos horribilis</option>
				<option value="24" >Ursus arctos isabellinus</option>
				<option value="25" >Ursus arctos syriacus</option>
				<option value="26" >Ursus arctos stikeenensis</option>
				<option value="27" >Ursus arctos sitkensis</option>
				<option value="28" >Ursus arctos middendorffi</option>
				<option value="29" >Ursus arctos lasiotus</option>
				<option value="30" >Ursus arctos gyas</option>
				<option value="31" >Ursus arctos dalli</option>
				<option value="32" >Ursus arctos crowtheri</option>
				<option value="33" >Ursus arctos collaris</option>
				<option value="34" >Ursus arctos californicus</option>
				<option value="35" >Ursus arctos alascensis</option>
				<option value="36" >Ursus arctos arctos</option>
				<option value="37" >Ursus americanus</option>
				<option value="39" >Ursus americanus amblyceps</option>
				<option value="40" >Ursus americanus americanus</option>
				<option value="41" >Ursus americanus eremicus</option>
				<option value="42" >Ursus americanus emmonsii</option>
				<option value="43" >Ursus americanus carlottae</option>
				<option value="44" >Ursus americanus californiensis</option>
				<option value="45" >Ursus americanus altifrontalis</option>
				<option value="46" >Ursus americanus luteolus</option>
				<option value="47" >Ursus americanus perniger</option>
				<option value="48" >Ursus americanus machetes</option>
				<option value="49" >Ursus americanus floridanus</option>
				<option value="50" >Ursus americanus hamiltoni</option>
				<option value="51" >Ursus americanus kermodei</option>
				<option value="52" >Ursus americanus pugnax</option>
				<option value="53" >Ursus americanus vancouveri</option>
				<option value="171" >Proto knurft</option>
				<option value="73" >Pseudo knurft</option>
				<option value="170" >Oetmammoet</option>
				</select>	
		</p>
		<p>
		<input type="submit" value="compare" />
		</p>
		</form>

		<hr style="height:1px;color:#999" />
		Coordinates: <span id="coordinates">(-1,-1)</span><br />

				<hr style="height:1px;color:#999" />
		<b>Comparison</b><br />
				There is no overlap between these two species.		<hr style="height:1px;color:#999" />
		
		<table>
					<tr style="vertical-align:top">
				<td colspan="4"><b>Ursus luteolus</b> (9)</td>
			</tr>
																<tr style="vertical-align:top">
					<td style="width:25px;border:1px solid black;background-color:#DE0909"></td>
					<td style="width:5px;"></td>
					<td style="width:215px;">Occurrences before 1900 (1)</td>
					<td style="width:25px;" hidden="0" onclick="doMapTypeToggle(25,this,7)" class="a">hide</td>
				</tr>
				<tr><td colspan="4" style="height:1px;"></td></tr>
													<tr style="vertical-align:top">
					<td style="width:25px;border:1px solid black;background-color:#42CF55"></td>
					<td style="width:5px;"></td>
					<td style="width:215px;">Favourite hangouts (2)</td>
					<td style="width:25px;" hidden="0" onclick="doMapTypeToggle(26,this,7)" class="a">hide</td>
				</tr>
				<tr><td colspan="4" style="height:1px;"></td></tr>
													<tr style="vertical-align:top">
					<td style="width:25px;border:1px solid black;background-color:#2D3DA1"></td>
					<td style="width:5px;"></td>
					<td style="width:215px;">Claimed territory (6)</td>
					<td style="width:25px;" hidden="0" onclick="doMapTypeToggle(27,this,7)" class="a">hide</td>
				</tr>
				<tr><td colspan="4" style="height:1px;"></td></tr>
													
					<tr style="vertical-align:top">
				<td colspan="4">&nbsp;</td>
			</tr>
			
					<tr style="vertical-align:top">
				<td colspan="4"><b>Melursus ursinus</b> (9)</td>
			</tr>
										<tr style="vertical-align:top">
					<td style="width:25px;border:1px solid black;background-color:#0088ff"></td>
					<td style="width:5px;"></td>
					<td style="width:215px;">Occurrence (9)</td>
					<td style="width:25px;" hidden="0" onclick="doMapTypeToggle(24,this,57)" class="a">hide</td>
				</tr>
				<tr><td colspan="4" style="height:1px;"></td></tr>
																															</table>

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
		name: 'Ursus luteolus: Claimed territory',
		addMarker: true,
		addDelete: false,
		occurrenceId: 742,
		taxonId: 7,
		colour:'2D3DA1',
		typeId:27
	});



	placeMarker([52.1350728575,7.0874023438],{
		name: 'Ursus luteolus: Favourite hangouts',
		addMarker: true,
		addDelete: false,
		occurrenceId: 736,
		taxonId: 7,
		colour:'42CF55',
		typeId:26
	});


	placeMarker([51.5103494058,9.8559570312],{
		name: 'Ursus luteolus: Claimed territory',
		addMarker: true,
		addDelete: false,
		occurrenceId: 737,
		taxonId: 7,
		colour:'2D3DA1',
		typeId:27
	});


	placeMarker([51.5103494058,8.5595703125],{
		name: 'Ursus luteolus: Claimed territory',
		addMarker: true,
		addDelete: false,
		occurrenceId: 738,
		taxonId: 7,
		colour:'2D3DA1',
		typeId:27
	});


	placeMarker([51.6468961755,6.9555664062],{
		name: 'Ursus luteolus: Claimed territory',
		addMarker: true,
		addDelete: false,
		occurrenceId: 739,
		taxonId: 7,
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
		name: 'Ursus luteolus: Claimed territory',
		addMarker: true,
		addDelete: false,
		occurrenceId: 741,
		taxonId: 7,
		colour:'2D3DA1',
		typeId:27
	});



	placeMarker([52.1350728575,9.5263671875],{
		name: 'Ursus luteolus: Favourite hangouts',
		addMarker: true,
		addDelete: false,
		occurrenceId: 735,
		taxonId: 7,
		colour:'42CF55',
		typeId:26
	});


	placeMarker([52.4977314143,8.7353515625],{
		name: 'Ursus luteolus: Occurrences before 1900',
		addMarker: true,
		addDelete: false,
		occurrenceId: 734,
		taxonId: 7,
		colour:'DE0909',
		typeId:25
	});


	var nodes8 = Array();
		nodes8[0] = [52.18898764354807,  7.8564453125];
		nodes8[1] = [52.094593896016406,  4.1650390625];
		nodes8[2] = [49.86798093805886,  6.38427734375];
		drawPolygon(nodes8,null,{
		name: 'Ursus luteolus: Claimed territory',
		addMarker: true,
		addDelete: false,
		occurrenceId: 740,
		taxonId: 7,
		colour:'2D3DA1',
		typeId:27
	});




	placeMarker([53.8110960683,8.3947753906],{
		name: 'Melursus ursinus: Occurrence',
		addMarker: true,
		addDelete: false,
		occurrenceId: 34,
		taxonId: 57,
		colour:'0088ff',
		typeId:24
	});


	placeMarker([53.6941696998,5.1867675781],{
		name: 'Melursus ursinus: Occurrence',
		addMarker: true,
		addDelete: false,
		occurrenceId: 31,
		taxonId: 57,
		colour:'0088ff',
		typeId:24
	});


	placeMarker([53.5116367585,5.8020019531],{
		name: 'Melursus ursinus: Occurrence',
		addMarker: true,
		addDelete: false,
		occurrenceId: 37,
		taxonId: 57,
		colour:'0088ff',
		typeId:24
	});


	placeMarker([53.4331667292,4.7473144531],{
		name: 'Melursus ursinus: Occurrence',
		addMarker: true,
		addDelete: false,
		occurrenceId: 38,
		taxonId: 57,
		colour:'0088ff',
		typeId:24
	});


	placeMarker([53.6811577854,4.0222167969],{
		name: 'Melursus ursinus: Occurrence',
		addMarker: true,
		addDelete: false,
		occurrenceId: 30,
		taxonId: 57,
		colour:'0088ff',
		typeId:24
	});


	placeMarker([53.8370355205,5.7800292969],{
		name: 'Melursus ursinus: Occurrence',
		addMarker: true,
		addDelete: false,
		occurrenceId: 32,
		taxonId: 57,
		colour:'0088ff',
		typeId:24
	});


	placeMarker([53.4724198802,7.0983886719],{
		name: 'Melursus ursinus: Occurrence',
		addMarker: true,
		addDelete: false,
		occurrenceId: 35,
		taxonId: 57,
		colour:'0088ff',
		typeId:24
	});


	placeMarker([53.9406328024,6.8566894531],{
		name: 'Melursus ursinus: Occurrence',
		addMarker: true,
		addDelete: false,
		occurrenceId: 33,
		taxonId: 57,
		colour:'0088ff',
		typeId:24
	});


	placeMarker([53.0254232664,6.7687988281],{
		name: 'Melursus ursinus: Occurrence',
		addMarker: true,
		addDelete: false,
		occurrenceId: 36,
		taxonId: 57,
		colour:'0088ff',
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
addRequestVar('idA','7')
addRequestVar('idB','57')

})

</script>

</body>
</html>
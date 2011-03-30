337
a:4:{s:8:"template";a:7:{s:10:"search.tpl";b:1;s:19:"../shared/_head.tpl";b:1;s:25:"../shared/_body-start.tpl";b:1;s:24:"../shared/_main-menu.tpl";b:1;s:25:"../shared/_page-start.tpl";b:1;s:20:"../shared/header.tpl";b:1;s:20:"../shared/footer.tpl";b:1;}s:9:"timestamp";i:1301505885;s:7:"expires";i:1301509485;s:13:"cache_serials";a:0:{}}<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title>Imaginary Beings Of The Literary World: Search</title>
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
		<form method="post" action="" id="theForm">
		Coordinates: <span id="coordinates">(-1,-1)</span><br />
		<hr style="height:1px;color:#999" />
	
		<input type="button" onclick="startPolygonDraw()" id="button-draw" value="draw area to search" /><br/>
		<input type="button" onclick="doMapSearch()" value="search" />
		<input type="button" onclick="clearPolygon();clearSearchResults();" value="clear" />
		</form>
		<hr style="height:1px;color:#999" />
		<table>
							<tr style="vertical-align:top">
				<td style="width:25px;border:1px solid black;background-color:#FF03AB"></td>
				<td style="width:5px;"></td>
				<td style="width:215px;">Occurrence (9)</td>
				<td style="width:25px;" hidden="0" onclick="doMapTypeToggle(24,this)" class="a">hide</td>
			</tr>
			<tr><td colspan="4" style="height:1px;"></td></tr>
									<tr style="vertical-align:top">
				<td style="width:25px;border:1px solid black;background-color:#00DECF"></td>
				<td style="width:5px;"></td>
				<td style="width:215px;">Occurrences before 1900 (9)</td>
				<td style="width:25px;" hidden="0" onclick="doMapTypeToggle(25,this)" class="a">hide</td>
			</tr>
			<tr><td colspan="4" style="height:1px;"></td></tr>
									<tr style="vertical-align:top">
				<td style="width:25px;border:1px solid black;background-color:#CF7C00"></td>
				<td style="width:5px;"></td>
				<td style="width:215px;">Favourite hangouts (2)</td>
				<td style="width:25px;" hidden="0" onclick="doMapTypeToggle(26,this)" class="a">hide</td>
			</tr>
			<tr><td colspan="4" style="height:1px;"></td></tr>
									<tr style="vertical-align:top">
				<td style="width:25px;border:1px solid black;background-color:#2D3DA1"></td>
				<td style="width:5px;"></td>
				<td style="width:215px;">Claimed territory (3)</td>
				<td style="width:25px;" hidden="0" onclick="doMapTypeToggle(27,this)" class="a">hide</td>
			</tr>
			<tr><td colspan="4" style="height:1px;"></td></tr>
						</table>
		<hr style="height:1px;color:#999" />
		<table>
			<tr><td colspan="2" ><b>Found species</b></td></tr>
									<tr style="vertical-align:top">
				<td style="width:245px;">Ursus arctos syriacus (12)</td>
				<td style="width:25px;" hidden="0" onclick="doMapTypeToggle(null,this,25)" class="a">hide</td>
			</tr>
			<tr><td colspan="2" style="height:1px;"></td></tr>
																																																																													<tr style="vertical-align:top">
				<td style="width:245px;">Ursus arctos gyas (11)</td>
				<td style="width:25px;" hidden="0" onclick="doMapTypeToggle(null,this,30)" class="a">hide</td>
			</tr>
			<tr><td colspan="2" style="height:1px;"></td></tr>
																																																																		
		</table>
	</div>

</div>


<script type="text/JavaScript">
$(document).ready(function(){


	initMap();
	initMapSearch();
		map.fitBounds(new google.maps.LatLngBounds(new google.maps.LatLng(40.054823907898765,  46.81396484375), new google.maps.LatLng(58.16047452736173,  69.786376953125)));
	

	var nodes0 = Array();
		nodes0[0] = [51.414522454145576,  51.16455078125];
		nodes0[1] = [50.07995144138613,  53.14208984375];
		nodes0[2] = [52.897206225976795,  53.40576171875];
		nodes0[3] = [52.55120604518616,  57.62451171875];
		nodes0[4] = [51.19473329964724,  55.42724609375];
		nodes0[5] = [50.5848759940676,  57.14111328125];
		nodes0[6] = [49.4555209703425,  59.51416015625];
		nodes0[7] = [46.18161809353259,  56.83349609375];
		nodes0[8] = [49.255150002138855,  54.59228515625];
		nodes0[9] = [47.563442995243,  53.44970703125];
		nodes0[10] = [48.03574563263533,  49.09912109375];
		nodes0[11] = [49.14028568238956,  51.42822265625];
		drawPolygon(nodes0,null,{
		name: 'Ursus arctos syriacus: Occurrence',
		addMarker: true,
		addDelete: false,
		occurrenceId: 792,
		taxonId: 25,
		colour:'FF03AB',
		typeId:24
	});


	placeMarker([49.1690267719,56.0424804688],{
		name: 'Ursus arctos syriacus: Occurrence',
		addMarker: true,
		addDelete: false,
		occurrenceId: 791,
		taxonId: 25,
		colour:'FF03AB',
		typeId:24
	});

	var nodes2 = Array();
		nodes2[0] = [55.35560311156944,  57.130126953125];
		nodes2[1] = [51.208500966341816,  58.712158203125];
		nodes2[2] = [51.59232669700684,  63.106689453125];
		nodes2[3] = [54.343653951483844,  63.370361328125];
		nodes2[4] = [53.82812064521295,  66.094970703125];
		nodes2[5] = [55.9506454856383,  65.831298828125];
		nodes2[6] = [57.01821957035777,  62.227783203125];
		nodes2[7] = [54.64990635434842,  60.030517578125];
		drawPolygon(nodes2,null,{
		name: 'Ursus arctos syriacus: Occurrence',
		addMarker: true,
		addDelete: false,
		occurrenceId: 819,
		taxonId: 25,
		colour:'FF03AB',
		typeId:24
	});


	placeMarker([43.4388407497,48.6376953125],{
		name: 'Ursus arctos syriacus: Occurrence',
		addMarker: true,
		addDelete: false,
		occurrenceId: 872,
		taxonId: 25,
		colour:'FF03AB',
		typeId:24
	});

	placeMarker([43.2950795468,50.3735351562],{
		name: 'Ursus arctos syriacus: Occurrence',
		addMarker: true,
		addDelete: false,
		occurrenceId: 873,
		taxonId: 25,
		colour:'FF03AB',
		typeId:24
	});

	placeMarker([42.6520207998,49.5166015625],{
		name: 'Ursus arctos syriacus: Occurrence',
		addMarker: true,
		addDelete: false,
		occurrenceId: 874,
		taxonId: 25,
		colour:'FF03AB',
		typeId:24
	});

	placeMarker([42.3117247582,50.9228515625],{
		name: 'Ursus arctos syriacus: Occurrence',
		addMarker: true,
		addDelete: false,
		occurrenceId: 875,
		taxonId: 25,
		colour:'FF03AB',
		typeId:24
	});

	placeMarker([49.2981553611,62.2277832031],{
		name: 'Ursus arctos syriacus: Occurrences before 1900',
		addMarker: true,
		addDelete: false,
		occurrenceId: 808,
		taxonId: 25,
		colour:'00DECF',
		typeId:25
	});

	var nodes8 = Array();
		nodes8[0] = [52.24283712977869,  64.952392578125];
		nodes8[1] = [50.543003801798854,  66.710205078125];
		nodes8[2] = [53.620110823918694,  67.677001953125];
		drawPolygon(nodes8,null,{
		name: 'Ursus arctos syriacus: Occurrences before 1900',
		addMarker: true,
		addDelete: false,
		occurrenceId: 818,
		taxonId: 25,
		colour:'00DECF',
		typeId:25
	});


	var nodes9 = Array();
		nodes9[0] = [44.49834696880879,  47.0556640625];
		nodes9[1] = [44.561003072966734,  50.6591796875];
		nodes9[2] = [45.646573222328655,  53.076171875];
		nodes9[3] = [47.07188034849015,  52.9443359375];
		nodes9[4] = [47.22132170008718,  51.0986328125];
		nodes9[5] = [46.53041100724528,  48.76953125];
		nodes9[6] = [46.10549891533475,  47.275390625];
		nodes9[7] = [46.10549891533475,  47.275390625];
		drawPolygon(nodes9,null,{
		name: 'Ursus arctos syriacus: Occurrences before 1900',
		addMarker: true,
		addDelete: false,
		occurrenceId: 871,
		taxonId: 25,
		colour:'00DECF',
		typeId:25
	});


	placeMarker([50.4311626719,47.1984863281],{
		name: 'Ursus arctos syriacus: Favourite hangouts',
		addMarker: true,
		addDelete: false,
		occurrenceId: 811,
		taxonId: 25,
		colour:'CF7C00',
		typeId:26
	});

	var nodes11 = Array();
		nodes11[0] = [54.29238861006309,  65.743408203125];
		nodes11[1] = [52.98989148406794,  63.721923828125];
		nodes11[2] = [52.564564532410365,  66.446533203125];
		nodes11[3] = [50.487116326098665,  67.589111328125];
		nodes11[4] = [53.983453408108794,  69.786376953125];
		nodes11[5] = [56.342332208159355,  69.171142578125];
		drawPolygon(nodes11,null,{
		name: 'Ursus arctos syriacus: Favourite hangouts',
		addMarker: true,
		addDelete: false,
		occurrenceId: 816,
		taxonId: 25,
		colour:'CF7C00',
		typeId:26
	});


	var nodes12 = Array();
		nodes12[0] = [48.85195715043376,  52.384033203125];
		nodes12[1] = [51.605975218700216,  57.921142578125];
		nodes12[2] = [55.81507979111646,  59.766845703125];
		nodes12[3] = [58.16047452736173,  61.085205078125];
		nodes12[4] = [56.83836669665407,  61.524658203125];
		nodes12[5] = [55.36809218759611,  60.821533203125];
		nodes12[6] = [52.95019354369011,  60.557861328125];
		nodes12[7] = [50.94621120203979,  60.557861328125];
		nodes12[8] = [48.967488841519504,  56.339111328125];
		drawPolygon(nodes12,null,{
		name: 'Ursus arctos gyas: Occurrence',
		addMarker: true,
		addDelete: false,
		occurrenceId: 867,
		taxonId: 30,
		colour:'FF03AB',
		typeId:24
	});


	var nodes13 = Array();
		nodes13[0] = [43.45479318373455,  47.78076171875];
		nodes13[1] = [44.30997426222822,  50.63720703125];
		nodes13[2] = [43.66179193713776,  52.548828125];
		nodes13[3] = [41.855119570471444,  54.06494140625];
		nodes13[4] = [40.78249647748179,  53.31787109375];
		nodes13[5] = [40.5992310643638,  49.05517578125];
		nodes13[6] = [41.428189111282016,  46.81396484375];
		drawPolygon(nodes13,null,{
		name: 'Ursus arctos gyas: Occurrence',
		addMarker: true,
		addDelete: false,
		occurrenceId: 881,
		taxonId: 30,
		colour:'FF03AB',
		typeId:24
	});


	placeMarker([52.4173973660,67.5891113281],{
		name: 'Ursus arctos gyas: Occurrences before 1900',
		addMarker: true,
		addDelete: false,
		occurrenceId: 856,
		taxonId: 30,
		colour:'00DECF',
		typeId:25
	});

	placeMarker([45.2302989658,49.3188476562],{
		name: 'Ursus arctos gyas: Occurrences before 1900',
		addMarker: true,
		addDelete: false,
		occurrenceId: 876,
		taxonId: 30,
		colour:'00DECF',
		typeId:25
	});

	placeMarker([45.8612102038,50.2856445313],{
		name: 'Ursus arctos gyas: Occurrences before 1900',
		addMarker: true,
		addDelete: false,
		occurrenceId: 877,
		taxonId: 30,
		colour:'00DECF',
		typeId:25
	});

	placeMarker([46.4850425243,51.2304687500],{
		name: 'Ursus arctos gyas: Occurrences before 1900',
		addMarker: true,
		addDelete: false,
		occurrenceId: 878,
		taxonId: 30,
		colour:'00DECF',
		typeId:25
	});

	placeMarker([45.7079823102,50.7031250000],{
		name: 'Ursus arctos gyas: Occurrences before 1900',
		addMarker: true,
		addDelete: false,
		occurrenceId: 879,
		taxonId: 30,
		colour:'00DECF',
		typeId:25
	});

	placeMarker([44.5610030730,48.5058593750],{
		name: 'Ursus arctos gyas: Occurrences before 1900',
		addMarker: true,
		addDelete: false,
		occurrenceId: 880,
		taxonId: 30,
		colour:'00DECF',
		typeId:25
	});

	placeMarker([48.3287557641,51.0656738281],{
		name: 'Ursus arctos gyas: Claimed territory',
		addMarker: true,
		addDelete: false,
		occurrenceId: 865,
		taxonId: 30,
		colour:'2D3DA1',
		typeId:27
	});

	placeMarker([51.4966721596,48.8684082031],{
		name: 'Ursus arctos gyas: Claimed territory',
		addMarker: true,
		addDelete: false,
		occurrenceId: 866,
		taxonId: 30,
		colour:'2D3DA1',
		typeId:27
	});

	var nodes22 = Array();
		nodes22[0] = [49.711942160547686,  61.964111328125];
		nodes22[1] = [47.80013102712821,  66.798095703125];
		nodes22[2] = [44.13676646492792,  68.116455078125];
		nodes22[3] = [42.27921910247984,  64.073486328125];
		nodes22[4] = [42.53879444730493,  60.645751953125];
		nodes22[5] = [45.13737650050882,  56.954345703125];
		nodes22[6] = [48.73615824842707,  58.536376953125];
		nodes22[7] = [47.26607218734441,  61.876220703125];
		drawPolygon(nodes22,null,{
		name: 'Ursus arctos gyas: Claimed territory',
		addMarker: true,
		addDelete: false,
		occurrenceId: 870,
		taxonId: 30,
		colour:'2D3DA1',
		typeId:27
	});


	setPredefPolygon('(53.15490735135709, 52.713623046875),(47.881244578587406, 49.769287109375),(42.72470052283929, 47.176513671875),(40.054823907898765, 59.525146484375),(43.653843596927096, 68.050537109375),(49.76164539260263, 68.489990234375),(52.35705061656906, 65.853271484375),(53.23389047985355, 60.447998046875)');


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
addRequestVar('coordinates','(53.15490735135709, 52.713623046875),(47.881244578587406, 49.769287109375),(42.72470052283929, 47.176513671875),(40.054823907898765, 59.525146484375),(43.653843596927096, 68.050537109375),(49.76164539260263, 68.489990234375),(52.35705061656906, 65.853271484375),(53.23389047985355, 60.447998046875)')

})

</script>

</body>
</html>
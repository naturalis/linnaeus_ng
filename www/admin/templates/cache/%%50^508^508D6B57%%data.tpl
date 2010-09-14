245
a:4:{s:8:"template";a:4:{s:8:"data.tpl";b:1;s:26:"../shared/admin-header.tpl";b:1;s:28:"../shared/admin-messages.tpl";b:1;s:26:"../shared/admin-footer.tpl";b:1;}s:9:"timestamp";i:1284370550;s:7:"expires";i:1284374150;s:13:"cache_serials";a:0:{}}<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

	<title>Imaginary Beings - Project data</title>

	<link href="/admin/images/system/favicon.ico" rel="shortcut icon" type="image/x-icon" />
	<link href="/admin/images/system/favicon.ico" rel="icon" type="image/x-icon" />

	<style type="text/css" media="all">
		@import url("/admin/style/main.css");
		@import url("/admin/style/admin-inputs.css");
		@import url("/admin/style/admin-help.css");
		@import url("/admin/style/admin.css");
	</style>

	<script type="text/javascript" src="/admin/javascript/jquery-1.4.2.min.js"></script>
	<script type="text/javascript" src="/admin/javascript/main.js"></script>

</head>

<body><div id="body-container">
<div id="header-container">
	<a href="/admin/admin-index.php"><img src="/admin/images/system/linnaeus_logo.png" id="lng-logo" />
	<img src="/admin/images/system/eti_logo.png" id="eti-logo" /></a>
</div>
<div id="page-container">

<div id="page-header-titles">
	<span id="page-header-title">Linnaeus NG Administration v0.1</span>
	<br />
	<div id="breadcrumbs">
				<span class="crumb"><a href="/admin/views/users/choose_project.php">Projects</a></span>
		<span class="crumb-arrow">&rarr;</span>
					<span class="crumb"><a href="/admin/admin-index.php">Imaginary Beings</a></span>
		<span class="crumb-arrow">&rarr;</span>
					<span class="crumb"><a href="/admin/views/projects">Project administration</a></span>
		<span class="crumb-arrow">&rarr;</span>
					<span id="crumb-current">Project data</span>
		<span class="crumb-arrow">&nbsp;</span>
			</div>
</div>




<div id="page-main">
<form enctype="multipart/form-data" action="" method="POST">
<table>
	<tr>
		<td>
			Internal project name:
		</td>
		<td colspan="2">
			Imaginary Beings
		</td>
	</tr>
	<tr>
		<td>
			Internal project description:
		</td>
		<td colspan="2">
			Borges bestiarium
		</td>
	</tr>
	<tr>
		<td>
			Project title:
		</td>
		<td colspan="2">
			<input type="text" name="title" value="Imaginary Beings Of The Literary World" style="width:300px;" />
		</td>
	</tr>
	<tr style="vertical-align:top">
		<td>
			Project logo:
		</td>
		<td colspan="2">
				<img src="/admin/images/project/0002/project_logo.png" width="150px" /><br />
		<label><input type="checkbox" value="1" name="deleteLogo" />Delete current logo (uploading a new logo deletes the old one as well)</label><br />
				<input type="hidden" name="MAX_FILE_SIZE" value="10000000" />
		<input name="uploadedfile" type="file" /><br />
		</td>
	</tr>
	<tr style="vertical-align:top">
		<td>
			Project languages:
		</td>
		<td>
			<select name="language-select" id="language-select">
														<option 
					value="26"
					class="language-select-item-active"
					>English</option>
											<option 
					value="24"
					class="language-select-item-active"
					>Dutch</option>
											<option 
					value="99"
					class="language-select-item"
					>Spanish</option>
											<option 
					value="36"
					class="language-select-item"
					>German</option>
											<option 
					value="31"
					class="language-select-item"
					>French</option>
											
				<option disabled="disabled" class="language-select-item-disabled">
					&mdash;&mdash;&mdash;&mdash;&mdash;&mdash;&mdash;&mdash;&mdash;&mdash;&mdash;&mdash;&mdash;&mdash;
				</option>				<option 
					value="1"
					class="language-select-item"
					>Abkhaz</option>
											<option 
					value="2"
					class="language-select-item"
					>Afrikaans</option>
											<option 
					value="3"
					class="language-select-item"
					>Albanian</option>
											<option 
					value="4"
					class="language-select-item"
					>Amharic</option>
											<option 
					value="5"
					class="language-select-item"
					>Arabic</option>
											<option 
					value="7"
					class="language-select-item"
					>Armenian</option>
											<option 
					value="8"
					class="language-select-item"
					>Assamese</option>
											<option 
					value="6"
					class="language-select-item"
					>Assyrian/Syriac</option>
											<option 
					value="9"
					class="language-select-item"
					>Aymara</option>
											<option 
					value="10"
					class="language-select-item"
					>Azeri</option>
											<option 
					value="11"
					class="language-select-item"
					>Basque</option>
											<option 
					value="12"
					class="language-select-item"
					>Belarusian</option>
											<option 
					value="13"
					class="language-select-item"
					>Bengali</option>
											<option 
					value="14"
					class="language-select-item"
					>Bislama</option>
											<option 
					value="15"
					class="language-select-item"
					>Bosnian</option>
											<option 
					value="16"
					class="language-select-item"
					>Bulgarian</option>
											<option 
					value="17"
					class="language-select-item"
					>Burmese</option>
											<option 
					value="18"
					class="language-select-item"
					>Catalan</option>
											<option 
					value="19"
					class="language-select-item"
					>Chinese</option>
											<option 
					value="20"
					class="language-select-item"
					>Croatian</option>
											<option 
					value="21"
					class="language-select-item"
					>Czech</option>
											<option 
					value="22"
					class="language-select-item"
					>Danish</option>
											<option 
					value="23"
					class="language-select-item"
					>Dhivehi</option>
											<option 
					value="25"
					class="language-select-item"
					>Dzongkha</option>
											<option 
					value="27"
					class="language-select-item"
					>Estonian</option>
											<option 
					value="28"
					class="language-select-item"
					>Fijian</option>
											<option 
					value="29"
					class="language-select-item"
					>Filipino</option>
											<option 
					value="30"
					class="language-select-item"
					>Finnish</option>
											<option 
					value="32"
					class="language-select-item"
					>Frisian</option>
											<option 
					value="33"
					class="language-select-item"
					>Gagauz</option>
											<option 
					value="34"
					class="language-select-item"
					>Galician</option>
											<option 
					value="35"
					class="language-select-item"
					>Georgian</option>
											<option 
					value="37"
					class="language-select-item"
					>Greek</option>
											<option 
					value="38"
					class="language-select-item"
					>Gujarati</option>
											<option 
					value="39"
					class="language-select-item"
					>Haitian Creole</option>
											<option 
					value="40"
					class="language-select-item"
					>Hebrew</option>
											<option 
					value="41"
					class="language-select-item"
					>Hindi</option>
											<option 
					value="42"
					class="language-select-item"
					>Hiri Motu</option>
											<option 
					value="43"
					class="language-select-item"
					>Hungarian</option>
											<option 
					value="44"
					class="language-select-item"
					>Icelandic</option>
											<option 
					value="45"
					class="language-select-item"
					>Indonesian</option>
											<option 
					value="46"
					class="language-select-item"
					>Inuinnaqtun</option>
											<option 
					value="47"
					class="language-select-item"
					>Inuktitut</option>
											<option 
					value="48"
					class="language-select-item"
					>Irish</option>
											<option 
					value="49"
					class="language-select-item"
					>Italian</option>
											<option 
					value="50"
					class="language-select-item-active"
					>Japanese</option>
											<option 
					value="51"
					class="language-select-item"
					>Kannada</option>
											<option 
					value="52"
					class="language-select-item"
					>Kashmiri</option>
											<option 
					value="53"
					class="language-select-item"
					>Kazakh</option>
											<option 
					value="54"
					class="language-select-item"
					>Khmer</option>
											<option 
					value="55"
					class="language-select-item"
					>Korean</option>
											<option 
					value="56"
					class="language-select-item"
					>Kurdish</option>
											<option 
					value="57"
					class="language-select-item"
					>Kyrgyz</option>
											<option 
					value="58"
					class="language-select-item"
					>Lao</option>
											<option 
					value="59"
					class="language-select-item"
					>Latin</option>
											<option 
					value="60"
					class="language-select-item"
					>Latvian</option>
											<option 
					value="61"
					class="language-select-item"
					>Lithuanian</option>
											<option 
					value="62"
					class="language-select-item"
					>Luxembourgish</option>
											<option 
					value="69"
					class="language-select-item"
					>Ma-ori</option>
											<option 
					value="63"
					class="language-select-item"
					>Macedonian</option>
											<option 
					value="64"
					class="language-select-item"
					>Malagasy</option>
											<option 
					value="65"
					class="language-select-item"
					>Malay</option>
											<option 
					value="66"
					class="language-select-item"
					>Malayalam</option>
											<option 
					value="67"
					class="language-select-item"
					>Maltese</option>
											<option 
					value="68"
					class="language-select-item"
					>Manx Gaelic</option>
											<option 
					value="70"
					class="language-select-item"
					>Marathi</option>
											<option 
					value="71"
					class="language-select-item"
					>Mayan</option>
											<option 
					value="72"
					class="language-select-item"
					>Moldovan</option>
											<option 
					value="73"
					class="language-select-item"
					>Mongolian</option>
											<option 
					value="74"
					class="language-select-item"
					>Ndebele</option>
											<option 
					value="75"
					class="language-select-item"
					>Nepali</option>
											<option 
					value="76"
					class="language-select-item"
					>Northern Sotho</option>
											<option 
					value="77"
					class="language-select-item"
					>Norwegian</option>
											<option 
					value="78"
					class="language-select-item"
					>Occitan</option>
											<option 
					value="79"
					class="language-select-item"
					>Oriya</option>
											<option 
					value="80"
					class="language-select-item"
					>Ossetian</option>
											<option 
					value="81"
					class="language-select-item"
					>Papiamento</option>
											<option 
					value="82"
					class="language-select-item"
					>Pashto</option>
											<option 
					value="83"
					class="language-select-item"
					>Persian</option>
											<option 
					value="84"
					class="language-select-item"
					>Polish</option>
											<option 
					value="85"
					class="language-select-item"
					>Portuguese</option>
											<option 
					value="86"
					class="language-select-item"
					>Punjabi</option>
											<option 
					value="87"
					class="language-select-item"
					>Quechua</option>
											<option 
					value="88"
					class="language-select-item"
					>Rhaeto-Romansh</option>
											<option 
					value="89"
					class="language-select-item"
					>Russian</option>
											<option 
					value="90"
					class="language-select-item"
					>Sanskrit</option>
											<option 
					value="91"
					class="language-select-item"
					>Serbian</option>
											<option 
					value="92"
					class="language-select-item"
					>Shona</option>
											<option 
					value="93"
					class="language-select-item"
					>Sindhi</option>
											<option 
					value="94"
					class="language-select-item"
					>Sinhala</option>
											<option 
					value="95"
					class="language-select-item"
					>Slovak</option>
											<option 
					value="96"
					class="language-select-item"
					>Slovene</option>
											<option 
					value="97"
					class="language-select-item"
					>Somali</option>
											<option 
					value="98"
					class="language-select-item"
					>Sotho</option>
											<option 
					value="100"
					class="language-select-item"
					>Sranan Tongo</option>
											<option 
					value="101"
					class="language-select-item"
					>Swahili</option>
											<option 
					value="102"
					class="language-select-item"
					>Swati</option>
											<option 
					value="103"
					class="language-select-item"
					>Swedish</option>
											<option 
					value="104"
					class="language-select-item"
					>Tajik</option>
											<option 
					value="105"
					class="language-select-item"
					>Tamil</option>
											<option 
					value="106"
					class="language-select-item"
					>Telugu</option>
											<option 
					value="107"
					class="language-select-item"
					>Tetum</option>
											<option 
					value="108"
					class="language-select-item"
					>Thai</option>
											<option 
					value="109"
					class="language-select-item"
					>Tok Pisin</option>
											<option 
					value="110"
					class="language-select-item"
					>Tsonga</option>
											<option 
					value="111"
					class="language-select-item"
					>Tswana</option>
											<option 
					value="112"
					class="language-select-item"
					>Turkish</option>
											<option 
					value="113"
					class="language-select-item"
					>Turkmen</option>
											<option 
					value="114"
					class="language-select-item"
					>Ukrainian</option>
											<option 
					value="115"
					class="language-select-item"
					>Urdu</option>
											<option 
					value="116"
					class="language-select-item"
					>Uzbek</option>
											<option 
					value="117"
					class="language-select-item"
					>Venda</option>
											<option 
					value="118"
					class="language-select-item"
					>Vietnamese</option>
											<option 
					value="119"
					class="language-select-item"
					>Welsh</option>
											<option 
					value="120"
					class="language-select-item"
					>Xhosa</option>
											<option 
					value="121"
					class="language-select-item"
					>Yiddish</option>
											<option 
					value="122"
					class="language-select-item"
					>Zulu</option>
						</select>
		</td>
	</tr>
	<tr>
		<td></td>
		<td style="text-align:center">
			<img 
				src="/admin/images/system/icons/arrow-270.png" 
				onclick="projectSaveLanguage('add',[$('#language-select :selected').val(),$('#language-select :selected').text()])" 
				class="general-clickable-image" 
				title="add language"
			/>
		</td>
	</td>
	<tr>
		<td></td>
		<td>
		<!-- u>Language(s) currently in use</u><br / -->
		<span id="language-list">	
		</span>
		</td>
	</tr>		
</table>
<input type="submit" value="save" />
</form>

<br />
The "welcome" and "contributors" texts will be added once the html-editor is in place.
</div>




<script type="text/JavaScript">
$(document).ready(function(){

		projectAddLanguage([26,'English',6,0,1])
			projectAddLanguage([24,'Dutch',8,0,0])
																																																			projectAddLanguage([50,'Japanese',18,1,1])
																																																																									projectUpdateLanguageBlock();

});
</script>



</div ends="page-container">

<div id="footer-container">
	<div id="footer-menu">
		<a href="/admin/views/users/logout.php">Log out (logged in as Jorge Luis Borges)</a>
		<br />
	</div>
</div ends="footer-container">

</div ends="body-container"></body>
</html>
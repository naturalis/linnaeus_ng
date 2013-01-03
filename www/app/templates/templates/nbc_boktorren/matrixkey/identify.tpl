{include file="../shared/header.tpl"}
{literal}
<script>
function nbcToggleGroup(id) {

	if ($('#character-group-'+id).css('display')=='none') {
		$('#character-group-'+id).removeClass('hidden').addClass('visible');
		$('#character-item-'+id).removeClass('closed').addClass('open');
	} else {
		$('#character-group-'+id).removeClass('visible').addClass('hidden');
		$('#character-item-'+id).removeClass('open').addClass('closed');
	}
	
} 

var nbcCharacters = Array();

function nbcAddCharacter(c) {

	nbcCharacters[c.id] = c;
	
}

function nbcShowStates(id) {

	var c = nbcCharacters[id];

	if (c) {

		allAjaxHandle = $.ajax({
			url : 'ajax_interface.php',
			type: 'POST',
			data : ({
				action : 'get_formatted_states' ,
				id : id , 
				time : getTimestamp()
			}),
			success : function (data) {
				//alert(data);
				showDialog(c.label,data);

				$('#dialog').css('width', (c.type=='media' ? 610 : 425));
				$.modaldialog.reinitPosition({top:175});

			}
		})

	}

}
</script>
<style>
.hidden {
	display: none;	
}
.visible {
	display: block;	
}

#dialog-content, #dialog-content-inner {
	background-color: #fff;
}

#dialog-content-inner-inner {
	max-height:300px;
	overflow-y:auto;
}
#state-header {
	font-weight: bold;
}
.state-image-cell {
	margin: 15px 15px 0px 15px;
}
.state-image-cell:hover {
	color: #f00;
}
.state-image {
	border:2px solid #eee;
}
.state-image:hover {
	border:2px solid #666;
	cursor: pointer;
}
</style>
{/literal}

		<div id="content">
			<div>
				<div id="facets">
					<ul class="facetCategories">

{foreach from=$groups item=v}
		<li id="character-item-{$v.id}" class="closed"><a href="#" onclick="nbcToggleGroup({$v.id})">{$v.label}</a>
		<ul id="character-group-{$v.id}" class="facets hidden">
		{foreach from=$v.chars item=c}
		{assign var=foo value="|"|explode:$c.label}{if $foo[0] && $foo[1]}{assign var=cLabel value=$foo[0]}{assign var=cText value=$foo[1]}{else}{assign var=cLabel value=$c.label}{assign var=cText value=''}{/if}
		<li><a class="facetLink" href="#" onclick="nbcShowStates({$c.id})">{$cLabel}</a>
			<!-- span>
				<div class="facetValueHolder">(value 1)<a href="#" class="removeBtn">(deselecteer)</a></div>
				<div class="facetValueHolder">(value 2)<a href="#" class="removeBtn">(deselecteer)</a></div>
			</span -->
		
		</li>
		{/foreach}
		</ul></li>
{/foreach}					

					<ul class="facetCategories clearSelectionBtn">
						<li class="closed"><span><a href="?set=Boktorren van NL&amp;lookup=Boktorren">wis geselecteerde eigenschappen</a></span></li>
					</ul>
					<ul class="facetCategories sourceContainer">
						<li class="closed">
							<p><strong>Gebaseerd op</strong></p>

<p>Zeegers, Th. &amp; Th. Heijerman 2008. De Nederlandse boktorren
(Cerambycidae). (<a href="http://www.naturalis.nl/ET2"
target="_blank">Meer info</a>)</p>
</li></ul></div><div id="results" xmlns:opensearch="http://a9.com/-/spec/opensearch/1.1/"><div id="resultsHeader"><span><div class="headerSelectionLabel"><strong style="color:#333">143</strong> (van <strong style="color:#777;">143</strong>) objecten in huidige selectie
			</div><div class="headerPagination"><ul class="list paging"><li><strong>1</strong></li><li><a href="?&amp;set=Boktorren van NL&amp;&amp;lookup=Boktorren&amp;startPage=2">2</a></li><li><a href="?&amp;set=Boktorren van NL&amp;&amp;lookup=Boktorren&amp;startPage=3">3</a></li><li><a href="?&amp;set=Boktorren van NL&amp;&amp;lookup=Boktorren&amp;startPage=4">4</a></li><li><a href="?&amp;set=Boktorren van NL&amp;&amp;lookup=Boktorren&amp;startPage=5">5</a></li><li><span>...</span></li><li><a href="?&amp;set=Boktorren van NL&amp;&amp;lookup=Boktorren&amp;startPage=9">9</a></li><li class="next"><a href="?&amp;set=Boktorren van NL&amp;&amp;lookup=Boktorren&amp;startPage=2">&gt;&gt;</a></li></ul></div></span></div><div class="result"><div class="resultImageHolder"><a href="http://www.naturalisbeeldbibliotheek.nl/beeldbank/comping/132734.jpg" onclick="Lightbox.showBoxImage(this.href);return false;"><img class="result" height="207" width="145" src="http://www.naturalisbeeldbibliotheek.nl/beeldbank/comping/132734.jpg" title="Foto Theodoor Heijerman" /></a></div><div><span class="scientificName">Aegomorphus clavipes</span></div><img class="gender" height="17" width="8" src="/images/female.png" title="vrouwelijk" /><a class="similarBtn" href="/boktorren-home/boktorren-facetdeterminatie.aspx?&amp;set=Boktorren van NL&amp;facetGroup=&amp;lookup=Boktorren&amp;similar=http://www.rnaproject.org/data/7756d4ff-563b-4d19-b133-efa42c9ef880&amp;similar=http://www.rnaproject.org/data/7f0e2b4c-3a21-4eeb-97e1-5b1a37784406&amp;similarBaseObj_title=Aegomorphus clavipes&amp;similarBaseObj_NSRUrl=&amp;similarBaseObj_imageSource=http://www.naturalisbeeldbibliotheek.nl/beeldbank/comping/132734.jpg" target="_self">gelijkende soorten</a></div><div class="result"><div class="resultImageHolder"><a href="http://www.naturalisbeeldbibliotheek.nl/beeldbank/comping/132736.jpg" onclick="Lightbox.showBoxImage(this.href);return false;"><img class="result" height="207" width="145" src="http://www.naturalisbeeldbibliotheek.nl/beeldbank/comping/132736.jpg" title="Foto Theodoor Heijerman" /></a></div><div><span class="scientificName">Agapanthia cardui</span></div><img class="gender" height="17" width="8" src="/images/male.png" title="mannelijk" /><a class="similarBtn" href="/boktorren-home/boktorren-facetdeterminatie.aspx?&amp;set=Boktorren van NL&amp;facetGroup=&amp;lookup=Boktorren&amp;similar=http://www.rnaproject.org/data/11d36b0f-a74b-4f08-8723-ff96bdd6cfd5&amp;similar=http://www.rnaproject.org/data/92561bc8-669d-458e-814b-bfcb22a2fe85&amp;similar=http://www.rnaproject.org/data/d12027c5-b451-4a3e-994c-92ab266dbc79&amp;similarBaseObj_title=Agapanthia cardui&amp;similarBaseObj_NSRUrl=&amp;similarBaseObj_imageSource=http://www.naturalisbeeldbibliotheek.nl/beeldbank/comping/132736.jpg" target="_self">gelijkende soorten</a></div><div class="result"><div class="resultImageHolder"><a href="http://www.naturalisbeeldbibliotheek.nl/beeldbank/comping/132746.jpg" onclick="Lightbox.showBoxImage(this.href);return false;"><img class="result" height="207" width="145" src="http://www.naturalisbeeldbibliotheek.nl/beeldbank/comping/132746.jpg" title="Foto Theodoor Heijerman" /></a></div><div><span class="scientificName">Anoplodera rufipes</span></div><img class="gender" height="17" width="8" src="/images/female.png" title="vrouwelijk" /></div><div class="result"><div class="resultImageHolder"><a href="http://www.naturalisbeeldbibliotheek.nl/beeldbank/comping/132737.jpg" onclick="Lightbox.showBoxImage(this.href);return false;"><img class="result" height="207" width="145" src="http://www.naturalisbeeldbibliotheek.nl/beeldbank/comping/132737.jpg" title="Foto Theodoor Heijerman" /></a></div><div><a href="http://www.nederlandsesoorten.nl/get?site=nlsr&amp;view=nlsr&amp;page_alias=conceptcard&amp;cid=0AHCYSI00364" target="_blanc">Beemdkroonboktor<br /><span class="scientificName">Agapanthia intermedia</span></a></div><img class="gender" height="17" width="8" src="/images/female.png" title="vrouwelijk" /><a class="similarBtn" href="/boktorren-home/boktorren-facetdeterminatie.aspx?&amp;set=Boktorren van NL&amp;facetGroup=&amp;lookup=Boktorren&amp;similar=http://www.rnaproject.org/data/9d5fad1f-6325-498f-8ac7-efcacd46fe65&amp;similar=http://www.rnaproject.org/data/4fa9f65a-0d26-4534-af49-125e23f20d4f&amp;similar=http://www.rnaproject.org/data/6a599a2e-df27-44c0-9bdf-cdf129639d0b&amp;similar=http://www.rnaproject.org/data/5f7604ba-633b-46b6-8cbe-82b5f90db64b&amp;similarBaseObj_title=Beemdkroonboktor&amp;similarBaseObj_NSRUrl=http://www.nederlandsesoorten.nl/get?site=nlsr&amp;view=nlsr&amp;page_alias=conceptcard&amp;cid=0AHCYSI00364&amp;similarBaseObj_imageSource=http://www.naturalisbeeldbibliotheek.nl/beeldbank/comping/132737.jpg" target="_self">gelijkende soorten</a></div><div class="result"><div class="resultImageHolder"><a href="http://www.naturalisbeeldbibliotheek.nl/beeldbank/comping/132806.jpg" onclick="Lightbox.showBoxImage(this.href);return false;"><img class="result" height="207" width="145" src="http://www.naturalisbeeldbibliotheek.nl/beeldbank/comping/132806.jpg" title="Foto Theodoor Heijerman" /></a></div><div><a href="http://www.nederlandsesoorten.nl/get?site=nlsr&amp;view=nlsr&amp;page_alias=conceptcard&amp;cid=0AHCYFBRRZWL" target="_blanc">Behaarde borstelboktor<br /><span class="scientificName">Pogonocherus decoratus</span></a></div><img class="gender" height="17" width="8" src="/images/female.png" title="vrouwelijk" /><a class="similarBtn" href="/boktorren-home/boktorren-facetdeterminatie.aspx?&amp;set=Boktorren van NL&amp;facetGroup=&amp;lookup=Boktorren&amp;similar=http://www.rnaproject.org/data/e154f22f-bac4-4306-a98d-fac944283141&amp;similar=http://www.rnaproject.org/data/36d4c982-bf54-44b0-a232-41808213b88b&amp;similar=http://www.rnaproject.org/data/e8dba1e4-5a1b-4c7f-88bd-a9bb4e07e61a&amp;similar=http://www.rnaproject.org/data/12b1e17b-14a0-4ef5-a561-9218fcf81af7&amp;similar=http://www.rnaproject.org/data/6f217837-8f35-4a96-9864-7ded76b79f85&amp;similarBaseObj_title=Behaarde borstelboktor&amp;similarBaseObj_NSRUrl=http://www.nederlandsesoorten.nl/get?site=nlsr&amp;view=nlsr&amp;page_alias=conceptcard&amp;cid=0AHCYFBRRZWL&amp;similarBaseObj_imageSource=http://www.naturalisbeeldbibliotheek.nl/beeldbank/comping/132806.jpg" target="_self">gelijkende soorten</a></div><div class="result"><div class="resultImageHolder"><a href="http://www.naturalisbeeldbibliotheek.nl/beeldbank/comping/132744.jpg" onclick="Lightbox.showBoxImage(this.href);return false;"><img class="result" height="207" width="145" src="http://www.naturalisbeeldbibliotheek.nl/beeldbank/comping/132744.jpg" title="Foto Theodoor Heijerman" /></a></div><div><a href="http://www.nederlandsesoorten.nl/get?site=nlsr&amp;view=nlsr&amp;page_alias=conceptcard&amp;cid=0AHCYFBRPJDD" target="_blanc">Bloedrode smalboktor<br /><span class="scientificName">Corymbia sanguinolenta</span></a></div><img class="gender" height="17" width="8" src="/images/male.png" title="mannelijk" /><a class="similarBtn" href="/boktorren-home/boktorren-facetdeterminatie.aspx?&amp;set=Boktorren van NL&amp;facetGroup=&amp;lookup=Boktorren&amp;similar=http://www.rnaproject.org/data/fce04ab5-ca75-4361-9b55-61fb29153d15&amp;similar=http://www.rnaproject.org/data/ad9959ee-2c0f-42b7-957d-bf610422eedc&amp;similar=http://www.rnaproject.org/data/9218b2ae-efd9-42a6-89b8-7d9a47c79693&amp;similar=http://www.rnaproject.org/data/262df01b-f31f-4b2c-adc3-5928dcf65856&amp;similar=http://www.rnaproject.org/data/906b8edb-c94c-4357-9b22-d54e75ec7f98&amp;similar=http://www.rnaproject.org/data/c3771592-b750-4915-a9e7-c2d837649ac4&amp;similar=http://www.rnaproject.org/data/ff6dd276-05e3-4404-bae2-ee5df8e771bf&amp;similar=http://www.rnaproject.org/data/4cbec2d0-05c8-44ba-aea4-8fa3cd2c0f6b&amp;similar=http://www.rnaproject.org/data/993d03fc-f17b-49ff-be1f-b31ed4532695&amp;similar=http://www.rnaproject.org/data/ae229f16-37eb-45e9-bb2a-561c5fd006f9&amp;similar=http://www.rnaproject.org/data/5e0dc799-aa47-4356-b9a3-777e04284024&amp;similar=http://www.rnaproject.org/data/4cbec2d0-05c8-44ba-aea4-8fa3cd2c0f6b&amp;similarBaseObj_title=Bloedrode smalboktor&amp;similarBaseObj_NSRUrl=http://www.nederlandsesoorten.nl/get?site=nlsr&amp;view=nlsr&amp;page_alias=conceptcard&amp;cid=0AHCYFBRPJDD&amp;similarBaseObj_imageSource=http://www.naturalisbeeldbibliotheek.nl/beeldbank/comping/132744.jpg" target="_self">gelijkende soorten</a></div><div class="result"><div class="resultImageHolder"><a href="http://www.naturalisbeeldbibliotheek.nl/beeldbank/comping/132743.jpg" onclick="Lightbox.showBoxImage(this.href);return false;"><img class="result" height="207" width="145" src="http://www.naturalisbeeldbibliotheek.nl/beeldbank/comping/132743.jpg" title="Foto Theodoor Heijerman" /></a></div><div><a href="http://www.nederlandsesoorten.nl/get?site=nlsr&amp;view=nlsr&amp;page_alias=conceptcard&amp;cid=0AHCYFBRPJDD" target="_blanc">Bloedrode smalboktor<br /><span class="scientificName">Corymbia sanguinolenta</span></a></div><img class="gender" height="17" width="8" src="/images/female.png" title="vrouwelijk" /><a class="similarBtn" href="/boktorren-home/boktorren-facetdeterminatie.aspx?&amp;set=Boktorren van NL&amp;facetGroup=&amp;lookup=Boktorren&amp;similar=http://www.rnaproject.org/data/fce04ab5-ca75-4361-9b55-61fb29153d15&amp;similar=http://www.rnaproject.org/data/ad9959ee-2c0f-42b7-957d-bf610422eedc&amp;similar=http://www.rnaproject.org/data/9218b2ae-efd9-42a6-89b8-7d9a47c79693&amp;similar=http://www.rnaproject.org/data/262df01b-f31f-4b2c-adc3-5928dcf65856&amp;similar=http://www.rnaproject.org/data/906b8edb-c94c-4357-9b22-d54e75ec7f98&amp;similar=http://www.rnaproject.org/data/c3771592-b750-4915-a9e7-c2d837649ac4&amp;similar=http://www.rnaproject.org/data/ff6dd276-05e3-4404-bae2-ee5df8e771bf&amp;similar=http://www.rnaproject.org/data/4cbec2d0-05c8-44ba-aea4-8fa3cd2c0f6b&amp;similar=http://www.rnaproject.org/data/993d03fc-f17b-49ff-be1f-b31ed4532695&amp;similar=http://www.rnaproject.org/data/ae229f16-37eb-45e9-bb2a-561c5fd006f9&amp;similar=http://www.rnaproject.org/data/5e0dc799-aa47-4356-b9a3-777e04284024&amp;similar=http://www.rnaproject.org/data/993d03fc-f17b-49ff-be1f-b31ed4532695&amp;similarBaseObj_title=Bloedrode smalboktor&amp;similarBaseObj_NSRUrl=http://www.nederlandsesoorten.nl/get?site=nlsr&amp;view=nlsr&amp;page_alias=conceptcard&amp;cid=0AHCYFBRPJDD&amp;similarBaseObj_imageSource=http://www.naturalisbeeldbibliotheek.nl/beeldbank/comping/132743.jpg" target="_self">gelijkende soorten</a></div><div class="result"><div class="resultImageHolder"><a href="http://www.naturalisbeeldbibliotheek.nl/beeldbank/comping/132808.jpg" onclick="Lightbox.showBoxImage(this.href);return false;"><img class="result" height="207" width="145" src="http://www.naturalisbeeldbibliotheek.nl/beeldbank/comping/132808.jpg" title="Foto Theodoor Heijerman" /></a></div><div><a href="http://www.nederlandsesoorten.nl/get?site=nlsr&amp;view=nlsr&amp;page_alias=conceptcard&amp;cid=0AHCYFBRSBYH" target="_blanc">Bonte borstelboktor<br /><span class="scientificName">Pogonocherus hispidulus</span></a></div><img class="gender" height="17" width="8" src="/images/female.png" title="vrouwelijk" /><a class="similarBtn" href="/boktorren-home/boktorren-facetdeterminatie.aspx?&amp;set=Boktorren van NL&amp;facetGroup=&amp;lookup=Boktorren&amp;similar=http://www.rnaproject.org/data/36d4c982-bf54-44b0-a232-41808213b88b&amp;similar=http://www.rnaproject.org/data/e8dba1e4-5a1b-4c7f-88bd-a9bb4e07e61a&amp;similar=http://www.rnaproject.org/data/6f217837-8f35-4a96-9864-7ded76b79f85&amp;similar=http://www.rnaproject.org/data/12b1e17b-14a0-4ef5-a561-9218fcf81af7&amp;similar=http://www.rnaproject.org/data/e154f22f-bac4-4306-a98d-fac944283141&amp;similarBaseObj_title=Bonte borstelboktor&amp;similarBaseObj_NSRUrl=http://www.nederlandsesoorten.nl/get?site=nlsr&amp;view=nlsr&amp;page_alias=conceptcard&amp;cid=0AHCYFBRSBYH&amp;similarBaseObj_imageSource=http://www.naturalisbeeldbibliotheek.nl/beeldbank/comping/132808.jpg" target="_self">gelijkende soorten</a></div><div class="result"><div class="resultImageHolder"><a href="http://www.naturalisbeeldbibliotheek.nl/beeldbank/comping/132816.jpg" onclick="Lightbox.showBoxImage(this.href);return false;"><img class="result" height="207" width="145" src="http://www.naturalisbeeldbibliotheek.nl/beeldbank/comping/132816.jpg" title="Foto Theodoor Heijerman" /></a></div><div><a href="http://www.nederlandsesoorten.nl/get?site=nlsr&amp;view=nlsr&amp;page_alias=conceptcard&amp;cid=623375560" target="_blanc">Bonte ribbelboktor<br /><span class="scientificName">Rhagium bifasciatum</span></a></div><img class="gender" height="17" width="8" src="/images/male.png" title="mannelijk" /><a class="similarBtn" href="/boktorren-home/boktorren-facetdeterminatie.aspx?&amp;set=Boktorren van NL&amp;facetGroup=&amp;lookup=Boktorren&amp;similar=http://www.rnaproject.org/data/11e0d442-7b36-49a4-8be9-1e5201d539a5&amp;similar=http://www.rnaproject.org/data/21744bbb-41fc-4a52-89dc-b5edd7aaba6c&amp;similar=http://www.rnaproject.org/data/9dd095ad-eaa8-4e9b-b1d5-2bd23a11116b&amp;similar=http://www.rnaproject.org/data/407f3953-7ccd-4edb-9f36-c6f189995cdb&amp;similar=http://www.rnaproject.org/data/d3c63eea-d5ec-407b-b3dd-ea7cccb7b4a6&amp;similar=http://www.rnaproject.org/data/dc7114c8-2422-4f03-bb2b-1b236a91e6fb&amp;similarBaseObj_title=Bonte ribbelboktor&amp;similarBaseObj_NSRUrl=http://www.nederlandsesoorten.nl/get?site=nlsr&amp;view=nlsr&amp;page_alias=conceptcard&amp;cid=623375560&amp;similarBaseObj_imageSource=http://www.naturalisbeeldbibliotheek.nl/beeldbank/comping/132816.jpg" target="_self">gelijkende soorten</a></div><div class="result"><div class="resultImageHolder"><a href="http://www.naturalisbeeldbibliotheek.nl/beeldbank/comping/132749.jpg" onclick="Lightbox.showBoxImage(this.href);return false;"><img class="result" height="207" width="145" src="http://www.naturalisbeeldbibliotheek.nl/beeldbank/comping/132749.jpg" title="Foto Theodoor Heijerman" /></a></div><div><a href="http://www.nederlandsesoorten.nl/get?site=nlsr&amp;view=nlsr&amp;page_alias=conceptcard&amp;cid=0AHCYFBRPNBN" target="_blanc">Bruine grootoogboktor<br /><span class="scientificName">Arhopalus rusticus</span></a></div><img class="gender" height="17" width="8" src="/images/female.png" title="vrouwelijk" /><a class="similarBtn" href="/boktorren-home/boktorren-facetdeterminatie.aspx?&amp;set=Boktorren van NL&amp;facetGroup=&amp;lookup=Boktorren&amp;similar=http://www.rnaproject.org/data/7e7ae727-91a1-4b73-98ce-5714384fd453&amp;similar=http://www.rnaproject.org/data/9511e89b-cf11-4dba-a7c6-4ae51d44c11c&amp;similarBaseObj_title=Bruine grootoogboktor&amp;similarBaseObj_NSRUrl=http://www.nederlandsesoorten.nl/get?site=nlsr&amp;view=nlsr&amp;page_alias=conceptcard&amp;cid=0AHCYFBRPNBN&amp;similarBaseObj_imageSource=http://www.naturalisbeeldbibliotheek.nl/beeldbank/comping/132749.jpg" target="_self">gelijkende soorten</a></div><div class="result"><div class="resultImageHolder"><a href="http://www.naturalisbeeldbibliotheek.nl/beeldbank/comping/132787.jpg" onclick="Lightbox.showBoxImage(this.href);return false;"><img class="result" height="207" width="145" src="http://www.naturalisbeeldbibliotheek.nl/beeldbank/comping/132787.jpg" title="Foto Theodoor Heijerman" /></a></div><div><a href="http://www.nederlandsesoorten.nl/get?site=nlsr&amp;view=nlsr&amp;page_alias=conceptcard&amp;cid=0AHCYFBRRKCL" target="_blanc">Bruine soldaatboktor<br /><span class="scientificName">Obrium brunneum</span></a></div><img class="gender" height="17" width="8" src="/images/male.png" title="mannelijk" /><a class="similarBtn" href="/boktorren-home/boktorren-facetdeterminatie.aspx?&amp;set=Boktorren van NL&amp;facetGroup=&amp;lookup=Boktorren&amp;similar=http://www.rnaproject.org/data/785c64a8-edf9-4f80-a539-f5c589b2b0c6&amp;similar=http://www.rnaproject.org/data/c94c90ae-d12f-487f-bcb3-b34690617e37&amp;similar=http://www.rnaproject.org/data/63908033-4a8e-49c2-b580-7d19da543d21&amp;similarBaseObj_title=Bruine soldaatboktor&amp;similarBaseObj_NSRUrl=http://www.nederlandsesoorten.nl/get?site=nlsr&amp;view=nlsr&amp;page_alias=conceptcard&amp;cid=0AHCYFBRRKCL&amp;similarBaseObj_imageSource=http://www.naturalisbeeldbibliotheek.nl/beeldbank/comping/132787.jpg" target="_self">gelijkende soorten</a></div><div class="result"><div class="resultImageHolder"><img class="result" height="207" width="145" src="/images/noimage_Boktorren van NL.gif" title="geen afbeelding beschikbaar" /></div><div><span class="scientificName">Callidium coriaceum</span></div><span class="genderUnknown">-</span><a class="similarBtn" href="/boktorren-home/boktorren-facetdeterminatie.aspx?&amp;set=Boktorren van NL&amp;facetGroup=&amp;lookup=Boktorren&amp;similar=http://www.rnaproject.org/data/8517dc99-b8bd-47e8-87d1-da6fc082a67b&amp;similar=http://www.rnaproject.org/data/d5fa67dd-2c87-48d6-b5c1-5e0fabae97d6&amp;similarBaseObj_title=Callidium coriaceum&amp;similarBaseObj_NSRUrl=" target="_self">gelijkende soorten</a></div><div class="result"><div class="resultImageHolder"><img class="result" height="207" width="145" src="/images/noimage_Boktorren van NL.gif" title="geen afbeelding beschikbaar" /></div><div><span class="scientificName">Chlorophorus figuratus</span></div><span class="genderUnknown">-</span><a class="similarBtn" href="/boktorren-home/boktorren-facetdeterminatie.aspx?&amp;set=Boktorren van NL&amp;facetGroup=&amp;lookup=Boktorren&amp;similar=http://www.rnaproject.org/data/6062e3c9-186b-4e05-8328-e3424a56840a&amp;similar=http://www.rnaproject.org/data/de006ad5-13d8-4de8-ad8b-2a5e5b49d53e&amp;similar=http://www.rnaproject.org/data/eefba4bf-7d20-4ccc-b090-857bf74aabcd&amp;similarBaseObj_title=Chlorophorus figuratus&amp;similarBaseObj_NSRUrl=" target="_self">gelijkende soorten</a></div><div class="result"><div class="resultImageHolder"><img class="result" height="207" width="145" src="/images/noimage_Boktorren van NL.gif" title="geen afbeelding beschikbaar" /></div><div><span class="scientificName">Chlorophorus glabromaculatus</span></div><span class="genderUnknown">-</span><a class="similarBtn" href="/boktorren-home/boktorren-facetdeterminatie.aspx?&amp;set=Boktorren van NL&amp;facetGroup=&amp;lookup=Boktorren&amp;similar=http://www.rnaproject.org/data/b98d1047-478e-4ef5-a7f8-2baabcca7af4&amp;similar=http://www.rnaproject.org/data/d0dfa128-4657-4f7b-8fd0-3b53c8f26750&amp;similarBaseObj_title=Chlorophorus glabromaculatus&amp;similarBaseObj_NSRUrl=" target="_self">gelijkende soorten</a></div><div class="result"><div class="resultImageHolder"><img class="result" height="207" width="145" src="/images/noimage_Boktorren van NL.gif" title="geen afbeelding beschikbaar" /></div><div><span class="scientificName">Chlorophorus herbstii</span></div><span class="genderUnknown">-</span><a class="similarBtn" href="/boktorren-home/boktorren-facetdeterminatie.aspx?&amp;set=Boktorren van NL&amp;facetGroup=&amp;lookup=Boktorren&amp;similar=http://www.rnaproject.org/data/2de152ac-c86f-440a-8bd7-873d350dc474&amp;similar=http://www.rnaproject.org/data/b98d1047-478e-4ef5-a7f8-2baabcca7af4&amp;similarBaseObj_title=Chlorophorus herbstii&amp;similarBaseObj_NSRUrl=" target="_self">gelijkende soorten</a></div><div class="result"><div class="resultImageHolder"><img class="result" height="207" width="145" src="/images/noimage_Boktorren van NL.gif" title="geen afbeelding beschikbaar" /></div><div><span class="scientificName">Chlorophorus sartor</span></div><span class="genderUnknown">-</span><a class="similarBtn" href="/boktorren-home/boktorren-facetdeterminatie.aspx?&amp;set=Boktorren van NL&amp;facetGroup=&amp;lookup=Boktorren&amp;similar=http://www.rnaproject.org/data/eefba4bf-7d20-4ccc-b090-857bf74aabcd&amp;similar=http://www.rnaproject.org/data/de006ad5-13d8-4de8-ad8b-2a5e5b49d53e&amp;similar=http://www.rnaproject.org/data/6062e3c9-186b-4e05-8328-e3424a56840a&amp;similarBaseObj_title=Chlorophorus sartor&amp;similarBaseObj_NSRUrl=" target="_self">gelijkende soorten</a></div><div class="footerPagination"><ul class="list paging"><li><strong>1</strong></li><li><a href="?&amp;set=Boktorren van NL&amp;&amp;lookup=Boktorren&amp;startPage=2">2</a></li><li><a href="?&amp;set=Boktorren van NL&amp;&amp;lookup=Boktorren&amp;startPage=3">3</a></li><li><a href="?&amp;set=Boktorren van NL&amp;&amp;lookup=Boktorren&amp;startPage=4">4</a></li><li><a href="?&amp;set=Boktorren van NL&amp;&amp;lookup=Boktorren&amp;startPage=5">5</a></li><li><span>...</span></li><li><a href="?&amp;set=Boktorren van NL&amp;&amp;lookup=Boktorren&amp;startPage=9">9</a></li><li class="next"><a href="?&amp;set=Boktorren van NL&amp;&amp;lookup=Boktorren&amp;startPage=2">&gt;&gt;</a></li></ul></div></div></div>


		</div>

{literal}
<script type="text/JavaScript">
$(document).ready(function(){
{/literal}

{if $characteristics}
{foreach from=$characteristics item=v}
{assign var=foo value="|"|explode:$v.label}{if $foo[0] && $foo[1]}{assign var=cLabel value=$foo[0]}{assign var=cText value=$foo[1]}{else}{assign var=cLabel value=$v.label}{assign var=cText value=''}{/if}
nbcAddCharacter({literal}{{/literal}id: {$v.id},type:'{$v.type}',label:'{$cLabel|addslashes}',text:'{$cText|addslashes|trim}'{*
				states : [{if $v.states && $v.type!='range'}{foreach from=$v.states item=s key=k}
			{literal}{{/literal}
			id:{$s.id},
			label:'{$s.label|addslashes}',
			file_name:'{$s.file_name|addslashes}',
			label:'{$s.label|addslashes}',
			text:'{$s.text|addslashes}',
			{literal}}{/literal}{if $k!=$v.states|@count-1},{/if}
			{/foreach}
				{/if}
          ]
        *}{literal}}{/literal});
{/foreach}
{/if}

{literal}
});
</script>
{/literal}
		
		
		
		
		
		
		
		
		
		
{include file="../shared/footer.tpl"}
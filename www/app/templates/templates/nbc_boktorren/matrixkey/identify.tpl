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
		});

	}

}

function nbcGetResults() {

	allAjaxHandle = $.ajax({
		url : 'ajax_interface.php',
		type: 'POST',
		data : ({
			action : 'get_results_nbc' ,
			id : id , 
			time : getTimestamp()
		}),
		success : function (data) {
			alert(data);return;
			showDialog(c.label,data);

			$('#dialog').css('width', (c.type=='media' ? 610 : 425));
			$.modaldialog.reinitPosition({top:175});

		}
	});

	
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
				</ul>
			</li>
		{/foreach}				
		<ul class="facetCategories clearSelectionBtn">
			<li class="closed"><span><a href="?set=Boktorren van NL&amp;lookup=Boktorren">wis geselecteerde eigenschappen</a></span></li>
		</ul>
		<ul class="facetCategories sourceContainer">
			<li class="closed">
				<p>
					<strong>Gebaseerd op</strong>
				</p>
				<p>
					Zeegers, Th. &amp; Th. Heijerman 2008. De Nederlandse boktorren
					(Cerambycidae). (<a href="http://www.naturalis.nl/ET2" target="_blank">Meer info</a>)
				</p>
			</li>
		</ul>
	</div> {* /facets *}
	
	<div id="results" xmlns:opensearch="http://a9.com/-/spec/opensearch/1.1/">
		<div id="resultsHeader">
			<span>
				<div class="headerSelectionLabel">
					<strong style="color:#333">143</strong> (van <strong style="color:#777;">143</strong>) objecten in huidige selectie
				</div>
				<div class="headerPagination">
					<ul class="list paging">
						<li>
							<strong>1</strong>
						</li>
						<li>
							<a href="?&amp;set=Boktorren van NL&amp;&amp;lookup=Boktorren&amp;startPage=2">2</a>
						</li>
						<li>
							<a href="?&amp;set=Boktorren van NL&amp;&amp;lookup=Boktorren&amp;startPage=3">3</a>
						</li>
						<li>
							<a href="?&amp;set=Boktorren van NL&amp;&amp;lookup=Boktorren&amp;startPage=4">4</a>
						</li>
						<li>
							<a href="?&amp;set=Boktorren van NL&amp;&amp;lookup=Boktorren&amp;startPage=5">5</a>
						</li>
						<li>
							<span>...</span>
						</li>
						<li>
							<a href="?&amp;set=Boktorren van NL&amp;&amp;lookup=Boktorren&amp;startPage=9">9</a>
						</li>
						<li class="next">
							<a href="?&amp;set=Boktorren van NL&amp;&amp;lookup=Boktorren&amp;startPage=2">&gt;&gt;</a>
						</li>
					</ul>
				</div>
			</span>
		</div>
		
		<!--  div class="result">
			<div class="resultImageHolder">
				<a href="http://www.naturalisbeeldbibliotheek.nl/beeldbank/comping/132737.jpg" onclick="Lightbox.showBoxImage(this.href);return false;">
					<img class="result" height="207" width="145" src="http://www.naturalisbeeldbibliotheek.nl/beeldbank/comping/132737.jpg" title="Foto Theodoor Heijerman" />
				</a>
			</div>
			<div>
				<a href="http://www.nederlandsesoorten.nl/get?site=nlsr&amp;view=nlsr&amp;page_alias=conceptcard&amp;cid=0AHCYSI00364" target="_blanc">
					Beemdkroonboktor<br />
					<span class="scientificName">
						Agapanthia intermedia
					</span>
				</a>
			</div>
			<img class="gender" height="17" width="8" src="/images/female.png" title="vrouwelijk" />
			<a class="similarBtn" href="/boktorren-home/boktorren-facetdeterminatie.aspx?&amp;set=Boktorren van NL&amp;facetGroup=&amp;lookup=Boktorren&amp;similar=http://www.rnaproject.org/data/9d5fad1f-6325-498f-8ac7-efcacd46fe65&amp;similar=http://www.rnaproject.org/data/4fa9f65a-0d26-4534-af49-125e23f20d4f&amp;similar=http://www.rnaproject.org/data/6a599a2e-df27-44c0-9bdf-cdf129639d0b&amp;similar=http://www.rnaproject.org/data/5f7604ba-633b-46b6-8cbe-82b5f90db64b&amp;similarBaseObj_title=Beemdkroonboktor&amp;similarBaseObj_NSRUrl=http://www.nederlandsesoorten.nl/get?site=nlsr&amp;view=nlsr&amp;page_alias=conceptcard&amp;cid=0AHCYSI00364&amp;similarBaseObj_imageSource=http://www.naturalisbeeldbibliotheek.nl/beeldbank/comping/132737.jpg" target="_self">
				gelijkende soorten
			</a>
		</div -->
		
		{foreach from=$taxa item=v}

		<div class="result">
			<div class="resultImageHolder">
				<a href="http://www.naturalisbeeldbibliotheek.nl/beeldbank/comping/132737.jpg" onclick="Lightbox.showBoxImage(this.href);return false;">
					<img class="result" height="207" width="145" src="http://www.naturalisbeeldbibliotheek.nl/beeldbank/comping/132737.jpg" title="Foto Theodoor Heijerman" />
				</a>
			</div>
			<div>
				<a href="http://www.nederlandsesoorten.nl/get?site=nlsr&amp;view=nlsr&amp;page_alias=conceptcard&amp;cid=0AHCYSI00364" target="_blanc">
					Beemdkroonboktor<br />
					<span class="scientificName">
						Agapanthia intermedia
					</span>
				</a>
			</div>
			<img class="gender" height="17" width="8" src="/images/female.png" title="vrouwelijk" />
			<a class="similarBtn" href="/boktorren-home/boktorren-facetdeterminatie.aspx?&amp;set=Boktorren van NL&amp;facetGroup=&amp;lookup=Boktorren&amp;similar=http://www.rnaproject.org/data/9d5fad1f-6325-498f-8ac7-efcacd46fe65&amp;similar=http://www.rnaproject.org/data/4fa9f65a-0d26-4534-af49-125e23f20d4f&amp;similar=http://www.rnaproject.org/data/6a599a2e-df27-44c0-9bdf-cdf129639d0b&amp;similar=http://www.rnaproject.org/data/5f7604ba-633b-46b6-8cbe-82b5f90db64b&amp;similarBaseObj_title=Beemdkroonboktor&amp;similarBaseObj_NSRUrl=http://www.nederlandsesoorten.nl/get?site=nlsr&amp;view=nlsr&amp;page_alias=conceptcard&amp;cid=0AHCYSI00364&amp;similarBaseObj_imageSource=http://www.naturalisbeeldbibliotheek.nl/beeldbank/comping/132737.jpg" target="_self">
				gelijkende soorten
			</a>
		</div>
		
		{/foreach}
		
		
		
		<div class="footerPagination">
			<ul class="list paging">
				<li><strong>1</strong></li>
				<li><a href="?&amp;set=Boktorren van NL&amp;&amp;lookup=Boktorren&amp;startPage=2">2</a></li>
				<li><a href="?&amp;set=Boktorren van NL&amp;&amp;lookup=Boktorren&amp;startPage=3">3</a></li>
				<li><a href="?&amp;set=Boktorren van NL&amp;&amp;lookup=Boktorren&amp;startPage=4">4</a></li>
				<li><a href="?&amp;set=Boktorren van NL&amp;&amp;lookup=Boktorren&amp;startPage=5">5</a></li>
				<li><span>...</span></li>
				<li><a href="?&amp;set=Boktorren van NL&amp;&amp;lookup=Boktorren&amp;startPage=9">9</a></li>
				<li class="next"><a href="?&amp;set=Boktorren van NL&amp;&amp;lookup=Boktorren&amp;startPage=2">&gt;&gt;</a></li>
			</ul>
		</div>
	</div>
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
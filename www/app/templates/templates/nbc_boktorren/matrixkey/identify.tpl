{include file="../shared/header.tpl"}
<form id="form2" method="post" action="identify.php">
<input type="hidden" id="action2" name="action" value="" />
<input type="hidden" id="id2" name="id" value="" />
</form>
<div id="content">
	<div id="facets">
		<ul class="facetCategories">
		{foreach from=$groups item=v}
			{assign var=openGroup value=false}
			<li id="character-item-{$v.id}" class="closed"><a href="#" onclick="nbcToggleGroup({$v.id})">{$v.label}</a>
				<ul id="character-group-{$v.id}" class="facets hidden">
					{foreach from=$v.chars item=c}
					{assign var=foo value="|"|explode:$c.label}{if $foo[0] && $foo[1]}{assign var=cLabel value=$foo[0]}{assign var=cText value=$foo[1]}{else}{assign var=cLabel value=$c.label}{assign var=cText value=''}{/if}
					<li><a class="facetLink" href="#" onclick="nbcShowStates({$c.id})">{$cLabel} {$c.value}</a>
					{if $activeChars[$c.id]}
					{assign var=openGroup value=true}
					<span>
					{foreach from=$storedStates item=s key=cK}
					{if $s.characteristic_id==$c.id}
						<div class="facetValueHolder">
							{$s.value} {$s.label} <a href="#" class="removeBtn" onclick="$('#action2').val('clear');$('#id2').val('{$cK}');$('#form2').submit();">(deselecteer)</a>
						</div>
					{/if}
					{/foreach}
					</span>
					{/if}
					</li>
					{/foreach}
				</ul>
			</li>
			{if $openGroup}
			<script>
			nbcToggleGroup({$v.id});
			</script>
			{/if}
		{/foreach}				
		<ul class="facetCategories clearSelectionBtn">
			<li class="closed">
				<span><a href="#" onclick="$('#action2').val('clear');$('#form2').submit();">wis geselecteerde eigenschappen</a></span>
			</li>
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

	<div id="results">
		<div id="resultsHeader">
			<span>
				<div id="result-count" class="headerSelectionLabel">
				</div>
				<div class="headerPagination">
					<ul id="paging-header" class="list paging">
					</ul>
				</div>
			</span>
		</div>

		<div id="similarSpeciesHeader" class="hidden"></div>
		
		<div id="results-container">
		</div>
		
		<div class="footerPagination">
			<ul id="paging-footer" class="list paging">
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
nbcImageRoot = '{$nbcImageRoot}';
{if $nbcStart}
nbcStart = {$nbcStart};
{/if}
{if $nbcSimilar}
nbcShowSimilar({$nbcSimilar[0]},'{$nbcSimilar[1]}');
{else}
{/if}
{if $taxa}
nbcData = $.parseJSON('{$taxa}');
nbcProcessResults();
{else}
nbcGetResults();
{/if}

{literal}
});
</script>
{/literal}
		
{include file="../shared/footer.tpl"}
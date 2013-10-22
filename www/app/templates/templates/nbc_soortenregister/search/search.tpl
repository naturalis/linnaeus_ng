{include file="../shared/header.tpl"}

<div id="dialogRidge">

	{include file="_left_column.tpl"}
    
	<div id="content">


		<div id="results">
			<p>
				<h2>
				Gezocht op "{$search}": <span id="resultcount-header"></span>
				</h2>
			</p>
		{assign var=resultcount value=0}
		{if $results.species.numOfResults > 0}
			<p>
			<ol>
			{foreach from=$results.species.results key=cat item=res index=x}
				{if $res.syslabel=='higher_taxa' || $res.syslabel=='species_names' || $res.syslabel=='taxon_names'}
				{math equation="x + y" x=$resultcount y=$res.numOfResults assign=resultcount}
				{foreach from=$res.data key=k item=v name=r}
					<li style="margin-bottom:5px"><a class="result"href="../species/taxon.php?id={$v.taxon_id}{if $v.cat}&cat={$v.cat}{/if}&sidx={$v.sIndex}">
						{$v.target}
						{if $v.label}{h search=$search}{$v.label}{/h}
						{elseif $v.content}"{foundContent search=$search}{$v.content}{/foundContent}"{/if}
					</a>
					{if $v.post_script}<br />{$v.post_script}{/if}
					</li>
				{/foreach}
				{/if}
			{/foreach}
			</ol>
			</p>
		{/if}
		</div>
		
	</div>

	{include file="../shared/_right_column.tpl"}

</div>

{literal}
<script type="text/JavaScript">
$(document).ready(function(){
{/literal}
	$('#resultcount-header').html('{$resultcount} '+({$resultcount}==1 ? 'resultaat' : 'resultaten'));
{literal}
});
</script>
{/literal}

{include file="../shared/messages.tpl"}
{include file="../shared/footer.tpl"}

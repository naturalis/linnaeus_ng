{include file="../shared/admin-header.tpl"}

<div id="page-main">

{if $taxa|@count>0}

	{t}To change the name, rank or parent of a taxon, click its name. To edit a taxon's content, click the corresponding cell in the column 'content'.{/t}
	
	<span id="message-container" style="margin-left:175px">&nbsp;</span>
	<table>
	<tr>
		<th onclick="allTableColumnSort('taxon_order');">{t}Rank{/t}</th>
		<th onclick="allTableColumnSort('taxon');">{t}Taxon{/t}</th>
		<th onclick="allTableColumnSort('is_hybrid');">{t}Hybrid{/t}</th>
		<th>&nbsp;</th>
		<th onclick="allTableColumnSort('pct_finished');" style="text-align:center" >{t}Content{/t}</th>
		<th colspan="3" style="text-align:center" title="images, videos, soundfiles">{t}Media{/t}</th>
		<td>{t}Currently being edited by:{/t}</td>
	</tr>
	
	{assign var=prev_rank value=-1}
	{assign var=firstDotLength value=false}
	
	{section name=i loop=$taxa}
	{if $taxa[i].rank!=''}
		{if $firstDotLength==false}{assign var=firstDotLength value=$taxa[i].level}{/if}
		{assign var=t value=$taxa[i].id}
		{if $prev_rank!=$taxa[i].rank_id && $taxa[i].sibling_count>1}
			{if $arrowBuffer==true}
				{assign var=arrowBuffer value=false}
			{else}
				{assign var=arrowBuffer value=true}
			{/if}
		{/if}
	<tr class="taxon-list-row" id="row-{$taxa[i].id}">
		<td class="taxon-list-cell-rank">
		<span style="color:#bbb">{section name=loop start=$firstDotLength loop=$taxa[i].level-$}.{/section}</span>{$taxa[i].rank}
		</td>
		<td class="taxon-list-cell-name" id="namecell{$taxa[i].id}">
			<a href="edit.php?id={$taxa[i].id}">{$taxa[i].taxon}</a>
		</td>
		<td>
			{if $taxa[i].is_hybrid==1}<span class="taxon-hybrid-x">x</span>{/if}
		</td>
		<td>
		{if $arrowBuffer}&nbsp;{/if}
		{if $taxa[i].sibling_count>1}
			{if $taxa[i].sibling_pos=='last'}
				<span
					class="pseudo-a"
					title="{t}move taxon upward{/t}"
					onclick="$('#scroll').val($(window).scrollTop());$('#id').val({$t});$('#move').val('up');$('#rearrangeForm').submit();">
					&uarr;
				</span>
			{else}
				<span
					class="pseudo-a"
					title="{t}move taxon downward{/t}"
					onclick="$('#scroll').val($(window).scrollTop());$('#id').val({$t});$('#move').val('down');$('#rearrangeForm').submit();">
					&darr;
				</span>
			{/if}
		{/if}
		</td>
		<td
			class="taxon-list-cell-language{if $languages[j].publish[$t].pct_finished==100}-done{elseif $languages[j].publish[$t].pct_finished==0}-empty{/if}"
			title="{$languages[j].publish[$t].published} of {$languages[j].publish[$t].total} {t}pages published{/t}"
			onclick="window.open('taxon.php?id={$t}','_top');">
			{$taxa[i].pct_finished}% done
		</td>
		<td
			class="taxon-list-cell-media{if $taxa[i].totMediaCount==''}-empty{/if}"
			title="{t}images{/t}"
			onclick="window.open('media.php?id={$t}#image','_top');">
			{if $taxa[i].mediaCount.image!=''}{$taxa[i].mediaCount.image}{else}0{/if}
		</td>
		<td
			class="taxon-list-cell-media{if $taxa[i].totMediaCount==''}-empty{/if}"
			title="{t}videos{/t}"
			onclick="window.open('media.php?id={$t}#video','_top');">
			{if $taxa[i].mediaCount.video!=''}{$taxa[i].mediaCount.video}{else}0{/if}
		</td>
		<td class="taxon-list-cell-media{if $taxa[i].totMediaCount==''}-empty{/if}" 
			title="{t}soundfiles{/t}" 
			onclick="window.open('media.php?id={$t}#sound','_top');">
			{if $taxa[i].mediaCount.sound!=''}{$taxa[i].mediaCount.sound}{else}0{/if}
		</td>
		<td id="usage-{$taxa[i].id}">
		</td>
	</tr>
		{assign var=prev_rank value=$taxa[i].rank_id}

	{/if}
	{/section}
	</table>
	
	<br />

	{if $languages|@count==0}
		{t}You have to define at least one language in your project before you can add any taxa.{/t}<br />
		<a href="../projects/data.php">{t}Define languages.{/t}</a>
	{else}
		<a href="edit.php">{t}Add a new taxon{/t}</a>
	{/if}

{/if}
</div>

<form method="post" action="" name="sortForm" id="sortForm">
<input type="hidden" name="key" id="key" value="{$sortBy.key}" />
<input type="hidden" name="dir" value="{$sortBy.dir}"  />
</form>
<form method="post" action="" name="rearrangeForm" id="rearrangeForm">
<input type="hidden" name="rnd" value="{$rnd}" />
<input type="hidden" name="id" id="id" value="" />
<input type="hidden" name="move" id="move" value=""  />
<input type="hidden" name="scroll" id="scroll" value=""  />
</form>

{literal}
<script type="text/JavaScript">
$(document).ready(function(){
	taxonCheckLockOutStates();
{/literal}
{if $scroll}
	allScrollTo({$scroll});
{/if}
{literal}
});
</script>
{/literal}

{include file="../shared/admin-messages.tpl"}
{include file="../shared/admin-footer.tpl"}

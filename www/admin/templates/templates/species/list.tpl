{include file="../shared/admin-header.tpl"}

<div id="page-main">

{if $taxa|@count>0}

	{t}To change the name, rank or parent of a taxon, click its name. To edit a taxon's content, click the corresponding cell in the column 'content'.{/t}
	
	<span id="message-container" style="margin-left:175px">&nbsp;</span>
	<table style="width:100%">
	<tr style="vertical-align:bottom">
		<th style="width:100px;" onclick="allTableColumnSort('taxon_order');">{t}Rank{/t}</th>
		<th style="width:240px;" onclick="allTableColumnSort('taxon');">{t}Taxon{/t}</th>
{if $session.project.includes_hybrids==1}		<th style="width:25px;" onclick="allTableColumnSort('is_hybrid');">{t}Hybrid{/t}</th>{/if}
		<th style="width:50px;" onclick="allTableColumnSort('pct_finished');">{t}Content{/t}</th>
		<th style="width:50px;" title="{t}images, videos, soundfiles{/t}">{t}Media{/t}</th>
		<th style="width:80px;">{t}Synonyms{/t}</th>
		<th style="width:90px;">{t}Common names{/t}</th>
		<th style="width:40px;text-align:center">{t}Move{/t}</th>
		<th style="width:20px;text-align:center">{t}Delete{/t}</th>
		<th>{t}Is being edited by:{/t}</th>
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
{if $session.project.includes_hybrids==1}		<td>
			{if $taxa[i].is_hybrid==1}<span class="taxon-hybrid-x">x</span>{/if}
		</td>
{/if}

		<td>
			<span class="pseudo-a" onclick="window.open('taxon.php?id={$t}','_top');">{$taxa[i].pctFinished}% {t}done{/t}</span>
		</td>

		<td title="{t}media files{/t}">
			<span class="pseudo-a" onclick="window.open('media.php?id={$t}','_self');">{$taxa[i].mediaCount} {if $taxa[i].mediaCount==1}{t}file{/t}{else}{t}files{/t}{/if}</span>
		</td>

		<td>
			<span class="pseudo-a" onclick="window.open('synonyms.php?id={$t}','_self');">{$taxa[i].synonymCount} {if $taxa[i].synonymCount==1}{t}synonym{/t}{else}{t}synonyms{/t}{/if}</span>
		</td>

		<td>
			<span class="pseudo-a" onclick="window.open('common.php?id={$t}','_self');">{$taxa[i].commonnameCount} {if $taxa[i].commonnameCount==1}{t}name{/t}{else}{t}names{/t}{/if}</span>
		</td>

		<td style="text-align:center">
		{if $arrowBuffer}&nbsp;&nbsp;{/if}
		{if $taxa[i].sibling_count>1}
			{if $taxa[i].sibling_pos=='last'}
				<span
					class="pseudo-a"
					title="{t}move branch upward in the tree{/t}"
					onclick="$('#scroll').val($(window).scrollTop());$('#id').val({$t});$('#move').val('up');$('#rearrangeForm').submit();">
					&uarr;
				</span>
			{else}
				<span
					class="pseudo-a"
					title="{t}move branch downward in the tree{/t}"
					onclick="$('#scroll').val($(window).scrollTop());$('#id').val({$t});$('#move').val('down');$('#rearrangeForm').submit();">
					&darr;
				</span>
			{/if}
		{/if}
		{if !$arrowBuffer}&nbsp;&nbsp;{/if}
		</td>

		<td
			class="pseudo-a" 
			style="text-align:center" 
			onclick="taxonDeleteData({$taxa[i].id},'{$taxa[i].taxon}');">
			x
		</td>

		<td id="usage-{$taxa[i].id}"></td>
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

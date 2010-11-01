{include file="../shared/admin-header.tpl"}

<div id="page-main">
{if $taxa|@count>0}

To change the name, rank or parent of a taxon, click it's name. To edit a taxon's content, click the corresponding cell in the column 'content'.
<span id="message-container" style="margin-left:175px">&nbsp;</span>
<table>
<tr>
	<th onclick="allTableColumnSort('taxon_order');">Rank</th>
	<th onclick="allTableColumnSort('taxon');">Taxon</th>
	<th onclick="allTableColumnSort('is_hybrid');">Hybrid</th>
	<th>&nbsp;</th>
	<th onclick="allTableColumnSort('pct_finished');" style="text-align:center" >Content</th>
	<th colspan="3" style="text-align:center" title="images, videos, soundfiles">Media</th>
	<td>Currently being edited by:</td>
</tr>
{section name=i loop=$taxa}
{assign var=x value=$taxa[i].level}
{assign var=t value=$taxa[i].id}
<tr class="taxon-list-row" id="row-{$taxa[i].id}">
	<td class="taxon-list-cell-rank">
	<span style="color:#bbb">{section name=loop start=0 loop=$taxa[i].level}.{/section}</span>{$taxa[i].rank}
	</td>
	<td class="taxon-list-cell-name" id="namecell{$taxa[i].id}">
		<a href="edit.php?id={$taxa[i].id}">{$taxa[i].taxon_order}:{$taxa[i].taxon}</a>
	</td>
	<td>
		{if $taxa[i].is_hybrid==1}<span class="taxon-hybrid-x">x</span>{/if}
	</td>

	<td>
		<span class="pseudo-a" title="move taxon downward" onclick="$('#scroll').val($(window).scrollTop());$('#id').val({$t});$('#move').val('down');$('#rearrangeForm').submit();">&darr;</span>
	</td>

	<td class="taxon-list-cell-language{if $languages[j].publish[$t].pct_finished==100}-done{elseif $languages[j].publish[$t].pct_finished==0}-empty{/if}" title="{$languages[j].publish[$t].published} of {$languages[j].publish[$t].total} pages published" onclick="window.open('taxon.php?id={$t}','_top');">
		{$taxa[i].pct_finished}% done
	</td>
	<td class="taxon-list-cell-media{if $taxa[i].totMediaCount==''}-empty{/if}" title="images" onclick="window.open('media.php?id={$t}#image','_top');">{if $taxa[i].mediaCount.image!=''}{$taxa[i].mediaCount.image}{else}0{/if}</td>
	<td class="taxon-list-cell-media{if $taxa[i].totMediaCount==''}-empty{/if}" title="videos" onclick="window.open('media.php?id={$t}#video','_top');">{if $taxa[i].mediaCount.video!=''}{$taxa[i].mediaCount.video}{else}0{/if}</td>
	<td class="taxon-list-cell-media{if $taxa[i].totMediaCount==''}-empty{/if}" title="soundfiles" onclick="window.open('media.php?id={$t}#sound','_top');">{if $taxa[i].mediaCount.sound!=''}{$taxa[i].mediaCount.sound}{else}0{/if}</td>
	<td id="usage-{$taxa[i].id}"></td>
</tr>
{/section}
</table>
<br />
{if $languages|@count==0}
You have to define at least one language in your project before you can add any taxa.<br />
<a href="../projects/data.php">Define languages.</a>
{else}
<a href="edit.php">Add a new taxon</a>
{/if}
{/if}
</div>

<form method="post" action="" name="sortForm" id="sortForm">
<input type="hidden" name="key" id="key" value="{$sortBy.key}" />
<input type="hidden" name="dir" value="{$sortBy.dir}"  />
</form>
<form method="post" action="" name="rearrangeForm" id="rearrangeForm">
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

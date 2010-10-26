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
	<th onclick="allTableColumnSort('pct_finished');" style="text-align:center" >Content</th>
	<th colspan="3" style="text-align:center" title="images, videos, soundfiles">Media</td>
	<td>Currently being edited by:</td>
</tr>
{section name=i loop=$taxa}
<tr class="taxon-list-row">
	<td class="taxon-list-cell-rank">{$taxa[i].rank}</td>
	<td class="taxon-list-cell-name" id="namecell{$taxa[i].id}">
		<a href="edit.php?id={$taxa[i].id}">{$taxa[i].taxon}</a>
	</td>
	<td>
		{if $taxa[i].is_hybrid==1}<span class="taxon-hybrid-x">x</span>{/if}
	</td>
	{assign var=t value=$taxa[i].id}
	<td class="taxon-list-cell-language{if $languages[j].publish[$t].pct_finished==100}-done{elseif $languages[j].publish[$t].pct_finished==0}-empty{/if}" title="{$languages[j].publish[$t].published} of {$languages[j].publish[$t].total} pages published" onclick="window.open('taxon.php?id={$taxa[i].id}','_top');">
		{$taxa[i].pct_finished}% done
	</td>
	<td class="taxon-list-cell-media{if $taxa[i].totMediaCount==''}-empty{/if}" title="images" onclick="window.open('media.php?id={$taxa[i].id}#image','_top');">{if $taxa[i].mediaCount.image!=''}{$taxa[i].mediaCount.image}{else}0{/if}</td>
	<td class="taxon-list-cell-media{if $taxa[i].totMediaCount==''}-empty{/if}" title="videos" onclick="window.open('media.php?id={$taxa[i].id}#video','_top');">{if $taxa[i].mediaCount.video!=''}{$taxa[i].mediaCount.video}{else}0{/if}</td>
	<td class="taxon-list-cell-media{if $taxa[i].totMediaCount==''}-empty{/if}" title="soundfiles" onclick="window.open('media.php?id={$taxa[i].id}#sound','_top');">{if $taxa[i].mediaCount.sound!=''}{$taxa[i].mediaCount.sound}{else}0{/if}</td>
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

{literal}
<script type="text/JavaScript">
$(document).ready(function(){
	taxonCheckLockOutStates();
});
</script>
{/literal}

{include file="../shared/admin-messages.tpl"}
{include file="../shared/admin-footer.tpl"}

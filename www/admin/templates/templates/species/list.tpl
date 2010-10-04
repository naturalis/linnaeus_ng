{include file="../shared/admin-header.tpl"}

<div id="page-main">
Click a cell to access the subpages for a taxon in a specific language. To change the name of a taxon, click it's name.
<span id="message-container" style="margin-left:175px">&nbsp;</span>
<table>
<tr>
	<th></th>
	<th onclick="allTableColumnSort('taxon_order');">Rank</th>
	<th onclick="allTableColumnSort('taxon');">Taxon</th>
	{section name=j loop=$languages}
	<td style="text-align:right; width:75px">
		{if $languages[j].active=='n'}({/if}{$languages[j].language}{if $languages[j].def_language==1} *{/if}{if $languages[j].active=='n'}){/if}
	</td>
	{/section}
	<td>Media</td>
	<td>Currently being edited by:</td>
</tr>
{section name=i loop=$taxa}
<tr class="taxon-list-row">
	<td >{$taxa[i].symbol}</td>
	<td class="taxon-list-cell-rank">{$taxa[i].rank}</td>
	<td class="taxon-list-cell-name" id="namecell{$taxa[i].id}">
		<!-- a href="edit.php?id={$taxa[i].id}">{$taxa[i].taxon}</a -->
		<span onclick="taxonEditTaxonName({$taxa[i].id})" id="name{$taxa[i].id}" class="pseudo-a">{$taxa[i].taxon}</span>
	</td>
	{assign var=t value=$taxa[i].id}
	{section name=j loop=$languages}
	<td class="taxon-list-cell-language{if $languages[j].publish[$t].pct_finished==100}-done{elseif $languages[j].publish[$t].pct_finished==0}-empty{/if}" title="{$languages[j].publish[$t].published} of {$languages[j].publish[$t].total} pages published">
		<a href="edit.php?id={$taxa[i].id}&lan={$languages[j].language_id}">
		{$languages[j].publish[$t].pct_finished}% done
		</a>
	</td>
	{/section}	
	<td><a href="media.php?id={$taxa[i].id}">media</a></td>
	<td id="usage-{$taxa[i].id}">
	</td>
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

{include file="../shared/admin-footer.tpl"}

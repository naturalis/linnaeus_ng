{include file="../shared/admin-header.tpl"}

<div id="page-main">

<table>
<tr>
	<th onclick="allTableColumnSort('taxon');">Taxon</th>
	{section name=j loop=$languages}
	<td style="text-align:right; width:75px">
		{if $languages[j].active=='n'}({/if}{$languages[j].language}{if $languages[j].def_language==1} *{/if}{if $languages[j].active=='n'}){/if}
	</td>
	{/section}	

</tr>
{section name=i loop=$taxa}
<tr class="taxon-list-row">
	<td class="taxon-list-cell-name">
		<a href="edit.php?id={$taxa[i].id}">{$taxa[i].taxon}</a>
	</td>
	{assign var=t value=$taxa[i].id}
	{section name=j loop=$languages}
	<td class="taxon-list-cell-language{if $languages[j].publish[$t].pct_finished==100}-done{/if}" title="{$languages[j].publish[$t].published} of {$languages[j].publish[$t].total} pages published">
		<a href="edit.php?id={$taxa[i].id}&lan={$languages[j].language_id}">
		{$languages[j].publish[$t].pct_finished}% done
		</a>
	</td>
	{/section}	
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

<form method="post" action="" name="postForm" id="postForm">
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

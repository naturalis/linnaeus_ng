{include file="../shared/admin-header.tpl"}

<div id="page-main">

<table>
<tr>
	<td>Taxon</td>
	{section name=j loop=$languages}
	<td style="text-align:right; width:75px">
		{if $languages[j].active=='n'}({/if}{$languages[j].language}{if $languages[j].def_language==1} *{/if}{if $languages[j].active=='n'}){/if}
	</td>
	{/section}	

</tr>
{section name=i loop=$taxa}
<tr>
	<td>
		<a href="edit.php?id={$taxa[i].id}">{$taxa[i].taxon}</a>
	</td>
	{assign var=t value=$taxa[i].id}
	{section name=j loop=$languages}
	<td style="text-align:right">
		<a href="edit.php?id={$taxa[i].id}&lan={$languages[j].language_id}">{$languages[j].size[$t]}</a>
	</td>
	{/section}	
</tr>
{/section}
</table>
<br />
{if $languages|@count==0}
You have to define at least one language in your project before you can add any taxa.<br />
<a href="../projects/data.php">Define languages.</a>
{else}
<a href="add.php">Add a new taxon</a>
{/if}
</div>

{include file="../shared/admin-footer.tpl"}

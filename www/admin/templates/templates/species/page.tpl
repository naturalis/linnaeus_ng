{include file="../shared/admin-header.tpl"}

<div id="page-main">


<span id="message-container" style="float:right;"></span>
<br />

<div id="taxon-pages-table-div">
<table id="taxon-pages-table">
	<tr>
		<td style="width:150px"></td>
{section name=i loop=$languages}
		<td>
			{if $languages[i].active=='n'}({/if}{$languages[i].language}{if $languages[i].def_language=='1'} *{/if}{if $languages[i].active=='n'}){/if}
		</td>
{/section}
	</tr>
{section name=i loop=$pages}
	<tr>
		<td>
			{$pages[i].page}
		</td>
	{section name=j loop=$languages}
	{assign var=n value=$languages[j].language_id}
		<td>
			<input 
				type="text" 
				maxlength="32" 
				id="name-{$pages[i].id}-{$languages[j].language_id}" 
				onfocus="taxonSetActivePageTitle([{$pages[i].id},{$languages[j].language_id}])" 
				onblur="taxonPageTitleSave([{$pages[i].id},{$languages[j].language_id}])" 
				value="{$pages[i].page_titles[$n]}" 
			/>
		</td>
	{/section}
		<td class="cell-page-delete" onclick="taxonPageDelete({$pages[i].id},'{$pages[i].page}');"></td>
{/section}
	</tr>
</table>

<br />
{if $languages|@count==0}
You have to define at least one language in your project before you can add any pages.<br />
<a href="../projects/data.php">Define languages.</a>
{else}
<form method="post" action="" id="theForm">
{if $pages|@count<$maxSubPages}
Add a new page:
<input type="text" maxlength="32" id="new_page" name="new_page" value="" />
<input type="hidden" name="rnd" value="{$rnd}" />
<input type="hidden" name="show_order" value="{$nextShowOrder}" />
<input type="submit" value="save" />
{/if}
</form>
{/if}

</div>

</div>
{include file="../shared/admin-messages.tpl"}

{literal}
<script type="text/JavaScript">
$(window).unload( function () { taxonPageTitleSave(taxonActivePageTitle); } );
</script>
{/literal}


{include file="../shared/admin-footer.tpl"}

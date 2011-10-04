{include file="../shared/admin-header.tpl"}

<div id="page-main">

<p>
	<input type="button" value="{t}add new reference{/t}" onclick="window.open('../literature/edit.php','_top')" />&nbsp;
	<input type="button" value="{t}main page{/t}" onclick="window.open('taxon.php?id={$taxon.id}','_top')" />
</p>

<table>
	<tr class="tr-highlight">
		<th style="width:200px" onclick="allTableColumnSort('author_both');">{t}authors{/t}</th>
		<th style="width:50px" onclick="allTableColumnSort('year');">{t}year{/t}</th>
		<th style="width:500px">{t}reference{/t}</th>
		<th></th>
	</tr>
{section name=i loop=$refs}
	<tr class="tr-highlight">
		<td>
			{$refs[i].author_first}
			{if $refs[i].multiple_authors==1}{t}et al.{/t}{else}{if $refs[i].author_second!=''}&amp; {$refs[i].author_second}{/if}{/if}</td>
		<td>{$refs[i].year}</td>
		<td>{$refs[i].text|@substr:0:75}{if $refs[i].text|@strlen>75}...{/if}</td>
		<td>[<a href="../literature/edit.php?id={$refs[i].id}">{t}edit{/t}</a>]</td>
	</tr>
{/section}
</table>
<form method="post" action="" name="sortForm" id="sortForm">
<input type="hidden" name="key" id="key" value="{$sortBy.key}" />
<input type="hidden" name="search" value="{$search}"  />
<input type="hidden" name="dir" value="{$sortBy.dir}"  />
</form>
</div>

{literal}
<script type="text/JavaScript">
$(document).ready(function(){
{/literal}
allLookupNavigateOverrideUrl('literature.php?id=%s');
{literal}
});
</script>
{/literal}

{include file="../shared/admin-messages.tpl"}
{include file="../shared/admin-footer.tpl"}

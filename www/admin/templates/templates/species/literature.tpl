{include file="../shared/admin-header.tpl"}

<div id="page-main">
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
		<td>[<a href="edit.php?id={$refs[i].id}">edit</a>]</td>
	</tr>
{/section}
</table>
<p>
[<a href="../literature/edit.php">add new reference</a>]
</p>
<input type="button" value="{t}back{/t}" onclick="window.open('list.php','_top')" />

<form method="post" action="" name="sortForm" id="sortForm">
<input type="hidden" name="key" id="key" value="{$sortBy.key}" />
<input type="hidden" name="search" value="{$search}"  />
<input type="hidden" name="dir" value="{$sortBy.dir}"  />
</form>
</div>

{include file="../shared/admin-messages.tpl"}
{include file="../shared/admin-footer.tpl"}

{include file="../shared/admin-header.tpl"}

<div id="page-main">
<form method="post" action="" id="theForm">
{t}Search for:{/t}&nbsp;
<input type="text" name="search" value="{$search}" />&nbsp;
<input type="{t}submit{/t}" value="search" />
</form>
<br />
<table>
	<tr class="tr-highlight">
		<th style="width:200px" onclick="allTableColumnSort('term');">{t}term{/t}</th>
		<th style="width:50px" onclick="allTableColumnSort('language');">{t}language{/t}</th>
		<th style="width:500px">{t}definition{/t}</th>
		<th></th>
	</tr>
{section name=i loop=$terms}
	<tr class="tr-highlight">
		<td>{$terms[i].term}</td>
		<td>{$terms[i].language}</td>
		<td>{$terms[i].definition|@substr:0:75}{if $terms[i].definition|@strlen>75}...{/if}</td>
		<td>[<a href="edit.php?id={$terms[i].id}">edit</a>]</td>
	</tr>
{/section}
{if $terms|@count==0 && $search!=''}
	<tr class="tr-highlight">
		<td colspan="4">{t}(nothing found){/t}</td>
	</tr>
{/if}
</table>
<p>
[<a href="edit.php">{t}add new term{/t}</a>]
</p>
<form method="post" action="" name="sortForm" id="sortForm">
<input type="hidden" name="key" id="key" value="{$sortBy.key}" />
<input type="hidden" name="search" value="{$search}"  />
<input type="hidden" name="dir" value="{$sortBy.dir}"  />
</form>
</div>

{include file="../shared/admin-messages.tpl"}
{include file="../shared/admin-footer.tpl"}

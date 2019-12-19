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
		<td>[<a href="edit.php?id={$refs[i].id}">{t}edit{/t}</a>]</td>
	</tr>
{/section}
{if $refs|@count==0 && $search!=''}
	<tr class="tr-highlight">
		<td colspan="4">{t}(nothing found){/t}</td>
	</tr>
{/if}
</table>
<p>
[<a href="edit.php">{t}create new reference{/t}</a>]
[<a href="browse.php">{t}browse{/t}</a>]
</p>
<form method="post" action="" name="sortForm" id="sortForm">
<input type="hidden" name="key" id="key" value="{$sortBy.key}" />
<input type="hidden" name="search" value="{$search}"  />
<input type="hidden" name="dir" value="{$sortBy.dir}"  />
</form>
</div>

{include file="../shared/admin-messages.tpl"}
{include file="../shared/admin-footer.tpl"}

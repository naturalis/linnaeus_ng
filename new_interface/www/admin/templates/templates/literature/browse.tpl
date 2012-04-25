{include file="../shared/admin-header.tpl"}

{if $alpha|@count>0}
<div id="alphabet">
{t}Click to browse:{/t}
{section name=i loop=$alpha}
{if $alpha[i]==$letter}
<span class="alphabet-active-letter">{$alpha[i]}</span>
{else}
<span class="alphabet-letter" onclick="$('#letter').val('{$alpha[i]}');$('#theForm').submit();">{$alpha[i]}</span>
{/if}
{/section}
<form name="theForm" id="theForm" method="post" action="">
<input type="hidden" name="letter" id="letter" value="{$letter}"  />
</form>
</div>
{/if}
<div id="page-main">
{if $alpha|@count==0}
{t}(no references have been defined){/t}
{else}
<table>
	<tr>
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
		<td>{$refs[i].year}{$refs[i].suffix}</td>
		<td>{$refs[i].text|@substr:0:75}{if $refs[i].text|@strlen>75}...{/if}</td>
		<td>[<a href="edit.php?id={$refs[i].id}">{t}edit{/t}</a>]</td>
	</tr>
{/section}
</table>
<form method="post" action="" name="sortForm" id="sortForm">
<input type="hidden" name="key" id="key" value="{$sortBy.key}" />
<input type="hidden" name="letter" value="{$letter}"  />
<input type="hidden" name="dir" value="{$sortBy.dir}"  />
</form>
{/if}
<p>
[<a href="edit.php">{t}create new reference{/t}</a>]
[<a href="search.php">{t}search{/t}</a>]
</p>
</div>

{include file="../shared/admin-messages.tpl"}
{include file="../shared/admin-footer.tpl"}

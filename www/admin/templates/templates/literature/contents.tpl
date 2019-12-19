{include file="../shared/admin-header.tpl"}

<div id="page-main">
{if $alpha|@count==0}
{t}(no references have been defined){/t}
{else}
<table>
	<tr>
		<th style="width:200px" {*onclick="allTableColumnSort('author_both');"*}>{t}authors{/t}</th>
		<th style="width:75px" {*onclick="allTableColumnSort('year');"*}>{t}year{/t}</th>
		<th style="width:500px">{t}reference{/t}</th>
	</tr>
	{section name=i loop=$refs}
	<tr class="tr-highlight" style="vertical-align:top;">
		<td><a href="edit.php?id={$refs[i].id}">
			{$refs[i].author_first}
			{if $refs[i].multiple_authors==1}{t}et al.{/t}{else}{if $refs[i].author_second!=''}&amp; {$refs[i].author_second}{/if}{/if}
			</a>
		</td>
		<td>{$refs[i].year_full}</td>
		<td>{$refs[i].text|@strip_tags:substr:0:75}{if $refs[i].text|@strlen>75}...{/if}</td>
	</tr>
	{/section}
</table>
<form method="post" action="" name="sortForm" id="sortForm">
<input type="hidden" name="key" id="key" value="{$sortBy.key}" />
<input type="hidden" name="letter" value="{$letter}"  />
<input type="hidden" name="dir" value="{$sortBy.dir}"  />
</form>
{/if}
</div>

{include file="../shared/admin-messages.tpl"}
{include file="../shared/admin-footer.tpl"}
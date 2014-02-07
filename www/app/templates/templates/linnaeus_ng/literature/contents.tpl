{include file="../shared/header.tpl"}
{include file="_alphabet.tpl"}

<div id="page-main">
{if $alpha|@count==0}
{t}(no references have been defined){/t}
{else}
<table>
	<tr>
		<th style="width:200px">{t}authors{/t}</th>
		<th style="width:75px;text-align:right;padding-right:10px;">{t}year{/t}</th>
		<th style="width:500px">{t}reference{/t}</th>
	</tr>
	{section name=i loop=$refs}
	<tr class="tr-highlight" style="vertical-align:top;">
		<td><a href="reference.php?id={$refs[i].id}">
			{$refs[i].author_first}
			{if $refs[i].multiple_authors==1}{t}et al.{/t}{else}{if $refs[i].author_second!=''}&amp; {$refs[i].author_second}{/if}{/if}
			</a>
		</td>
		<td style="text-align:right;padding-right:10px;">{$refs[i].year_full}</td>
		<td>{$refs[i].text|@strip_tags:substr:0:50}{if $refs[i].text|@strlen>50}...{/if}</td>
	</tr>
	{/section}
</table>
{/if}
</div>

{include file="../shared/footer.tpl"}
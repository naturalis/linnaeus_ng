{include file="../shared/header.tpl"}

<div id="header-titles">
	<span id="header-title">
		{t}Literature:{/t}<span class="alphabet-letter-title">{$letter}</span>
	</span>
</div>

<div id="page-main" class="template-content">
	{include file="_alphabet.tpl"}
	{if $alpha|@count==0}
		{t}(no references have been defined){/t}
	{else}
		<div id="content">
			<table>
				<tr>
					<th style="width:200px">{t}authors{/t}</th>
					<th style="width:75px;text-align:right;padding-right:10px;">{t}year{/t}</th>
					<th style="width:500px">{t}reference{/t}</th>
				</tr>
				{foreach $refs v}
				<tr class="tr-highlight" style="vertical-align:top;">
					<td><a href="reference.php?id={$v.id}">
						{$v.author_first}
						{if $v.multiple_authors==1}{t}et al.{/t}{else}{if $v.author_second!=''}&amp; {$v.author_second}{/if}{/if}
						</a>
					</td>
					<td style="text-align:right;padding-right:10px;">{$v.year_full}</td>
					<td>{$v.text|@strip_tags:substr:0:50}{if $v.text|@strlen>50}...{/if}</td>
				</tr>
				{/foreach}
			</table>
		</div>
	{/if}
</div>

{include file="../shared/footer.tpl"}
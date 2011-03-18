{include file="../shared/header.tpl"}

{if $alpha}
<div id="alphabet">
	{foreach from=$alpha key=k item=v}
	{if $letter==$v}
	<span class="letter-active">{$v}</span>
	{else}
	<span class="letter" onclick="goAlpha('{$v}')">{$v}</span>
	{/if}
	{/foreach}
</div>
{/if}

<div id="page-main">
<table id="references">
<thead> 
	<tr>
		<th id="th-author">{t}author(s){/t}</th>
		<th id="th-year">{t}year of publication{/t}</th>
	</tr>
</thead>
<tbody>
{foreach from=$refs key=k item=v}

	<tr class="highlight">
		<td class="a" onclick="goLiterature({$v.id})">
			{$v.author_full}
		</td>
		<td>
			{$v.year}
		</td>
	</tr>
{/foreach}
</tbody>
</table>
</div>

{include file="../shared/footer.tpl"}

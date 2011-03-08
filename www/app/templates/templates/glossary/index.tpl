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
{if $gloss}
<table id="index">
<thead> 
	<tr>
		<th id="th-term">{t}term{/t}</th>
		<th id="th-definition">{t}synonyms{/t}</th>
	</tr>
</thead>
<tbody>
{foreach from=$gloss key=k item=v}
	<tr class="highlight">
		<td class="a" onclick="goGlossaryTerm({$v.id})">{$v.term}</td>
		<td>
		{foreach from=$v.synonyms key=k2 item=v2}
			{$v2.synonym}{if $v.synonyms|@count>1 && $k2!=$v.synonyms|@count-1}, {/if}
		{/foreach}
		</td>
	</tr>
{/foreach}
</tbody>
</table>
{else}
{t}No glossary terms have been defined in this language.{/t}
{/if}
</div>

{include file="../shared/footer.tpl"}

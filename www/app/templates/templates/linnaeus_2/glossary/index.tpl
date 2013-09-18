{include file="../shared/header.tpl"}

{if $alpha}
{include file="_alphabet.tpl"}
{/if}

<div id="page-main">
{if !$gloss}
{t}No glossary has been defined.{/t}
{else}
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
	{if $useJavascriptLinks}
		<td class="a" onclick="goGlossaryTerm({$v.id})">{$v.term}</td>
	{else}
		<td>
			<a href="../glossary/term.php?id={$v.id}">{$v.term}</a>
		</td>
	{/if}
		<td>
		{foreach from=$v.synonyms key=k2 item=v2}
			{$v2.synonym}{if $v.synonyms|@count>1 && $k2!=$v.synonyms|@count-1}, {/if}
		{/foreach}
		</td>
	</tr>
{/foreach}
</tbody>
</table>
{/if}
</div>

{include file="../shared/footer.tpl"}

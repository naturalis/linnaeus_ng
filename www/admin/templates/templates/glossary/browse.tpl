{include file="../shared/admin-header.tpl"}

<div id="alphabet">
Click to browse:
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

<div id="page-main">

<select name="language_id" id="language">
{section name=i loop=$languages}
	{if $languages[i].language!=''}<option value="{$languages[i].id}" {if $languages[i].language_id==$gloss.language_id}selected="selected"{/if}>{$languages[i].language}{if $languages[i].language_id==$defaultLanguage} *{/if}</option>{/if}
{/section}
</select> *


<table>
	<tr class="tr-highlight">
		<th style="width:200px" onclick="allTableColumnSort('author_both');">{t}authors{/t}</th>
		<th style="width:500px">{t}definition{/t}</th>
		<th></th>
	</tr>
{section name=i loop=$gloss}
	<tr class="tr-highlight">
		<td>{$gloss[i].term}</td>
		<td>{$gloss[i].definition|@substr:0:75}{if $gloss[i].definition|@strlen>75}...{/if}</td>
		<td>[<a href="edit.php?id={$gloss[i].id}">edit</a>]</td>
	</tr>
{/section}
</table>
<form method="post" action="" name="sortForm" id="sortForm">
<input type="hidden" name="key" id="key" value="{$sortBy.key}" />
<input type="hidden" name="letter" value="{$letter}"  />
<input type="hidden" name="dir" value="{$sortBy.dir}"  />
</form>
<p>
[<a href="edit.php">add new term</a>]
</p>
</div>

{include file="../shared/admin-messages.tpl"}
{include file="../shared/admin-footer.tpl"}

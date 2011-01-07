{include file="../shared/admin-header.tpl"}

<form name="theForm" id="theForm" method="post" action="">
<div id="alphabet">

{t}See glossary items in:{/t}&nbsp;
<select name="activeLanguage" id="language" onchange="$('#theForm').submit();">
{section name=i loop=$languages}
	{if $languages[i].language!=''}<option value="{$languages[i].id}" {if $languages[i].id==$activeLanguage}selected="selected"{/if}>{$languages[i].language}{if $languages[i].language_id==$defaultLanguage} *{/if}</option>
	{/if}
{/section}
</select>

<br />
{t}Click to browse:{/t}&nbsp;
{section name=i loop=$alpha}
{if $alpha[i]==$letter}
<span class="alphabet-active-letter">{$alpha[i]}</span>
{else}
<span class="alphabet-letter" onclick="$('#letter').val('{$alpha[i]}');$('#theForm').submit();">{$alpha[i]}</span>
{/if}
{/section}
{if $alpha|@count==0}(no terms have been defined){/if}
<input type="hidden" name="letter" id="letter" value="{$letter}"  />
</form>
</div>

<div id="page-main">


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
[<span class="pseudo-a" onclick="$('#newForm').submit();">add new term</span>]
</p>
<form method="post" action="edit.php" name="newForm" id="newForm">
<input type="hidden" name="activeLanguage" value="{$activeLanguage}"  />
</form>
</div>

{include file="../shared/admin-messages.tpl"}
{include file="../shared/admin-footer.tpl"}

{include file="../shared/admin-header.tpl"}

<form name="theForm" id="theForm" method="post" action="">

{if $languages|@count > 1}
<div id="alphabet">
{t}See glossary items in:{/t}&nbsp;
<select name="activeLanguage" id="language" onchange="$('#theForm').submit();">
{section name=i loop=$languages}
	{if $languages[i].language!=''}<option value="{$languages[i].language_id}" {if $languages[i].language_id==$activeLanguage}selected="selected"{/if}>{$languages[i].language}{if $languages[i].language_id==$defaultLanguage} *{/if}</option>
	{/if}
{/section}
</select>
</div>
{/if}

<div id="page-main">
{if $alpha|@count>0}
<table>
{foreach $gloss v i}
	<tr class="tr-highlight">
		<td><a href="edit.php?id={$v.id}">{$v.term}</a></td>
	</tr>
{/foreach}
</table>

{if $prevStart!=-1 || $nextStart!=-1}
<div id="navigation">
	{if $prevStart!=-1}
	<span class="a" onclick="goNavigate({$prevStart});">< {t}previous{/t}</span>
	{/if}
	{if $nextStart!=-1}
	<span class="a" onclick="goNavigate({$nextStart});">{t}next{/t} ></span>
	{/if}
</div>
{/if}
{/if}
</div>
<form action="" method="post" id="theForm" action="">
<input type="hidden" name="letter" id="letter" value="" />
</form>
{include file="../shared/admin-messages.tpl"}
{include file="../shared/admin-footer.tpl"}

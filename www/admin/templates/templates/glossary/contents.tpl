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
	<tr>
		<th style="width:175px">{t}term{/t}</th>
		<!-- th style="width:450px">{t}definition{/t}</th -->
	</tr>
{section name=i loop=$gloss}
	<tr class="tr-highlight">
		<td><a href="edit.php?id={$gloss[i].id}">{$gloss[i].term}</a></td>
		<!-- td>{$gloss[i].definition|@strip_tags:substr:0:100}{if $gloss[i].definition|@strlen>100}...{/if}</td -->
	</tr>
{/section}
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

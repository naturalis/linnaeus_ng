{include file="../shared/admin-header.tpl"}

<form name="theForm" id="theForm" method="post" action="">
<div id="alphabet">

{t}See glossary items in:{/t}&nbsp;
<select name="activeLanguage" id="language" onchange="$('#theForm').submit();">
{section name=i loop=$languages}
	{if $languages[i].language!=''}<option value="{$languages[i].language_id}" {if $languages[i].language_id==$activeLanguage}selected="selected"{/if}>{$languages[i].language}{if $languages[i].language_id==$defaultLanguage} *{/if}</option>
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
	<tr>
		<th style="width:175px">{t}term{/t}</th>
		<th style="width:350px">{t}definition{/t}</th>
		<th>{t}synonyms{/t}</th>
		<th>{t}media{/t}</th>
		<th></th>
	</tr>
{section name=i loop=$gloss}
	<tr class="tr-highlight">
		<td>{$gloss[i].term}</td>
		<td>{$gloss[i].definition|@substr:0:50}{if $gloss[i].definition|@strlen>50}...{/if}</td>
		<td style="text-align:right;padding-right:7px;">{$gloss[i].synonyms|@count}</td>
		<td style="text-align:right;padding-right:7px;">{$gloss[i].media|@count}</td>
		<td>[<a href="edit.php?id={$gloss[i].id}">edit</a>]</td>
	</tr>
{/section}
</table>


{if $prevStart!=-1 || $nextStart!=-1}
<div id="navigation">
	{if $prevStart!=-1}
	<span class="pseudo-a" onclick="goNavigate({$prevStart});">< {t}previous{/t}</span>
	{/if}
	{if $nextStart!=-1}
	<span class="pseudo-a" onclick="goNavigate({$nextStart});">{t}next{/t} ></span>
	{/if}
</div>
{/if}

<p>
[<span class="pseudo-a" onclick="$('#newForm').submit();">{t}add new term{/t}</span>]
</p>
<form method="post" action="edit.php" name="newForm" id="newForm">
<input type="hidden" name="activeLanguage" value="{$activeLanguage}"  />
</form>
</div>

{include file="../shared/admin-messages.tpl"}
{include file="../shared/admin-footer.tpl"}

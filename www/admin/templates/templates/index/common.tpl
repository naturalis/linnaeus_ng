{include file="../shared/admin-header.tpl"}

<div id="page-main">


{if $languages|@count > 1}
	<tr>
		<td>{t}Language:{/t}</td>
		<td>
			<select name="language_id" id="language">
			{foreach name=languageloop from=$languages key=k item=v}
				{if $v.language!=''}<option value="{$languages[i].language_id}"{if $v[i].language_id==$activeLanguage} selected="selected"{/if}>{$v.language}</option>
				{/if}
			{/foreach}
			</select>
		</td>
	</tr>
{/if}


	<div id="index">
		<table>
		{foreach name=taxonloop from=$taxa key=k item=v}
		<tr class="highlight">
			<td class="a" onclick="goTaxon({$v.id})">
				{if $v.label}{$v.label}{else}{$v.transliteration}{/if}
			</td>
			<td>({$languages[$v.language_id].language})</td>
		</tr>
		{/foreach}
		</table>
	</div>
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
</div>
<form name="theForm" id="theForm" method="post" action="">
</form>

{include file="../shared/admin-messages.tpl"}
{include file="../shared/admin-footer.tpl"}

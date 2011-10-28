{include file="../shared/admin-header.tpl"}

<div id="page-main">

	<div class="page-generic-div">
		<form method="post" action="" id="languageForm">
			{t}Language:{/t}
			<select name="activeLanguage" id="activeLanguage" onchange="$('#languageForm').submit();">
			<option value="*"{if $activeLanguage=='*'} selected="selected"{/if}>{t}show all{/t}</option>
			<option disabled="disabled">-----------------------</option>
			{foreach name=languageloop from=$languages key=k item=v}
			<option value="{$v.id}"{if $v.id==$activeLanguage} selected="selected"{/if}>{$v.language}</option>
			{/foreach}
			</select>
		</form>
	</div>

	<div class="page-generic-div">
		<table>
		{foreach name=taxonloop from=$taxa key=k item=v}
		<tr class="highlight">
			<td>
				<a href="../species/common.php?id={$v.id}">
				{if $v.label}{$v.label}{else}{$v.transliteration}{/if}
				</a>
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

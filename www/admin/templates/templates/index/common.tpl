{include file="../shared/admin-header.tpl"}

<div id="page-main">

	<div id="alphabet">
	{if $alpha|@count!=0}
	{t}Click to browse:{/t}&nbsp;
	{foreach name=loop from=$alpha key=k item=v}
	{if $v==$letter}
	<span class="alphabet-active-letter">{$v}</span>
	{else}
	<span class="alphabet-letter" onclick="$('#letter').val('{$v}');$('#theForm').submit();">{$v}</span>
	{/if}
	{/foreach}
	{/if}
	</div>

	<div class="page-generic-div">
		<form>
			{t}Language:{/t}
			<select id="languageSelect" onchange="
				$('#activeLanguage').val($('#languageSelect').val());
				$('#letter').val('');
				$('#theForm').submit();"
			>
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
				{$v.label}
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
		<span class="a" onclick="goNavigate({$prevStart});">< {t}previous{/t}</span>
		{/if}
		{if $nextStart!=-1}
		<span class="a" onclick="goNavigate({$nextStart});">{t}next{/t} ></span>
		{/if}
	</div>
{/if}
</div>
<form name="theForm" id="theForm" method="get" action="">
<input type="hidden" id="letter" name="letter" value="{$letter}" />
<input type="hidden" id="activeLanguage" name="activeLanguage" value="{$activeLanguage}" />
</form>

{include file="../shared/admin-messages.tpl"}
{include file="../shared/admin-footer.tpl"}

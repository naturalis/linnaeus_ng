{include file="../shared/admin-header.tpl"}

<div id="page-main">
<!-- select name="lan">
{foreach from=$uiLanguages item=v}
<option value="{$v.id}">{$v.language}</option>
{/foreach}
</select -->
<table>
<tr><th>{t}identifier (and English translation){/t}</th><th colspan=2>{t _s1='Dutch'}translation in %s{/t}</th></tr>
{foreach from=$texts item=v key=n}
<tr class="tr-highlight">
	<td style="width:650px;vertical-align:top">{$v.text}</td>
	<td 
		id="trans-{$v.id}-{$v.translation_language_id}" 
		counter="{$n}" 
		onclick="interfaceEnableTransEdit(this);"
		{if !$v.translation}style="background-color:#eee;cursor:pointer"
		{else}style="cursor:pointer"
		{/if}
		>{$v.translation}</td>
		<td id="msg-{$n}"></td>
	</tr>
{/foreach}
</table>
		
		{if $prevStart!=-1 || $nextStart!=-1}
			<div id="navigation">
				{if $prevStart!=-1}
				<span class="a" onclick="goNavigate({$prevStart});">< previous</span>
				{/if}
				{if $nextStart!=-1}
				<span class="a" onclick="goNavigate({$nextStart});">next ></span>
				{/if}
			</div>
		{/if}
		
</div>
<form action="" method="post" id="theForm" action="">
</form>

{include file="../shared/admin-footer.tpl"}

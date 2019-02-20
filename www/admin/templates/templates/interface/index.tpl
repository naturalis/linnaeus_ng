{include file="../shared/admin-header.tpl"}
<div id="page-main">
<p>
<form method="post" id="theForm">
Environment:
<select name="env" onchange="$('#theForm').submit()">
{foreach from=$envs item=i}
<option value="{$i}"{if $i==$env} selected="selected"{/if}>{$i}</option>
{/foreach}
</select>
Language:
<select name="lan" onchange="$('#theForm').submit()">
{foreach from=$translationLanguages item=v key=k}
<option value="{$v.id}"{if $v.id==$lan} selected="selected"{/if}>{$v.language}</option>
{if $v.id==$lan}{assign var=activeLanKey value=$k}{/if}
{/foreach}
</select>
</form>
</p>
{if $isOriginalLanguage}
<p>
	{t _s1=$translationLanguages[$activeLanKey].language}As the original tags are in %s, they do not require translating, but if you do specify a translation, it will overrule the original tag.{/t}
</p>
{/if}
<table>
<tr>
	<th>{t}identifier{/t}</th>
	<th colspan=2>
		{t _s1=$translationLanguages[$activeLanKey].language}translation in %s{/t}
	</th>
</tr>
{foreach from=$texts item=v key=n}
<tr id="row-{$v.id}" class="tr-highlight">
	<td style="width:550px;vertical-align:top;border-bottom:1px dotted #eee">{$v.text}</td>
	<td style="width:250px;vertical-align:top;border-bottom:1px dotted #eee" 	
		id="trans-{$v.id}-{$v.translation_language_id}" 
		counter="{$n}" 
		onclick="interfaceEnableTransEdit(this);"
		{if !$v.translation}style="background-color:#eee;cursor:pointer"
		{else}style="cursor:pointer"
		{/if}
		>{$v.translation}</td>
		<td style="border-bottom:1px dotted #eee;cursor:pointer;color:red;padding:0px 2px 0px 2px;" onclick="interfaceDeleteTag({$v.id});" title="{t}Delete tag and all its translations!{/t}">x</td>
		<td id="msg-{$n}"></td>
	</tr>
{/foreach}
</table>
		
{if $prevStart!=-1 || $nextStart!=-1}
	<div id="navigation">
		<input type="hidden" id="currStart" value="{$currStart}" />
		{if $prevStart!=-1}
		<span class="a" onclick="goNavigate({$prevStart});">< previous</span>
		{/if}
		{if $nextStart!=-1}
		<span class="a" onclick="goNavigate({$nextStart});">next ></span>
		{/if}
	</div>
{/if}
		
</div>

<script>

{literal}	
$(document).ready(function(){
{/literal}
	interfaceFinalCounter = {$n}; 
	interfaceNextStart = {$nextStart};
	{if $immediateEdit}interfaceEnableTransEdit($('[counter=0]'));
	{/if}
{literal}	
});
{/literal}	

</script>

{include file="../shared/admin-footer.tpl"}

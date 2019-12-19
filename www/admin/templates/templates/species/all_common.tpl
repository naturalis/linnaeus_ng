{include file="../shared/admin-header.tpl"}
<div id="page-main">
{if $commonnames|@count==0}
{t}No common names have been defined for this taxon.{/t}
{else}
<span class="message-error">Be aware: clicking the delete button immediately deletes the synonym, without confirmation.</span>
<table>
	<tr>
		<th style="width:10px" title="{t}corresponding taxon{/t}">taxon</th>
		<th style="width:150px;">{t}common name{/t}</th>
		<th style="width:150px;">{t}transliteration{/t}</th>
		<th style="width:100px;">{t}language{/t}</th>
		<th>delete</th>
	</tr>
	{foreach from=$commonnames item=v}
	<tr class="tr-highlight"  id="com-{$v.id}">
		<td style="white-space:nowrap"><a href="common.php?id={$v.taxon_id}" style="color:#777">{$v.taxon}</a></td>
		<td>{$v.commonname}</td>
		<td>{$v.transliteration}</td>
		<td>{$v.language_name}</td>
		<td style="text-align:center" class="a" onclick="taxonEasyCommonDelete({$v.id},'delete');">x</td>
	</tr>
	{/foreach}
</table>
{/if}
</div>

{include file="../shared/admin-messages.tpl"}

<script type="text/javascript">
{literal}
$(document).ready(function(){
{/literal}
allLookupNavigateOverrideUrl('common.php?id=%s');
{literal}
});
{/literal}
</script>

{include file="../shared/admin-footer.tpl"}
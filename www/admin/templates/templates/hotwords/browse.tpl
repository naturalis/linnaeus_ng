{include file="../shared/admin-header.tpl"}

<div id="page-main">
<p>
<b>{if $controller}{t _s1=$controller}Hotwords in %s module{/t}{else}{t}All hotwords{/t}{/if} ({$num})</b> <span title="delete all" class="a" onclick="hotwordsDelete();">x</span>
</p>
{foreach item=v from=$hotwords}
{$v.hotword} <span title="delete" class="a" onclick="hotwordsDelete('{$v.id}');">x</span><br />
{/foreach}

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
<form id="theForm" method="post">
<input type="hidden" id="id" name="id" value="" />
<input type="hidden" id="action" name="action" value="" />
<input type="hidden" id="c" name="c" value="{$controller}" />
</form>


{include file="../shared/admin-footer.tpl"}
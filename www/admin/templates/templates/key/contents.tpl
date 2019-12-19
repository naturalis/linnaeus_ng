{include file="../shared/admin-header.tpl"}

<div id="page-main">

<p>
</p>
{foreach item=v from=$list}
<a href="step_show.php?node={$v.node}">{$v.number}: {$v.label}</a><br />
{/foreach}

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

<form id="theForm" method="post">
<input type="hidden" id="id" name="id" value="" />
<input type="hidden" id="action" name="action" value="" />
<input type="hidden" id="c" name="c" value="{$controller}" />
</form>

{include file="../shared/admin-footer.tpl"}

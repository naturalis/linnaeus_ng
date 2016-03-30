{include file="../shared/admin-header.tpl"}

{include file="../shared/admin-messages.tpl"}

<div id="page-main">

	{if $totals.converted == 0}

	<p>Convert {$totals.total} local media files to the ResourceSpace infrastructure:</p>

	<ul id="file-list">
	{foreach from=$totals.modules key=module item=count}
		{if $count > 0}
		<li>{$module}: {$count}</li>
		{/if}
	{/foreach}
	</ul>

	{else}

	<p>A total of {$totals.converted} media files was converted previously.</p>

	{/if}

<p style="margin-top: 25px;">
<form id="theForm" action="../../views/media/conversion_progress.php?start" target="hidden">
<input type="hidden" name="action" value="convert">
</form>
<input id="submit" type="button" value="{if $totals.converted == 0}{t}start{/t}{else}{t}continue{/t}{/if}" />
</p>


<iframe id="conversion_progress" width="90%" name="hidden" scrolling="auto" frameborder="0"
	style="margin-top: 25px;" height="500" scr="../../views/media/conversion_progress.php">
</iframe>

</div>

{literal}
<script type="text/JavaScript">
$(document).ready(function() {
	$("#submit").click(function() {
		$("#theForm").submit();
	});
});
</script>
{/literal}

{include file="../shared/admin-footer.tpl"}



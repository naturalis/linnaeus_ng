{include file="../shared/admin-header.tpl"}
{include file="../shared/admin-messages.tpl"}

<div id="page-main">

<ul>
	<li><a href="update.php">{t}Update hotwords{/t}</a></li>
	<li><a href="browse.php">{t}Browse all hotwords{/t}</a> (<span id="grandTot"></span>)</li>
	<li><span class="a" onclick="hotwordsDelete('*');">{t}Delete all hotwords{/t}</span></li>
    {assign var=grandTot value=0}
	{foreach item=v from=$controllers}
	<li><a href="browse.php?c={$v.controller}">{t}Browse hotwords in {/t}{$v.controller}</a> ({$v.tot})</li>
    {assign var=grandTot value=$grandTot+$v.tot}
	{/foreach}
</ul>
<form id="theForm" method="post">
<input type="hidden" id="id" name="id" value="" />
<input type="hidden" id="action" name="action" value="" />
</form>
</div>

<script>
$('#grandTot').html('{$grandTot}');
</script>

{include file="../shared/admin-footer.tpl"}
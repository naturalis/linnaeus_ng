{include file="../shared/admin-header.tpl"}
{include file="../shared/admin-messages.tpl"}

<div id="page-main">

    <h3>Hotwords</h3>
    
    <p>
    Hotwords are words that are automatically linked to a corresponding module and topic.
    </p>
    
    <ul>
        <li><a href="update.php">{t}Update hotwords{/t}</a></li>
        <li><a href="browse.php">{t}Browse all hotwords{/t}</a> (<span id="grandTot"></span>)</li>
        <li><span class="a" onclick="hotwordsDelete('*');">{t}Delete all hotwords{/t}</span></li>
    </ul>
    
    {assign var=grandTot value=0}
    {if $controllers|@count>0}
    {t}Browse hotwords in:{/t}
    <ul>
        {foreach $controllers v}
        <li><a href="browse.php?c={$v.controller}">{$v.controller}</a> ({$v.tot})</li>
        {assign var=grandTot value=$grandTot+$v.tot}
        {/foreach}
    </ul>
    {/if}

</div>

<script>
$(document).ready(function(e)
{
	$('#grandTot').html('{$grandTot}');    
});
</script>

{include file="../shared/admin-footer.tpl"}
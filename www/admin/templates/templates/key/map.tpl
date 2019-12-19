{include file="../shared/admin-header.tpl"}

<div id="page-main">

    <p>
    {t}Below is a graphic representation of your key. Click a node to see the steps that follow from it. Click and drag to move the entire tree.{/t}
    </p>
    <div id="center-container">
        <div id="info"></div>
        <div id="infovis"></div>    
    </div>
    
    <div id="log"></div>

</div>

<script type="text/JavaScript">
$(document).ready(function()
{
	keyTreeInit({$json});
});
</script>

{include file="../shared/admin-footer.tpl"}

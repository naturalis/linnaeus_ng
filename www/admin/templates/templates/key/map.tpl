{include file="../shared/admin-header.tpl"}

<div id="page-main">

<div id="center-container">
    <div id="infovis"></div>    
</div>

<div id="log"></div>

{literal}
<script type="text/JavaScript">
$(document).ready(function(){
{/literal}
	init({$json});
{literal}
});
</script>
{/literal}

{include file="../shared/admin-footer.tpl"}

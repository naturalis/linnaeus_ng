{include file="../shared/admin-header.tpl"}

<div id="page-main">

{include file="_add_edit_body.tpl"}

</div>

{literal}
<script type="text/JavaScript">
$(document).ready(function(){
	activeLanguage = {/literal}{$activeLanguage}{literal};
});
</script>
{/literal}


{include file="../shared/admin-footer.tpl"}

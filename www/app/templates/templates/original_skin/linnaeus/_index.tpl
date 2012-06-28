<div id="page-main">
	{include file="_navigator-menu.tpl"}
	<div id="general-content">
	{$content}
	</div>
</div>
{literal}
<script type="text/JavaScript">
$(document).ready(function(){
	allLookupContentOverrideUrl('../search/ajax_interface.php');
});
</script>
{/literal}

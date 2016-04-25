<style>

#body-container {
	width:1500px;
}

#yggdrasil {
	border-right:1px dashed #ddd;
	width:500px;
	min-height:1000px;
	float:left;
	margin-right:10px;
}
#page-main  {
	width:750px;
}

#page-main, #page-block-warnings, .page-generic-div {
	float:left;
}

#footer-container {
	display:none;
}
</style>


<div id="yggdrasil">


</div>

<script>
$(document).ready(function()
{
	{if $noautoexpand}
	$('#yggdrasil').load( '/linnaeus_ng/admin/views/nsr/tree.php' );	
	{else}
	$('#yggdrasil').load( '/linnaeus_ng/admin/views/nsr/tree.php?node=' + {$concept.id} );	
	{/if}
});
</script>


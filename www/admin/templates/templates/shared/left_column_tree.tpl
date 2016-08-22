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

#page-container-div {
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
	$('#yggdrasil').load( '/linnaeus_ng/admin/views/nsr/tree.php', function() { embedAdminMenu(); } );
	{else}
	$('#yggdrasil').load( '/linnaeus_ng/admin/views/nsr/tree.php?node=' + {$concept.id}, function() { embedAdminMenu(); } );
	{/if}
	
	
	function embedAdminMenu()
	{
		var top=$( '#admin-menu-top' ).html();
		var bottom=$( '#admin-menu-bottom' ).html();
		
		if (top)
			$( '#menu-container-top' ).html(top);
		else
			$( '.menu-container-top' ).toggle(false);

		if (bottom)
			$( '#menu-container-bottom' ).html(bottom);
		else
			$( '.menu-container-bottom' ).toggle(false);
	}
	
//	menu-container
});
</script>


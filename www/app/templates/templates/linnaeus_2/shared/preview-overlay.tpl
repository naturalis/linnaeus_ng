<div id="preview-overlay">
<!--
<input type="button" value="edit page" onclick="window.open('{$urlBackToAdmin}','_self');" />
-->

<p><a href="{$urlBackToAdmin}">EDIT PAGE</a></p>
</div>


{literal}
<script type="text/JavaScript">
$(document).ready(function(){
{/literal}

	var pos = $('#body-container').position();
	$('#preview-overlay').css('left',(pos.left + $('#body-container').width() - $('#preview-overlay').width()));

{literal}

	
});
</script>
{/literal}

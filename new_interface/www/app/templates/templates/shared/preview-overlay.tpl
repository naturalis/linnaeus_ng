<div id="preview-overlay" style="
	position:absolute;
	top:10px;
	left:0px;
	width:500px;
	height:150px;
	color:#aaa;
	text-align:right;
"
>
<input type="button" value="edit page" onclick="window.open('{$urlBackToAdmin}','_self');" />
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

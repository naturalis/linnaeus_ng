{include file="../shared/admin-header.tpl"}

{literal}
 <style>
#sortable { list-style-type: none; margin: 0; padding: 0; width: 60%; }
#sortable li { margin: 0 3px 3px 3px; padding: 0.4em; padding-left: 1.5em; height: 18px; width:auto;}
</style>
{/literal}

<div id="page-main">
<p>
{t _s1=$matrix.matrix}Editing matrix "%s"{/t}<br />
{t _s1=$characteristic.label}Sort states of characteristic "%s".{/t}
</p>
Drag and drop to sort, click 'save' to store new order.<br />
Sort all at once: <span class="a" onclick="matrixDoMoveState(-1,'alph');">alphabetically</span> or <span class="a" onclick="matrixDoMoveState(-1,'num');">numerically</span>.
</p>

<ul id="sortable" class="sortable-drag-list">
{foreach item=v key=k from=$states}
<li id="state-{$v.id}" class="ui-state-default">{$v.label}</li>
{/foreach}
</ul>



	<div style="clear:both;border-bottom:1px dotted #ddd;width:500px;margin-bottom:10px"></div>

   
    <form method="post" id="theForm">
    <input type="hidden" name="sId" value="{$characteristic.id}" />
    <input type="hidden" name="id" id="id" value="" />
    <input type="hidden" name="r" id="r" value="" />
    <input type="hidden" name="rnd" value="{$rnd}" />

	<input type="button" value="{t}save{/t}" onclick="matrixSaveStateOrder()" />
	<input type="button" value="{t}back{/t}" onclick="window.open('index.php','_self')" />
</p>
</form>
</div>

{literal}
<script type="text/javascript">
$(document).ready(function(){

	$( "#sortable" ).sortable({
		opacity: 0.6, 
		cursor: 'move',
	}).disableSelection();


});
</script>
{/literal}

{include file="../shared/admin-messages.tpl"}
{include file="../shared/admin-footer.tpl"}

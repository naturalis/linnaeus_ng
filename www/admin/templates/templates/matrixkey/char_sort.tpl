{include file="../shared/admin-header.tpl"}

<style>
#sortable { list-style-type: none; margin: 0; padding: 0; width: 60%; }
#sortable li { margin: 0 3px 3px 3px; padding: 0.4em; padding-left: 1.5em; height: 18px; width:auto;}
</style>

<div id="page-main">

<p>
<span class="matrix-header">
	{t _s1=$matrix.label}Editing matrix "%s"{/t}
</span>
</p>
{t}Sort characters{/t}

{t}Drag and drop to sort, click 'save' to store new order.{/t}<br />

<!-- 
{t}Sort all at once:{/t} <span class="a" onclick="matrixDoMoveState(-1,'alph');">{t}alphabetically{/t}</span> {t}or{/t} <span class="a" onclick="matrixDoMoveState(-1,'num');">{t}numerically{/t}</span>.
-->

<p>

<ul id="sortable" class="sortable-drag-list">
{foreach $characteristics v k}
	<li id="character-{$v.id}" class="ui-state-default">{$v.label} ({$v.type})</li>
{/foreach}
</ul>

<div style="clear:both;border-bottom:1px dotted #ddd;width:500px;margin-bottom:10px"></div>

<form method="post" id="theForm">
<input type="hidden" name="rnd" value="{$rnd}" />
<input type="button" value="{t}save{/t}" onclick="matrixSaveOrder('character')" />
<input type="button" value="{t}back{/t}" onclick="window.open('index.php?id={$matrix.id}','_self')" />
</form>

</p>
</div>

<script type="text/javascript">
$(document).ready(function()
{
	$( "#sortable" ).sortable({
		opacity: 0.6, 
		cursor: 'move',
	}).disableSelection();
});
</script>

{include file="../shared/admin-messages.tpl"}
{include file="../shared/admin-footer.tpl"}

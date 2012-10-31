{include file="../shared/admin-header.tpl"}

<div id="page-main">
<p>
<span class="matrix-header">
	{t _s1=$matrix.matrix}Editing matrix "%s"{/t}
</span>
</p>

{t _s1=$characteristic.label}Sort states of characteristic "%s".{/t}
<form method="post" id="theForm">
<input type="hidden" name="sId" value="{$characteristic.id}" />


<input type="hidden" name="id" id="id" value="" />
<input type="hidden" name="r" id="r" value="" />
<input type="hidden" name="rnd" value="{$rnd}" />
{literal}
<script>
function bla(id,r) {
	$('#id').val(id);
	$('#r').val(r);
	$('#theForm').submit();
}
</script>
{/literal}

<table>
{foreach item=v key=k from=$states}
	<tr>
		<td>{$v.show_order+1}.</td>
		<td>{$v.label}</td>
		<td title="{t}move up{/t}">{if $k>0}<span class="a" onclick="bla({$v.id},'u');">&nbsp;&uarr;&nbsp;</span>{/if}</td>
		<td title="{t}move down{/t}">{if $k<$states|@count-1}<span class="a" onclick="bla({$v.id},'d');">&nbsp;&darr;&nbsp;</span>{/if}</td>
	</tr>
{/foreach}
</table>
<p>
	<input type="button" value="{t}back{/t}" onclick="window.open('index.php','_self')" />
</p>
</form>
</div>

{include file="../shared/admin-messages.tpl"}
{include file="../shared/admin-footer.tpl"}

{include file="../shared/admin-header.tpl"}

<div id="page-main">

<p>This table can be used to change the default rights for types of users for specific modules. Click a square to apply the change. The rows marked * indicate all rights for that module.</p>

<table cellpadding="0" cellspacing="0" class="rights_matrix">
	<tr class="tr-highlight">
		<td>{$v.controller}</td>
		<td class="label">{$v.view}</td>
	{foreach item=v from=$roles}
		<td class="label" style="text-align:center;">{$v.abbrev}</td>
	{/foreach}

	</tr>
{foreach item=v from=$rights}
	<tr class="tr-highlight">
		<td>{$v.controller}</td>
		<td class="label">
			<!--a href="../{$v.controller}/{if $v.view!='*'}{$v.view}.php{/if}"-->{$v.view}<!--/a-->
		</td>
	{foreach item=vv from=$v.roles}
		<td class="label YN" style="
			background-color:{if $vv.state}#0F0;{else}#F44;{/if}"
			id="r-{$v.id}-{$vv.id}"
			onclick="userChangeRoleRight(this);">
		{if $vv.state}Y{else}N{/if}
		</td>
	{/foreach}

	</tr>
{/foreach}
</table>
</div>
<form method="post" action="" id="theForm">
<input type="hidden" name="rnd" value="{$rnd}" />
<input type="hidden" name="right" id="right" value="" />
<input type="hidden" name="role" id="wrong" value="" />
</form>

{include file="../shared/admin-footer.tpl"}
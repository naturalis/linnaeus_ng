{include file="../shared/admin-header.tpl"}

<div id="page-main">
<table cellpadding="0" cellspacing="0">
	<tr class="tr-highlight">
		<td style="border-bottom:1px solid #999;">{$v.controller}</td>
		<td style="border-bottom:1px solid #999;border-right:1px solid #999;">{$v.view}</td>
	{foreach item=v from=$roles}
		<td style="border-bottom:1px solid #999;border-right:1px solid #999;text-align:center;">{$v.abbrev}</td>
	{/foreach}

	</tr>
{foreach item=v from=$rights}
	<tr class="tr-highlight">
		<td style="border-bottom:1px solid #999">{$v.controller}</td>
		<td style="border-bottom:1px solid #999;border-right:1px solid #999;">
			<a href="../{$v.controller}/{if $v.view!='*'}{$v.view}.php{/if}">{$v.view}</a>
		</td>
	{foreach item=vv from=$v.roles}
		<td style="
			border-bottom:1px solid #999;
			border-right:1px solid #999;
			text-align:center;
			cursor:pointer;
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

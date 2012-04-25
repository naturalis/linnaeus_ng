{include file="../shared/admin-header.tpl"}

<div id="page-main">
<form method="post">
Show alphabet in runtime? <label><input name="show_alpha" type="radio" value="1" {if $module.show_alpha==1} checked="checked"{/if}/>yes</label><label><input name="show_alpha" type="radio" value="0" {if $module.show_alpha!=1} checked="checked"{/if} />no</label><br /><br />
<input type="submit" name="submit" value="save" />
</form>
</div>

{include file="../shared/admin-messages.tpl"}
{include file="../shared/admin-footer.tpl"}

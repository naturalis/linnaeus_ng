{include file="../shared/admin-header.tpl"}

<div id="page-main">

<form method="post">
<p>
	Module name: <input type="text" value="{$module.module}" name="module" />
</p>
<p>
	Show alphabet in runtime? <label><input name="show_alpha" type="radio" value="1" {if $module.show_alpha==1} checked="checked"{/if}/>yes</label><label><input name="show_alpha" type="radio" value="0" {if $module.show_alpha!=1} checked="checked"{/if} />no</label>
</p>
<input type="submit" name="submit" value="save" />
</form>

<p>
Other tasks:<br />
<a href="order.php" class="allLookupLink">{t}Change page order{/t}</a><br />
<a href="import.php" class="allLookupLink">{t}Import{/t}</a>
</ul>
</p>


</div>


{include file="../shared/admin-messages.tpl"}
{include file="../shared/admin-footer.tpl"}

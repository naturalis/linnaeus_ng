{include file="../shared/admin-header.tpl"}
{include file="../shared/admin-messages.tpl"}
<div id="page-main">
<form method="post" id="theForm" action="">
{t}Set the type of map that will appear in the runtime interface:{/t}<br/>
<input type="hidden" name="rnd" value="{$rnd}" />
<label><input type="radio" name="maptype" value="l2" {if $maptype=='l2'}checked="checked"{/if} />Linnaeus 2</label><br />
<label><input type="radio" name="maptype" value="lng" {if $maptype!='l2'}checked="checked"{/if} />Google Maps</label>
<p>
<input type="submit" value="{t}save{/t}" />
</p>
</form>

</div>
{include file="../shared/admin-footer.tpl"}

{include file="../shared/admin-header.tpl"}
{include file="../shared/admin-messages.tpl"}
{assign var=process value=true}

<div id="page-main">
{if $processed==true}
<p>
<a href="l2_additional.php">Import custom modules</a>
</p>
{else}
<form method="post">
<input type="hidden" name="process" value="1"  />
<input type="hidden" name="rnd" value="{$rnd}" />
<p>
<b>Map data</b><br/>
{literal}
<label>Import map items?&nbsp;&nbsp;<input type="checkbox" name="map_items" onchange="$('#afoort').attr('disabled',($(this).is(':checked') ? '' : 'disabled'));$('#afoort-label').css('color',($(this).is(':checked') ? '#000' : '#999'));" checked="checked"></label><br />
<label id="afoort-label">Map uses "Amersfoort"-coordinates&nbsp;&nbsp;<input type="checkbox" name="afoort" id="afoort"></label>
{/literal}
</p>
<input type="submit" value="import" />
</form>
{/if}
</div>

{include file="../shared/admin-footer.tpl"}
{include file="../shared/admin-header.tpl"}

<div id="page-main">

	<form method="post" id="theForm" action="">
	<input type="hidden" name="action" id="action" value="resolve">
	<input type="hidden" name="rnd" value="{$rnd}" />
    <p>
    Plak een lijst NSR ID's in het vak hieronder - een per regel - en klik op "opzoeken" (alleen voor taxa!).
    </p>
    <p>
    <textarea name="codes" style="width:250px;height:200px">{$codes}</textarea>
    </p>
    <p>
    <input type="submit" value="opzoeken" />
    </p>
    </form>
    
{if $result}


<p>
	<a href="#" onclick="$('#action').val('download');$('#theForm').submit();return false;">resultaten downloaden als tekstbestand</a>
</p>

{if $result|@count <= 100}
<table>
<tr><td>#</td><td>NSR ID</td><td>LNG ID</td><td>taxon</td></tr>
{foreach from=$result key=k item=v}
<tr>
	<td>{$v.line}</td>
    <td>{$v.code}</td>
    <td>{$v.lng_id}</td>
    <td>{$v.taxon}</td>
</tr>
{/foreach}
</table>
{/if}

{/if}

</div>

{include file="../shared/admin-messages.tpl"}
{include file="../shared/admin-footer.tpl"}
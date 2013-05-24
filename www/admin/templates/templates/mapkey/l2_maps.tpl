{include file="../shared/admin-header.tpl"}
{assign var=map value=$maps[$mapId]}

<div id="page-main">

{foreach item=v from=$maps}
	{$v.name}<br />
{/foreach}

<form enctype="multipart/form-data" action="" method="post">
<input type="hidden" name="id" value="{$id}" />  
<input type="hidden" name="rnd" value="{$rnd}" />
{t}Choose a file to upload:{/t} <input name="uploadedfile" type="file" /><br />
<input type="submit" value="{t}upload{/t}" />&nbsp;
</form>


</div>

{include file="../shared/admin-messages.tpl"}
{include file="../shared/admin-footer.tpl"}
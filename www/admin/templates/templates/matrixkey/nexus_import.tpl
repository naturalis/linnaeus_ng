{include file="../shared/admin-header.tpl"}
{include file="../shared/admin-messages.tpl"}

<div id="page-main">
{if $mId}
<span class="a" onclick="$('#id').val({$mId});$('#action').val('activate');$('#theForm').submit()">go to new matrix</span>
<form id="theForm" method="post" action="matrices.php">
<input type="hidden" name="id" id="id" value="" />
<input type="hidden" name="action" id="action" value="" />
</form>
{else}
<form method="post" action="" enctype="multipart/form-data">
	<input type="hidden" name="MAX_FILE_SIZE" value="10000000" />
	<input name="uploadedfile" type="file" />
	<p>
	<input type="submit" value="{t}upload{/t}" />
	</p>
</form>
{/if}
</div>
{include file="../shared/admin-footer.tpl"}
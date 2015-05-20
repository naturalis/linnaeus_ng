{include file="../shared/admin-header.tpl"}
{include file="../shared/admin-messages.tpl"}

<div class="page-generic-div literature-match">
<p>
	<a href="bulk_upload_download.php">download augmented input file</a>
    {if $have_ref_col}
    <br />
	<a href="bulk_upload_download.php?action=ref_only">download reference # matched to ID only</a>
    {/if}
    
</p>
<p>
	<a href="bulk_upload.php">back</a>
</p>
</div>

<script>
$(document).ready(function()
{
});
</script>

{include file="../shared/admin-footer.tpl"}

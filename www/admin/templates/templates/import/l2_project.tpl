{include file="../shared/admin-header.tpl"}
{include file="../shared/admin-messages.tpl"}

<div id="page-main">
{if $newProjectId}
<p>
A new project has been created. In the next step, species and ranks will be analyzed. The results will be presented for you to review before they are loaded.
</p>
<p>
<form method="post" id="theForm">
<input type="button" value="Analyze species and ranks" onclick="window.open('l2_analyze.php','_self');" />
</form>
</p>
{else}
<p>
An error occurred during the creation of the new project. The import was aborted.
</p>
<p>
<a href="linnaeus2.php">back</a>
</p>
{/if}
</div>

{include file="../shared/admin-footer.tpl"}
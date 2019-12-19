{include file="../shared/admin-header.tpl"}
{include file="../shared/admin-messages.tpl"}

<div id="page-main">
{if $newProjectId}
<p>
The new project has been created.<br />
In the next step, species and ranks will be analyzed. The results will be presented for you to review before they are loaded.<br />
Please be aware that each step can take some time, depending on he size of the project.
</p>
<p>
<form method="post" id="theForm">
<input type="button" value="Get ranks and species" onclick="window.open('l2_species.php','_self');" /><br />
</form>
</p>
{else}
<p>
An error occurred during the creation of the new project. The import was aborted.
</p>
<p>
<a href="l2_start.php">back</a>
</p>
{/if}
</div>

{include file="../shared/admin-footer.tpl"}
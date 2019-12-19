{include file="../shared/admin-header.tpl"}
{include file="../shared/admin-messages.tpl"}

<div id="page-main">
<form method="post">
<input type="hidden" name="action" value="export" />
<input type="hidden" name="rnd" value="{$rnd}" />
<p>
{t}Export taxon data to XML{/t}
</p>

<p>
aantal: <input type="text" style="width:40px;text-align:right" name="numberOfRecords" placeholder="5000" value="{$requestData.numberOfRecords}" /> * voor alles<br />
records per bestand: <input type="text" style="width:40px;text-align:right" name="recordsPerFile" placeholder="10000" value="{$requestData.recordsPerFile}" /><br />
doelfolder: <input type="text" style="width:250px" name="exportfolder" placeholder="/tmp/" value="{$requestData.exportfolder}" /><br />
</p>

<input type="submit" value="{t}export{/t}" />

</form>
<p>
{t}Images and other media files should be copied by hand, and are referenced in the export file by filename only. They can be found in the server folder:<br />{/t}
{$session.admin.project.paths.project_media}
</p>
</div>

{include file="../shared/admin-footer.tpl"}
{include file="../shared/admin-header.tpl"}
{include file="../shared/admin-messages.tpl"}
<div id="page-main">
{if $processed==true}
<p>
XYZ
</p>
{else}
Review the options below and press "import" to start the import database. Please note that the loading of data might take several minutes.
<form method="post">
<input type="hidden" name="process" value="1"  />
<input type="hidden" name="rnd" value="{$rnd}" />
<p>
<b>Species data</b><br/>
<label>Import general species descriptions?&nbsp;&nbsp;<input type="checkbox" name="taxon_overview" checked="checked"></label><br />
<label>Import media?&nbsp;&nbsp;
{if $session.system.import.imagePath===false}
You specified no media import.</span>
{else}
<input type="checkbox" name="taxon_media" checked="checked">
{/if}
</label><br />
<label>Import common names?&nbsp;&nbsp;<input type="checkbox" name="taxon_common" checked="checked"></label><br />
<label>Import synonyms?&nbsp;&nbsp;<input type="checkbox" name="taxon_synonym" checked="checked"></label><br />
</p>

<input type="submit" value="import" />
</form>
{/if}
<p>
<a href="l2_start.php">back</a>
</p>

</div>

{include file="../shared/admin-footer.tpl"}
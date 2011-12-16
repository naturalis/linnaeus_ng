{include file="../shared/admin-header.tpl"}
{include file="../shared/admin-messages.tpl"}
<div id="page-main">
{if $processed==true}
<p>
<a href="l2_literature_glossary.php">Import literature and glossary</a>
</p>
{else}
Select which elements you wish to import and click "Import".<br />
Please note that importing might take several minutes, especially when you
are importing media files.
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

<input type="submit" value="{t}Import{/t}" />
</form>
{/if}
</div>

{include file="../shared/admin-footer.tpl"}
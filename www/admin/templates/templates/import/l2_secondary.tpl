{include file="../shared/admin-header.tpl"}
{include file="../shared/admin-messages.tpl"}
{assign var=process value=true}
{literal}
<style>
.error {
	color:red;
}
.info {
	color:#308;
}
.minor {
	color:#888;
}
</style>
{/literal}
<div id="page-main">
{if $processed==true}
<p>
Data import is complete. You have been added as system administrator to the new project. In that capacity you can finish configuring the project by adding modules, creating users etc.
</p>
<p>
<a href="go_new_project.php">Go to project index</a>
</p>
{else}
Review the options below and press "import" to start the import database. Please note that the loading of data might take several minutes.
<form method="post">
<input type="hidden" name="process" value="1"  />
<input type="hidden" name="rnd" value="{$rnd}" />
<p>
<b>Species data</b><br/>
<label>Import general species descriptions?&nbsp;&nbsp;<input type="checkbox" name="taxon_overview" checked="checked"></label><br />
<label>Import common names?&nbsp;&nbsp;<input type="checkbox" name="taxon_common" checked="checked"></label><br />
<label>Import synonyms?&nbsp;&nbsp;<input type="checkbox" name="taxon_synonym" checked="checked"></label><br />
{if $session.system.import.imagePath===false}
(you specified no media import)<br />
{else}
<label>Import media?&nbsp;&nbsp;<input type="checkbox" name="taxon_media" checked="checked"></label><br />
{/if}

</p>

<p>
<b>Literature</b><br/>
{if $literature}
<label>Import literary references ({$literature|@count})?&nbsp;&nbsp;<input type="checkbox" name="literature" checked="checked"></label>
{capture assign=lit_errors}{foreach from=$literature key=k item=v}
{if $v.references.unknown_species|@count != 0}
"<i>{$v.original}</i>" refers to an unknown species:<br/>
{foreach from=$v.references.unknown_species item=u}
&#149;&nbsp;<span class="error">{$u}</span><br/>
{/foreach}
{/if}
{/foreach}{/capture}
{if $smarty.capture.lit_errors}
<br /><br />	
The following literary references refer to unknown species. The literary references themselves will be imported, but the links to the unknown species listed below will not.<br />
{$smarty.capture.lit_errors}
{/if}

{else}
No literary references found.
{/if}
</p>

<p>
<b>Glossary</b><br/>
{if $glossary}
<label>Import glossary items ({$glossary|@count})?&nbsp;&nbsp;<input type="checkbox" name="glossary" checked="checked"></label><br />
{else}
No glossary items found.
{/if}
</p>

<p>
<b>Content</b><br/>
{if $content.Introduction}
<label>Import project introduction?&nbsp;&nbsp;<input type="checkbox" name="content_introduction" checked="checked"></label><br />
{else}
No project introduction found.
{/if}
{if $content.Contributors}
<label>Import contributors text?&nbsp;&nbsp;<input type="checkbox" name="content_contributors" checked="checked"></label><br />
{else}
No contributors text found.
{/if}
</p>

<p>
<b>Additional topics</b><br/>
{if $additionalContent}
<label>Import additional topics ({$additionalContent|@count})?&nbsp;&nbsp;<input type="checkbox" name="additional_content" checked="checked"></label><br />
{else}
No additional topics found.
{/if}
</p>

<p>
<b>Keys</b><br/>
<label>Import dichotomous key(s)?&nbsp;&nbsp;<input type="checkbox" name="key_dich" checked="checked"></label><br />
<label>Import matrix key(s)?&nbsp;&nbsp;<input type="checkbox" name="key_matrix" checked="checked"></label><br />
</p>

<p>
<b>Map data</b><br/>
{if $mapItems.occurrences>0}
<label>Import map data ({$mapItems.total} items for {$mapItems.occurrences|@count} species in {$mapItems.types|@count} categories on {$mapItems.maps|@count} maps)?&nbsp;&nbsp;<input type="checkbox" name="map_items" checked="checked"></label><br />
{else}
No map data found.
{/if}
</p>

<input type="submit" value="import" />
</form>
{/if}
<p>
<a href="l2_start.php">back</a>
</p>

</div>

{include file="../shared/admin-footer.tpl"}
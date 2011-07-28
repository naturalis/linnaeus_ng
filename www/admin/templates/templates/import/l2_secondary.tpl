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
<p>
You will have to manually set the distinction between higher and lower taxa. You can do this here:<br />
<a href="../species/ranks.php">Species module &rarr; Taxonomic ranks </a>
</p>
<p>
You will also have to assign species to collaborators in order to see and edit taxa. You can do this here:<br />
<a href="../species/collaborators.php">Species module &rarr; Assign taxa to collaborator</a>
</p>
{else}
Review the options below and press "import" to start the import database. Please note that the loading of data might take several minutes.
<form method="post">
<input type="hidden" name="process" value="1"  />
<input type="hidden" name="rnd" value="{$rnd}" />
<p>
<b>Species data</b><br/>
Import general species descriptions?&nbsp;&nbsp;<label><input type="checkbox" name="taxon_overview" checked="checked"></label><br />
Import common names?&nbsp;&nbsp;<label><input type="checkbox" name="taxon_common" checked="checked"></label><br />
Import synonyms?&nbsp;&nbsp;<label><input type="checkbox" name="taxon_synonym" checked="checked"></label><br />
{if $session.system.import.imagePath===false}
(you specified no media import)<br />
{else}
Import media?&nbsp;&nbsp;<label><input type="checkbox" name="taxon_media" checked="checked"></label><br />
{/if}

</p>

<p>
<b>Literature</b><br/>
{if $literature}
Import literary references ({$literature|@count})?&nbsp;&nbsp;<label><input type="checkbox" name="literature" checked="checked"></label><br /><br />	
The following literary references contain errors and will not be loaded:<br />
{foreach from=$literature key=k item=v}
{if $v.references.unknown_species|@count != 0}
"<i>{$v.original}</i>" contains references to unknown species:<br/>
{foreach from=$v.references.unknown_species item=u}
&#149;&nbsp;<span class="error">{$u}</span><br/>
{/foreach}
{/if}
{/foreach}
{else}
No literary references found.
{/if}
</p>

<p>
<b>Glossary</b><br/>
{if $glossary}
Import glossary items ({$glossary|@count})?&nbsp;&nbsp;<label><input type="checkbox" name="glossary" checked="checked"></label><br />
{else}
No glossary items found.
{/if}
</p>

<p>
<b>Content</b><br/>
{if $content.Introduction}
Import project introduction?&nbsp;&nbsp;<label><input type="checkbox" name="content_introduction" checked="checked"></label><br />
{else}
No project introduction found.
{/if}
{if $content.Contributors}
Import contributors text?&nbsp;&nbsp;<label><input type="checkbox" name="content_contributors" checked="checked"></label><br />
{else}
No contributors text found.
{/if}
</p>

<p>
<b>Additional topics</b><br/>
{if $additionalContent}
Import additional topics ({$additionalContent|@count})?&nbsp;&nbsp;<label><input type="checkbox" name="additional_content" checked="checked"></label><br />
{else}
No additional topics found.
{/if}
</p>

<p>
<b>Keys</b><br/>
Import dichotomous key(s)?&nbsp;&nbsp;<label><input type="checkbox" name="key_dich" checked="checked"></label><br />
Import matrix key(s)?&nbsp;&nbsp;<label><input type="checkbox" name="key_matrix" checked="checked"></label><br />
</p>

<input type="submit" value="import" />
</form>
{/if}
<p>
<a href="linnaeus2.php">back</a>
</p>

</div>

{include file="../shared/admin-footer.tpl"}
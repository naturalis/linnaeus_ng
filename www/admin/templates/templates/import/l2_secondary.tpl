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
Data import is complete.
</p>
<p>
You will have to manually set the distinction between higher and lower taxa. You can do this here:<br />
<a href="../species/ranks.php">Species module -> Taxonomic ranks </a>
</p>
<p>
You will also have to assign species to collaborators in order to see and edit taxa. You can do this here:<br />
Projects -> TanBIF species -> Species module -> s  
<a href="../species/collaborators.php">Species module -> Assign taxa to collaborator</a>
</p>
<p>
<a href="go_new_project.php">Go to project index</a>
</p>
{else}
Review the options below and press "import" to start the import database. 
<form method="post">
<input type="hidden" name="process" value="1"  />
<input type="hidden" name="rnd" value="{$rnd}" />
<p>
<b>Species data</b><br/>
Import general species descriptions?&nbsp;&nbsp;<label><input type="checkbox" name="taxon_overview" checked="checked">yes</label><br />
Import common names?&nbsp;&nbsp;<label><input type="checkbox" name="taxon_common" checked="checked">yes</label><br />
{if $session.system.import.imagePath===false}
(specified no media import)<br />
{else}
Import media?&nbsp;&nbsp;<label><input type="checkbox" name="taxon_media" checked="checked">yes</label><br />
{/if}

</p>

<p>
<b>Literature</b><br/>
Import literary references?&nbsp;&nbsp;<label><input type="checkbox" name="literature" checked="checked">yes</label><br />
The following literary references contain errors:<br />
{foreach from=$literature key=k item=v}
{if $v.references.unknown_species|@count != 0}
"<i>{$v.original}</i>" contains references to unknown species:<br/>
{foreach from=$v.references.unknown_species item=u}
&#149;&nbsp;<span class="error">{$u}</span><br/>
{/foreach}
{/if}
{/foreach}
</p>

<p>
<b>Content</b><br/>
{if $content.Introduction}
Import project introduction?&nbsp;&nbsp;<label><input type="checkbox" name="content_introduction" checked="checked">yes</label><br />
{/if}
{if $content.Contributors}
Import contributors text?&nbsp;&nbsp;<label><input type="checkbox" name="content_contributors" checked="checked">yes</label><br />
{/if}
</p>

<p>
<b>Keys</b><br/>
Import dichotomous key(s)?&nbsp;&nbsp;<label><input type="checkbox" name="key_dich" checked="checked">yes</label><br />
Import matrix key(s)?&nbsp;&nbsp;<label><input type="checkbox" name="key_matrix" checked="checked">yes</label><br />
</p>

<input type="submit" value="import" />
</form>
{/if}
<p>
<a href="linnaeus2.php">back</a>
</p>

</div>

{include file="../shared/admin-footer.tpl"}
{include file="../shared/admin-header.tpl"}


{include file="../shared/admin-messages.tpl"}

<div id="page-main">
{if $errors|@count ==0}
{t}Editing taxa:{/t}
<ul class="admin-list">
	<li><a href="list.php">{t}Taxon list{/t}</a></li>
	<li><a href="orphans.php">{t}Orphans (taxa outside of the main taxon tree){/t}</a></li>
</ul>
<ul class="admin-list">
	<li><a href="new.php">{t}Add a new taxon{/t}</a></li>
	<li><a href="file.php">{t}Import taxon tree from file{/t}</a></li>
	<li><a href="col.php">{t}Import taxon tree from Catalogue Of Life (experimental){/t}</a></li>
	<li><a href="import.php">{t}Import taxon content from file{/t}</a></li>
</ul>
{t}Management tasks:{/t}
<ul class="admin-list">
	<li><a href="ranks.php">{t}Define taxonomic ranks{/t}</a></li>
	<li><a href="page.php">{t}Define categories{/t}</a></li>
	<li><a href="sections.php">{t}Define sections{/t}</a></li>
	<li><a href="collaborators.php">{t}Assign taxa to collaborators{/t}</a></li>
	<li><a href="ranklabels.php">{t}Label taxonomic ranks{/t}</a></li>
</ul>
{/if}
</div>

{include file="../shared/admin-footer.tpl"}

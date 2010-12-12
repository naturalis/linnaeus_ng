{include file="../shared/admin-header.tpl"}

<div id="page-main">
<ul class="admin-list">
	<li><a href="step_show.php">{t}Edit key (from startpoint){/t}</a></li>
	<li><a href="section.php">{t}Edit key subsections{/t}</a></li>
	<li><a href="map.php">{t}Key map{/t}</a></li>
	<li><a href="process.php">{t}Compute taxon division{/t}</a></li>
</ul>
<ul class="admin-list">
	<li><a href="orphans.php">{t}Taxa not part of the key{/t}</a></li>
	<li><a href="dead_ends.php">{t}Unconnected key endings{/t}</a></li>
	<!-- li><a href="import.php">{t}Import key (experimental){/t}</a></li -->
</ul>

<ul class="admin-list">
	<li><a href="rank.php">{t}Define ranks that can appear in key{/t}</a></li>
</ul>

</div>

{include file="../shared/admin-footer.tpl"}

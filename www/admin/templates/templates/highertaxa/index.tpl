{include file="../shared/admin-header.tpl"}
{include file="../shared/admin-messages.tpl"}

<div id="page-main">
<p>
{t _s1='<a href="../species/">' _s2='</a>' _s3='<a href="../species/ranks.php">' _s4='</a>'}The "higher taxa" module allows you to concisely describe the taxa in the upper part of the taxon tree. Taxa in the lower parts can be described compehensively in the %sspecies module%s. At what rank the distinction between higher and lower taxa is made, is defined in the %sranks definition%s in the species module.{/t}
</p>
{t}Please be aware that the higher and lower taxa are part of the same tree. Changes in the structure of the higher taxa might affect those of the lower taxa, so take care when editing. To avoid unforseen problems, it is impossible to delete taxa in the higher taxa module that have children among the lower taxa. In order to remove such a taxon completely, the child taxa should first be moved or deleted in the species module.{/t}
<ul class="admin-list">
	<li><a href="../species/branches.php">{t}Taxon list{/t}</a></li>
	<li><a href="../species/new.php">{t}Add a new higher taxon{/t}</a></li>
</ul>
</div>

{include file="../shared/admin-footer.tpl"}

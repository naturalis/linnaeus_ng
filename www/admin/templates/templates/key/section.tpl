{include file="../shared/admin-header.tpl"}


<div id="page-main">
{if $keySections|@count==0}
<p>
No key subsections are available.
</p>
{else}
Click a key subsection to edit:
<ul>
{section name=i loop=$keySections}
<li><a href="step_show.php?id={$keySections[i].id}">{$keySections[i].title}</a></li>
{/section}
</ul>
{/if}
<a href="?action=new">Start a new subsection</a>
</div>

{include file="../shared/admin-footer.tpl"}

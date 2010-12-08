{include file="../shared/admin-header.tpl"}

<div id="page-main">
<p>
{t}"Key subsections" are parts of the dichotomous key that are not connected to the entire key. Put differently, they are steps that are not the starting step of your key, nor the target of any choice in another step. By creating subsections, different collaborators can work on specific parts of the key, which are later "hooked up" to the main key.{/t}
</p>
{if $keySections|@count==0}
<p>
{t}No key subsections are available.{/t}
</p>
{else}
<p>
{t}Available subsections (click to edit):{/t}
</p>
<ul>
{section name=i loop=$keySections}
<li><a href="step_show.php?id={$keySections[i].id}">{t}Step{/t} {$keySections[i].number}: {$keySections[i].title}</a></li>
{/section}
</ul>
{/if}
<a href="?action=new">Start a new subsection</a>
</div>

{include file="../shared/admin-footer.tpl"}

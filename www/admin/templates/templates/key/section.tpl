{include file="../shared/admin-header.tpl"}

<div id="page-main">
<p>
{t}"Key sections" are parts of the dichotomous key that are not connected to the entire key. Put differently, they are steps that are not the starting step of your key, nor the target of any choice in another step. By creating sections, different collaborators can work on specific parts of the key, which are later hooked up to the main key.{/t}
</p>
{if $keySections|@count==0}
<p>
{t}No key sections are available.{/t}
</p>
{else}
<p>
{t}Available sections (click to edit):{/t}
</p>
<ul>
{foreach from=$keySections item=v}
<li>
	<a href="step_show.php?id={$v.id}">{t}Step{/t} {$v.number}: {$v.title}</a>
&nbsp;(<a href="?action=setstart&id={$v.id}">set as key start</a>)
</li>
{/foreach}
</ul>
{/if}
<a href="?action=new">{t}Start a new subsection{/t}</a>
</div>

{include file="../shared/admin-footer.tpl"}

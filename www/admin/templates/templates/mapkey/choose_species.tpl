{include file="../shared/admin-header.tpl"}

<div id="page-main">

<table>
{foreach from=$taxa key=k item=v}
{if $v.lower_taxon==1}
	<tr class="tr-highlight">
		<td>{$v.taxon}</td>
		<td>[<a href="draw_species.php?id={$v.id}">{t}add data{/t}</a>]</td>
	</tr>
{/if}
{/foreach}
</table>

{if $prevStart!=-1 || $nextStart!=-1}
	<div id="navigation">
		{if $prevStart!=-1}
		<span class="pseudo-a" onclick="goNavigate({$prevStart});">{t}< previous{/t}</span>
		{/if}
		{if $nextStart!=-1}
		<span class="pseudo-a" onclick="goNavigate({$nextStart});">{t}next >{/t}</span>
		{/if}
	</div>
{/if}

<form name="theForm" id="theForm" method="post" action="">
</form>

<p>
{t _s1='<a href="file.php">' _s2='</a>'}You can also define multiple occurrences at once by %suploading a file%s.{/t}
</p>
</div>
{include file="../shared/admin-messages.tpl"}
{include file="../shared/admin-footer.tpl"}

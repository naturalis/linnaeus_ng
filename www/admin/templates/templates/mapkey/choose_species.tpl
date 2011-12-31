{include file="../shared/admin-header.tpl"}

<div id="page-main">

<table>
{foreach from=$taxa key=k item=v}
	<tr class="tr-highlight">
		<td style="width:300px">{$v.taxon}</td>
		<td>{if $occurringTaxa[$v.id]}[<a href="species_show.php?id={$v.id}">{t}show data{/t}</a>]{else}{t}(no data){/t}{/if}</td>
		<td>[<a href="species_edit.php?id={$v.id}">{t}edit data{/t}</a>]</td>
		<td>{if $occurringTaxa[$v.id]}[<a href="copy.php?id={$v.id}">{t}copy data{/t}</a>]{/if}</td>
	</tr>
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
{* t _s1='<a href="file.php">' _s2='</a>'}You can also define multiple occurrences at once by %suploading a file%s.{/t *}
</p>
</div>
{include file="../shared/admin-messages.tpl"}
{include file="../shared/admin-footer.tpl"}

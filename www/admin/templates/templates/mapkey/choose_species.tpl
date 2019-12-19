{include file="../shared/admin-header.tpl"}

<div id="page-main">
{if $geodataTypes|@count==0}
<p>
	You have to define at least one data type before you can add any map data. <a href="data_types.php">Define data types</a>
</p>
{/if}
{if $taxa}
<table>
{foreach from=$taxa key=k item=v}
	<tr class="tr-highlight">
		<td style="width:300px">{$v.taxon}</td>
		{*<td>{if $occurringTaxa[$v.id]}[<a href="species_show.php?id={$v.id}">{t}show data{/t}</a>]{else}{t}(no data){/t}{/if}</td>*}
		{* <td>{if $occurringTaxa[$v.id]}[<a href="preview.php?id={$v.id}">{t}preview{/t}</a>]{else}{t}(no data){/t}{/if}</td> *}
		<td>{if $geodataTypes|@count>0}[<a href="{if $maptype=='l2'}l2_{/if}species_edit.php?id={$v.id}">{t}edit{/t}</a>]{/if}</td>
		<td>{if $occurringTaxa[$v.id]}[<a href="copy.php?id={$v.id}">{t}copy data{/t}</a>]{/if}</td>
	</tr>
{/foreach}
</table>
{else}
{t}No species have been defined.{/t}
{/if}

{if $prevStart!=-1 || $nextStart!=-1}
	<div id="navigation">
		{if $prevStart!=-1}
		<span class="a" onclick="goNavigate({$prevStart});">{t}< previous{/t}</span>
		{/if}
		{if $nextStart!=-1}
		<span class="a" onclick="goNavigate({$nextStart});">{t}next >{/t}</span>
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

{include file="../shared/admin-header.tpl"}

<div id="page-main" class="taxonomy">

	<h2>{$concept.taxon} <a class="inline-href" href="taxonomy.php?id={$concept.id}">edit concept</a></h2>

	<table>
	{foreach from=$names.list item=v}
		{if $v.nametype!='isValidNameOf'}
		<tr class="tr-highlight">
			<td>{$v.name}</td>
			<td>{$v.nametype_label}</td>
			<td>{$v.expert_name}</td>
			<td>{$v.organisation_name}</td>
			<td style="max-width:100px;overflow:hidden;white-space:nowrap">{$v.reference_label}</td>
			<td style="max-width:100px;overflow:hidden;white-space:nowrap">{$v.reference_author}</td>
			<td><a class="inline-href" href="names_edit.php?id={$concept.id}&name_id={$v.id}">edit</a></td>
		</tr>
		{/if}
	{/foreach}
	</table>
	
	<p>
		<a href="names_edit.php?id={$concept.id}">add a name</a><br />
		<a href="taxon.php?id={$concept.id}">main page</a>
	</p>
</div>

{include file="../shared/admin-messages.tpl"}
{include file="../shared/admin-footer.tpl"}

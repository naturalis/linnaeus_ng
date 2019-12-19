{include file="../shared/admin-header.tpl"}

<style type="text/css">
.link-cell {
	border-bottom: 1px dotted #ddd;
	text-align: bottom;
}
</style>

<div id="page-main">

	<p>
		<span class="matrix-header">{t _s1=$matrix.label}Viewing taxon-state links in the matrix "%s"{/t}</span>
	</p>

	<form id="theForm" method="post" action="">
	<input type="hidden" name="rnd" value="{$rnd}" />
	<table>
		<tr>
			<td>
				{t}select a taxon:{/t}
			</td>
			<td>
				<select name="taxon" id="taxon" onchange="$('#theForm').submit();">
					<option></option>
					{foreach $taxa v}
						<option value="{$v.id}" {if $v.id==$taxon}selected="selected"{/if}>{$v.taxon}{if $v.name} ({$v.name}){/if}</option>
					{/foreach}
				</select>
		</td>
	</tr>
</table>
</form>

<br />

<table>
	<tr>
		<th style="min-width: 200px;border-bottom: 1px solid black;">{t}character{/t}</th>
		<th style="min-width: 200px;border-bottom: 1px solid black;">{t}state{/t}</th>
	</tr>
	{foreach $links val k}

	<tr class="tr-highlight">
		<td class="link-cell">
			{if $prevChar!=$val.characteristic}<b>{$val.characteristic_split.label}</b>{/if}
			{assign var=prevChar value=$val.characteristic}
		</td>
		<td class="link-cell">
			{$val.state}
		</td>
	</tr>
	{/foreach}
	{if $links|@count==0}
	<tr>
		<td colspan="2">{t}No links found.{/t}</td>
	</tr>
	{/if}
</table>

<p>
	<input type="button" onclick="window.open('edit.php','_self');" value="{t}back{/t}">
</p>

</div>




{include file="../shared/admin-messages.tpl"}
{include file="../shared/admin-footer.tpl"}

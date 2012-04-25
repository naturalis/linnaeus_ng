{include file="../shared/admin-header.tpl"}

<div id="page-main">
<p>
<span class="matrix-header">{t _s1=$matrix.matrix}Viewing taxon-state links in the matrix "%s"{/t} (<a href="edit.php">{t}view matrix{/t}</a>)</span>
</p>

<form id="theForm" method="post" action="">
<input type="hidden" name="rnd" value="{$rnd}" />
<table>
	<tr>
		<td>
			{t}Choose a taxon:{/t}
		</td>
		<td>
	<select name="taxon" id="taxon" onchange="$('#theForm').submit();">
		<option></option>
	{foreach from=$taxa key=k item=val}
		<option value="{$val.id}" {if $val.id==$taxon}selected="selected"{/if}>
		{section name=foo loop=$val.level-$taxa[0].level}
		&nbsp;
		{/section}		
		{$val.taxon}</option>
	{/foreach}
	</select>
		</td>
	</tr>
</table>
</form>
<br />
<table>
	<tr>
		<th style="width:200px">
		{t}Character{/t}
		</th>
		<th>
		{t}State{/t}
		</th>
	</tr>
	{foreach from=$links key=k item=val}
	<tr class="tr-highlight">
		<td>
		{if $prevChar!=$val.characteristic}{$val.characteristic}{/if}
		{assign var=prevChar value=$val.characteristic}
		</td>
		<td>
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
</div>


{include file="../shared/admin-messages.tpl"}
{include file="../shared/admin-footer.tpl"}
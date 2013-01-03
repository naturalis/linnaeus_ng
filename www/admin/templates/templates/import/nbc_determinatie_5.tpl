{include file="../shared/admin-header.tpl"}
{include file="../shared/admin-messages.tpl"}

<div id="page-main">
{if $processed}
	<form method="post" action="nbc_determinatie_6.php">
	<input type="hidden" name="rnd" value="{$rnd}" />
	<p>
	Enter the skin name to use:<br />
	<input type="text" name="skinname" value="{$skinName}" />
	</p>
	<p>
	<input type="submit" value="Finish import">
	</p>
	</form>
	

{else}
<p>
	<form method="post" action="nbc_determinatie_5.php">
	Assign the correct character types and click the button to import the matrix data.
	<p>
	<table>
	{foreach from=$characters item=v}
	{if $v.group!='hidden'}
	<tr class="tr-highlight">
		<td>{$v.label}</td>
		<td>
			<select name="char_type[{$v.code}]">
				<option value="media">media</option>
				<option value="range">range</option>
				<option value="text">text / list</option>
			</select>
		</td>
	</tr>
	{/if}
	{/foreach}
	</table> 
	</p>
	
	<input type="hidden" name="rnd" value="{$rnd}" />
	<input type="hidden" name="action" value="matrix" />
	<input type="submit" value="Import matrix data">
	</form>
</p>
{/if}
<p>
	<a href="nbc_determinatie_4.php">Back</a>
</p>

</div>

{include file="../shared/admin-footer.tpl"}
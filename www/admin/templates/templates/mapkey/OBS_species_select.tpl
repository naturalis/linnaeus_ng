{include file="../shared/admin-header.tpl"}
<div id="page-main">
<table>
{t}Click the name of the species of which you want to see the geographical data.{/t}
{foreach from=$taxa key=k item=v}
	<tr>
		<td>
			<a href="species.php?id={$v.id}">{$v.taxon}</a>
		</td>
	</tr>
{/foreach}
</table>
</form>

</div>
{include file="../shared/admin-messages.tpl"}
{include file="../shared/admin-footer.tpl"}

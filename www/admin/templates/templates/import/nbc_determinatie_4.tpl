{include file="../shared/admin-header.tpl"}
{include file="../shared/admin-messages.tpl"}

<div id="page-main">
{if $processed}
	<a href="nbc_determinatie_5.php">Import matrix data</a>
{else}
<p>
	Click the button to import ranks and species.
	<form method="post" action="nbc_determinatie_4.php">
	<input type="hidden" name="rnd" value="{$rnd}" />
	<input type="hidden" name="action" value="species" />
	<input type="submit" value="Import ranks & species">
	</form>
</p>
{/if}
<p>
	<a href="nbc_determinatie_2.php">Back</a>
</p>

</div>

{include file="../shared/admin-footer.tpl"}
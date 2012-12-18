{include file="../shared/admin-header.tpl"}
{include file="../shared/admin-messages.tpl"}

<div id="page-main">
{if $processed}
	<a href="nbc_determinatie_6.php">Finish import</a>
{else}
<p>
	Click the button to import matrix data.
	<form method="post" action="nbc_determinatie_5.php">
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
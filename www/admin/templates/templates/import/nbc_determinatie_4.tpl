{include file="../shared/admin-header.tpl"}
{include file="../shared/admin-messages.tpl"}

<div id="page-main">
{if $processed}
	<a href="nbc_determinatie_5.php">Import matrix data</a>
{else}
<p>
	<form method="post" action="nbc_determinatie_4.php">
	<input type="hidden" name="rnd" value="{$rnd}" />
	<input type="hidden" name="action" value="species" />

	Click the button to import ranks and species.
    {if $variantColumns}
    <p>
    If your sheet contains data to differentiate between variations of the same species (gender, lifestage), please select
    the columns that contain the relevant information (only columns that are labeled as 'hidden' are shown).<br />
    {foreach from=$variantColumns item=v}
    <input type="checkbox" name="variant_columns[]" value="{$v.id}"{if in_array($v.label, $stdVariantColumns)} checked="checked"{/if}/>{$v.label}<br />
    {/foreach}
    </p>
    {/if}
    
    
	<input type="submit" value="Import ranks & species">
	</form>
</p>
{/if}
<p>
	<a href="nbc_determinatie_2.php">Back</a>
</p>

</div>

{include file="../shared/admin-footer.tpl"}
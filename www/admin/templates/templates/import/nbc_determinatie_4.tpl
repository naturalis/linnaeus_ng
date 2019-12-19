{include file="../shared/admin-header.tpl"}
{include file="../shared/admin-messages.tpl"}

<div id="page-main">
{if $processed}
	<a href="nbc_determinatie_5.php">Import matrix data</a>
{else}
<form method="post" action="nbc_determinatie_4.php">
<input type="hidden" name="rnd" value="{$rnd}" />
<input type="hidden" name="action" value="save" />
<p>
Map extra fields:
<table cellpadding="0" cellspacing="0">

{foreach from=$hidden item=h key=k}
{if ($nbcColumns[$k] && $nbcColumns[$k]!='-') || !$nbcColumns[$k]}

{assign var=val value=$nbcColumns[$k]}
{if $val=='' && $k|in_array:$nbcColumns}
{assign var=val value=$k}
{/if}
	<tr>
    	<td>{$k}</td>
        <td>
        	<input type="text" name="nbcColumns[{$k}]" value="{$val}" {if $val==''} style="background:#fcc"{/if}>
		</td>
	</tr>
{/if}
{/foreach}
</table>
<p>
Possible values: {foreach from=$nbcColumns|@array_flip item=h key=k}
{if $k!='-'}"{$k}", {/if}
{/foreach}
</p>
<p>
    {* if $variantColumns}
    <p>
    If your sheet contains data to differentiate between variations of the same species (gender, lifestage), please select
    the columns that contain the relevant information (only columns that are labeled as 'hidden' are shown).<br />
    {foreach from=$variantColumns item=v}
    <input type="checkbox" name="variant_columns[]" value="{$v.id}"{if in_array($v.label, $stdVariantColumns)} checked="checked"{/if}/>{$v.label}<br />
    {/foreach}
    </p>
    {/if *}
<p>
	<input type="submit" value="Import ranks & species">
</p>
	</form>
{/if}
<p>
	<a href="nbc_determinatie_2.php">Back</a>
</p>

</div>

{include file="../shared/admin-footer.tpl"}
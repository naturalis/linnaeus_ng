{include file="../shared/admin-header.tpl"}

<div id="page-main">
{if $processed}
{if $taxonCount==0}
{t}Your key has no taxa attached to it.{/t}
{else}
{t _s1=$stepCount _s2=$taxonCount}Done. Processed %s steps and %s taxa.{/t}
{/if}
<p>
{t}<a href="step_show.php">{t}Go to your key{/t}</a>.{/t}
</p>
{else}
<p>
{t}Click the button below to have the system store a tree-structured representation of the key, required for runtime purposes.{/t}<br />
{t}Please note that, depending on the size of your key, this might take a few minutes.{/t}
</p>
<p>
Key tree last generated on: {if $keyinfo.keytree.date_hr}{$keyinfo.keytree.date_hr}{else}(never){/if}<br /><br />
Last change to a keystep on: {if $keyinfo.keystep.date_hr}{$keyinfo.keystep.date_hr}{else}(never){/if} (deletes are not logged!)<br />
Last change to a keystep choice on: {if $keyinfo.choice.date_hr}{$keyinfo.choice.date_hr}{else}(never){/if} (deletes are not logged!)<br />
{if $keyinfo.keystep.date_x > $keyinfo.keytree.date_x || $keyinfo.choice.date_x > $keyinfo.keytree.date_x || $didKeyTaxaChange}
</p>
<p>
<span class="message-error">The key tree is older than the last edit to your key. It should be updated.</span>
{/if}
</p>
<form action="" method="post">
<input type="hidden" name="action" value="store" />
<input type="hidden" name="rnd" value="{$rnd}" />
<input type="hidden" name="step" value="{$step}" />
<input type="submit" value="{t}store key tree{/t}" />
</form>
{/if}
</div>

{include file="../shared/admin-messages.tpl"}
{include file="../shared/admin-footer.tpl"}

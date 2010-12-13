{include file="../shared/admin-header.tpl"}

<div id="page-main">
{if $stepCount || $taxonCount}
{t _s1=$stepCount _s2=$taxonCount}Done. Processed %s steps and %s taxa.{/t}
<p>
{t}<a href="step_show.php">{t}Go to your key{/t}</a>.{/t}
</p>
{else}
<p>
{t}Click the button below to have the system compute the list of remaining taxa in each step of your key. Please be aware: in the case of large keys with many species, this might take some time.{/t}
</p>
<form action="" method="post">
<input type="hidden" name="action" value="process" />
<input type="hidden" name="rnd" value="{$rnd}" />
<input type="submit" value="{t}process{/t}" />
</form>
{/if}
</div>

{include file="../shared/admin-messages.tpl"}

{include file="../shared/admin-footer.tpl"}

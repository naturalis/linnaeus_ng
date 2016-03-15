{include file="../shared/admin-header.tpl"}

<div id="page-main">
<form method="post">
<input type="hidden" name="step" value="{$step.id}" />
<input type="hidden" name="action" value="insert" />
<input type="hidden" name="rnd" value="{$rnd}" />
{if $sourceSteps}
{t}Insert a step between{/t}
{if $sourceSteps|@count==1}
 {$sourceSteps[0].number}{if $sourceSteps[0].title}: "{$sourceSteps[0].title}"{/if} 
<input type="hidden" name="source" value="{$sourceSteps[0].id}" />
{else}
<p>
{foreach from=$sourceSteps item=v key=k}
<label>
<input 
	type="radio" 
	name="source" 
	value="{$v.id}"
	{if ($prevStep==$v.id) || ($prevStep==null && $k==0)}
	checked="checked"
	{/if}
	>{$v.number}{if $v.title}: "{$v.title}"{/if}
</label><br />
{/foreach}
</p>
{/if}
{t}and step{/t} {$step.number}{if $step.title}: "{$step.title}"{/if}.
{else}
{t}Insert a new start step before step{/t} {$step.number}{if $step.title}: "{$step.title}"{/if}.
{/if}
<p>
<input type="submit" value="{t}insert{/t}" />
<input type="button" value="{t}back{/t}" onclick="window.open('step_show.php?id={$step.id}','_self')" />
</p>
</div>

{include file="../shared/admin-footer.tpl"}

{include file="../shared/admin-header.tpl"}

<div id="page-main">

{t}Below is a list of steps without any choices. To edit, click the name the step.{/t}
<ul>
{section name=i loop=$deadSteps}
<li>{t}Step{/t} 
	<span class="pseudo-a" onclick="$('#step').val({$deadSteps[i].id});$('#stepForm').submit();">{$deadSteps[i].number}: {if $deadSteps[i].title}"{$deadSteps[i].title}"{else}...{/if}</span></li>
{/section}
</ul>
{if $smarty.section.i.max==0}There are currently no steps without choices.{/if}


{t}Below is a list of unconnected choices, i.e. those that do not lead to another step or a taxon. To edit, click the name of either the step or the choice.{/t}
<ul>
{section name=i loop=$deadChoices}
<li>{t}Step{/t} 
	<span class="pseudo-a" onclick="$('#step').val({$deadChoices[i].step.id});$('#stepForm').submit();">{$deadChoices[i].step.number}: "{$deadChoices[i].step.title}"</span>, 
	<span class="pseudo-a" onclick="$('#choice').val({$deadChoices[i].id});$('#choiceForm').submit();">{t}choice{/t} {$deadChoices[i].show_order}: "{$deadChoices[i].title}"</span></li>
{/section}
</ul>
{if $smarty.section.i.max==0}There are currently no unconnected choices.{/if}
</div>
<form action="step_show.php" method="post" id="stepForm">
<input type="hidden" name="id" id="step" value="" />
</form>

<form action="choice_edit.php" method="post" id="choiceForm">
<input type="hidden" name="id" id="choice" value="" />
</form>

{include file="../shared/admin-footer.tpl"}

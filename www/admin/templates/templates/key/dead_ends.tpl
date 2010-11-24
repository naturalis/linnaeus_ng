{include file="../shared/admin-header.tpl"}

<div id="page-main">
{t}Below is a list of unconnected choices, i.e. those that do not lead to another step or a taxon. To edit, click the name of either the step or the choice.{/t}
<ul>
{section name=i loop=$keyendings}
<li>{t}Step{/t} 
	<span class="pseudo-a" onclick="$('#step').val({$keyendings[i].step.id});$('#stepForm').submit();">{$keyendings[i].step.number}: "{$keyendings[i].step.title}"</span>, 
	<span class="pseudo-a" onclick="$('#choice').val({$keyendings[i].id});$('#choiceForm').submit();">{t}choice{/t} {$keyendings[i].show_order}: "{$keyendings[i].title}"</span></li>
{/section}
</ul>
</div>
<form action="step_show.php" method="post" id="stepForm">
<input type="hidden" name="id" id="step" value="" />
</form>

<form action="choice_edit.php" method="post" id="choiceForm">
<input type="hidden" name="id" id="choice" value="" />
</form>

{include file="../shared/admin-footer.tpl"}

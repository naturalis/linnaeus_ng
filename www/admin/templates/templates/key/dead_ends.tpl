{include file="../shared/admin-header.tpl"}

<div id="page-main">
<p>
{t}Below is a list of steps without any choices. To edit, click the name the step.{/t}
<ul>
{foreach item=v from=$deadSteps}
<li>{t}Step{/t} 
	<span class="a" onclick="$('#step').val({$v.id});$('#stepForm').submit();">{$v.number}: {if $v.title}"{$v.title}"{else}...{/if}</span></li>
{/foreach}
</ul>
{if $deadSteps|@count==0}There are currently no steps without choices.{/if}
</p>


<p>
{t}Below is a list of steps with only one choice. To edit, click the name the step.{/t}
<ul>
{foreach item=v from=$sadSteps}
<li>{t}Step{/t} 
	<span class="a" onclick="$('#step').val({$v.id});$('#stepForm').submit();">{$v.number}: {if $v.title}"{$v.title}"{else}...{/if}</span></li>
{/foreach}
</ul>
{if $sadSteps|@count==0}There are currently no steps without choices.{/if}
</p>

<p>
{t}Below is a list of unconnected choices, i.e. those that do not lead to another step or a taxon. To edit, click the name of either the step or the choice.{/t}
<ul>
{foreach item=v from=$deadChoices}
<li>{t}Step{/t} 
	<span class="a" onclick="$('#step').val({$v.step.id});$('#stepForm').submit();">{$v.number}: "{$v.title|@strip_tags}"</span>, 
	<span class="a" onclick="$('#choice').val({$v.id});$('#choiceForm').submit();">{t}choice{/t} {$v.show_order}: "{$v.choice|@strip_tags|@trim|@substr:0:50}{if $v.choice|@count_characters>50}...{/if}"</span></li>
{/foreach}
</ul>
{if $deadChoices|@count==0}There are currently no unconnected choices.{/if}
</p>
</div>
<form action="step_show.php" method="post" id="stepForm">
<input type="hidden" name="id" id="step" value="" />
</form>

<form action="choice_edit.php" method="post" id="choiceForm">
<input type="hidden" name="id" id="choice" value="" />
</form>
{include file="../shared/admin-footer.tpl"}

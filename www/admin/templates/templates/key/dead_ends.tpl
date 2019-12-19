{include file="../shared/admin-header.tpl"}

<div id="page-main">

	<p>
	{if $deadSteps|@count==0}
		{t}There are currently no steps without choices.{/t}
	{else}
		{t}Below is a list of steps without any choices. To edit, click the name the step.{/t}
		<ul>
			{foreach item=v from=$deadSteps}
			<li>
				<a href="step_show.php?nopath=1&id={$v.id}">{t}Step{/t} {$v.number}:</a> {if $v.title}"{$v.title}"{else}...{/if}
			</li>
			{/foreach}
		</ul>
	{/if}
	</p>

	<p>
	{if $sadSteps|@count==0}
		{t}There are currently no steps with only one choice.{/t}
	{else}
		{t}Below is a list of steps with only one choice. To edit, click the name the step.{/t}
		<ul>
			{foreach item=v from=$sadSteps}
			<li>
				<a href="step_show.php?nopath=1&id={$v.id}">{t}Step{/t} {$v.number}:</a> {if $v.title}"{$v.title}"{else}...{/if}
			</li>
			{/foreach}
		</ul>
	{/if}
	</p>

	<p>
	{if $deadChoices|@count==0}
		{t}There are currently no unconnected choices.{/t}
	{else}
		{t}Below is a list of unconnected choices, i.e. those that do not lead to another step or a taxon. To edit, click the name of either the step or the choice.{/t}
		<ul>
		{foreach item=v from=$deadChoices}
		<li id="choice-{$v.id}"> 
			<a href="step_show.php?nopath=1&id={$v.keystep_id}">{t}Step{/t} {$v.number}:</a> "{$v.title|@strip_tags}", 
			<a href="choice_edit.php?id={$v.id}">{t}choice{/t} {$v.show_order}:</a> "{$v.choice_txt|@strip_tags|@trim|@substr:0:50}{if $v.choice_txt|@count_characters>50}...{/if}"
			[<a href="choice_edit.php?id={$v.id}" target="_blank">open in new window</a>]
		</li>
		{/foreach}
		</ul>
	{/if}
	</p>

</div>

{include file="../shared/admin-footer.tpl"}

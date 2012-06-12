<div id="allNavigationPane">
<div class="navigation-icon-wrapper">

{assign var='totalSteps' value=$keypath|@count}
{assign var='previousStep' value=$totalSteps-2}

<a class="navigation-icon" id="decision-path-icon" 
	style="margin-right: 618px;"
	href='javascript:showDialog("{t}Decision path{/t}",tmp);' 
	title="{t}Decision path{/t}">{t}Decision path{/t}</a>

{if $useJavascriptLinks}
    {if $totalSteps > 1}
        <a class="navigation-icon" id="first-icon" href="../key/" title="{t}Return to first step{/t}">{t}First{/t}</a>
    {else}
        <span class="navigation-icon" id="first-icon-inactive">{t}First{/t}</span>
    {/if} 
    <span
    {if $totalSteps > 1}
        onclick="keyDoStep({$keypath.$previousStep.id});" id="previous-icon" 
        title="{t}Return to step{/t} {$keypath.$previousStep.step_number}{if $v.choice_marker} ({$v.choice_marker}){/if}"
    {else}
        id="previous-icon-inactive"
    {/if} 
    class="navigation-icon" />{t}Previous{/t}</span>
{else}
    {if $totalSteps > 1}
        <a class="navigation-icon" id="first-icon" href="../key/" title="{t}Return to first step{/t}">{t}First{/t}</a>
        <a class="navigation-icon" id="previous-icon" href="../key/index.php?step={$keypath.$previousStep.id}" 
        title="{t}Return to step{/t} {$keypath.$previousStep.step_number}{if $v.choice_marker} ({$v.choice_marker}){/if}">
        {t}Previous{/t}</a>
    {else}
        <span class="navigation-icon" id="first-icon-inactive">{t}First{/t}</span>
        <span class="navigation-icon" id="previous-icon-inactive">{t}Previous{/t}</span>
    {/if}
{/if}

{if $backlink}
    <a class="navigation-icon" id="back-icon" href="{$backlink.url}" title="{t}Back to {/t}{$backlink.name}">{t}Back{/t}</a>
{else}
    <span class="navigation-icon" id="back-icon-inactive">{t}Back{/t}</span>
{/if}
</div>
</div>

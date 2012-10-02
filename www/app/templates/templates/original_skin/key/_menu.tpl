<div id="allNavigationPane">
<div class="navigation-icon-wrapper">

{assign var='totalSteps' value=$keypath|@count}
{assign var='previousStep' value=$totalSteps-2}

<span style="margin-left:10px;cursor:pointer;" onclick="allLookupShowDialog()">{t}contents{/t}</span>

{if $useJavascriptLinks}
    <span
    {if $totalSteps > 1}
        "onclick="keyDoStep($keypath.0.id)" id="first-icon" title="{t}Return to first step{/t}"
    {else}
        id="first-icon-inactive"
    {/if} 
    class="navigation-icon" />{t}First{/t}</span>
    <span
    {if $totalSteps > 1}
        onclick="keyDoStep($keypath.$previousStep.id) id="previous-icon" 
        title="{t}Return to step{/t} {$keypath.$previousStep.step_number}{if $v.choice_marker} ({$v.choice_marker}){/if}"
    {else}
        id="previous-icon-inactive"
    {/if} 
    class="navigation-icon" />{t}Previous{/t}</span>
{else}
    {if $totalSteps > 1}
        <a class="navigation-icon" id="first-icon" href="../key/index.php?step={$keypath.0.id}" title="{t}Return to first step{/t}">{t}First{/t}</a>
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

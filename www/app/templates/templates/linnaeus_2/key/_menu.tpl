{assign var=selectedChoice value=$keypath|@end}
<script type="text/javascript">
	allLookupSetSelectedId({$selectedChoice.id});
</script>


<div id="allNavigationPane">
<div class="navigation-icon-wrapper">

{assign var='totalSteps' value=$keypath|@count}
{assign var='previousStep' value=$totalSteps-2}

<script type="text/javascript">
    var decisionPath = '<div id="lookup-DialogContent">';
    {if $keypath|@count > 1}
        {foreach from=$keypath key=k item=v name=pathPopup}
            {if !$smarty.foreach.pathPopup.last}
            decisionPath = decisionPath + 
                '<p class="row">'+
                '<a href="javascript:void(0);keyDoStep({$v.id})"><b>{t}Step{/t} {$v.step_number|@escape}{if $v.choice_marker}{$v.choice_marker|@escape}{/if}</b>{if $v.step_number!=$v.step_title} - {$v.step_title|@escape}{/if}{if $v.choice_txt}:<br>{$v.choice_txt|@escape}{/if}</a>'+
                '</p>';
            {/if}
        {/foreach}
    {else}
        decisionPath = decisionPath + '<p>{t}No choices made yet{/t}</p>';
    {/if}
    decisionPath = decisionPath + '</div>';
</script>

<span onclick="allLookupShowDialog()" id="contents-icon" title="{t}Contents{/t}" class="navigation-icon" />
{t}Contents{/t}</span>

{if $useJavascriptLinks}
    {if $totalSteps > 1}
        <a class="navigation-icon" id="first-icon" onclick="keyDoStep($keypath.0.id);" title="{t}Return to first step{/t}">{t}First{/t}</a>
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


{include file="../shared/_back-to-search.tpl"}
<a class="navigation-icon" id="decision-path-icon" 
	
	href='javascript:showDialog("{t}Decision path{/t}",decisionPath);' 
	title="{t}Decision path{/t}">{t}Decision path{/t}</a>
	
</div>
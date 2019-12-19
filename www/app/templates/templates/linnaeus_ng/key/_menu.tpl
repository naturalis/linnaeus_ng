{assign var=selectedChoice value=$keypath|@end}

<script type="text/javascript">
	allLookupSetSelectedId({$selectedChoice.id});
</script>

<!-- <div id="allNavigationPane">
<div class="navigation-icon-wrapper">

{assign var='totalSteps' value=$keypath|@count}
{assign var='previousStep' value=$totalSteps-2}

<script type="text/javascript">
    var decisionPath = '<div id="lookup-DialogContent">';
    {if $keypath|@count > 1}
        {foreach $keypath v k pathPopup}
            {if !$smarty.foreach.pathPopup.last}
            decisionPath = decisionPath + 
                '<p class="row">'+
                '<a href="javascript:void(0);keyDoStep({$v.id})"><b>{t}Step{/t} {$v.step_number|@escape:javascript}{if $v.choice_marker}{$v.choice_marker|@escape:javascript}{/if}</b>{if $v.step_number!=$v.step_title} - {$v.step_title|@escape:javascript}{/if}{if $v.choice_txt}:<br>{$v.choice_txt|@escape:javascript}{/if}</a>'+
                '</p>';
            {/if}
        {/foreach}
    {else}
        decisionPath = decisionPath + '<p>{t}No choices made yet{/t}</p>';
    {/if}
    decisionPath = decisionPath + '</div>';
</script>

<span onclick="allLookupShowDialog()" id="contents-icon" title="{t}Contents{/t}" class="navigation-icon icon-book" />
{t}Contents{/t}</span>

{if $totalSteps > 1}
    <a class="navigation-icon icon-nav-first" id="first-icon" href="../key/index.php?step={$keypath.0.id}" title="{t}Return to first step{/t}">{t}First{/t}</a>
    <a class="navigation-icon icon-nav-prev" id="previous-icon" href="../key/index.php?step={$keypath.$previousStep.id}" 
    title="{t}Previous{/t} {t}step{/t} {$keypath.$previousStep.step_number}{if $v.choice_marker} ({$v.choice_marker}){/if}">
    {t}Previous{/t}</a>
{else}
    <span class="navigation-icon icon-nav-first icon-inactive" id="first-icon-inactive">{t}First{/t}</span>
    <span class="navigation-icon icon-nav-prev icon-inactive" id="previous-icon-inactive">{t}Previous{/t}</span>
{/if}

{if $backlink}
    <a class="navigation-icon icon-nav-back" id="back-icon" href="javascript:history.back()" back-url="{$backlink.url}" title="{t}Back to {/t} {$backlink.name}">{t}Back{/t}</a>
{else}
    <span class="navigation-icon icon-nav-back icon-inactive" id="back-icon-inactive">{t}Back{/t}</span>
{/if}

    {include file="../shared/_back-to-search.tpl"}
    
    <a class="navigation-icon icon-hierarchy" id="decision-path-icon"
    	href='javascript:showDialog("{t}Decision path{/t}",decisionPath);' 
    	title="{t}Decision path{/t}">{t}Decision path{/t}</a>
</div>


	
</div> -->
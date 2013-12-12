{include file="../shared/header.tpl"}
{include file="_search-main-no-tabs.tpl"}

<div id="page-main">
    {include file="_taxa.tpl"}
	<div id="step">
		<div id="question">
        {if $step.image}
            <div id="step-image">
                <img alt="{$step.image}" src="{$projectUrls.uploadedMedia}{$step.image}" />
            </div>
        {/if}
		</div>
		<div id="choices">

{if $choices|@count > 2}
    <table id="choice-grid"><tr><td>
{/if}

{foreach from=$choices key=k item=v}
    {if $k==2}
        </td><td>
    {/if}

    <div class="l2_choice" 
        {if $v.res_keystep_id!='' && $v.res_keystep_id!='-1'}
            onclick="{if $useJavascriptLinks}keyDoChoice({$v.id}){else}window.location.href='../key/index.php?choice={$v.id}&{$addedProjectIDParam}={$session.app.project.id}'{/if}"
        {elseif $v.res_taxon_id!=''}
            onclick="{if $useJavascriptLinks}goTaxon({$v.res_taxon_id}){else}window.location.href='../species/taxon.php?id={$v.res_taxon_id}&{$addedProjectIDParam}={$session.app.project.id}'{/if}"
        {/if}
    >
		
		<div class="l2_text">{$v.choice_txt}</div>
			<div class="target">
			{if $v.res_keystep_id!='' && $v.res_keystep_id!='-1'}
				{if $v.target_number}
				<span class="arrow">&rarr;</span>
				<span>{t}Step{/t} {$v.target_number}{if $v.target_number!=$v.target}: {$v.target}{/if}</span>
				{/if}
			{elseif $v.res_taxon_id!=''}
				<span class="arrow">&rarr;</span><span>{$v.target}</span>
			{/if}
			</div>
		</div>
{/foreach}
{if $choices|@count > 2}
    </td></tr></table>
{/if}
		
		
		
		</div>
	</div>
</div>


{literal}
<script>
$(".inline-image").click(function (e) {
    e.stopPropagation();
});
</script>
{/literal}

{include file="../shared/footer.tpl"}

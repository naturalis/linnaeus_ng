{include file="../shared/header.tpl"}
<p id="header-titles-small">
    <span id="header-title">{t}Step{/t} {$step.number}{if $step.number!=$step.title}. {$step.title}{/if}</span>
</p>
<div id="page-main">    
	<div id="step">
        {if $step.image or ($step.content && $step.content!=$step.title)}
        <div id="question">
            {if $step.image}<img alt="" src="{$step.image}" />{/if}
            {if $step.content && $step.content!=$step.title}{$step.content}{/if}
        </div>
        {/if}
		<div id="choices">
    		{foreach $choices v k}
    			<div class="l2_choice {if !$v.choice_img}no_image{/if}">
                    <div class="l2_text">
                        {if $v.choice_img}
                            <div class="choice-image__container">
                                <img src="{$v.choice_img}">
                            </div>
                        {/if}
                    	{$v.choice_txt}
    				</div>
                    {if $v.res_keystep_id!='' && $v.res_keystep_id!='-1'}
                        <div class="target" onclick="window.open('../key/index.php?choice={$v.id}&{$addedProjectIDParam}={$session.app.project.id}','_self');">
                    {elseif $v.res_taxon_id!=''}
                        <div class="target" onclick="window.open('../species/taxon.php?id={$v.res_taxon_id}&{$addedProjectIDParam}={$session.app.project.id}','_self');">
                    {/if}

                    {if $v.res_keystep_id!='' && $v.res_keystep_id!='-1'}
                        {if $v.target_number}
                            <span>{t}Step{/t} {$v.target_number}{if $v.target_number!=$v.target}: {$v.target}{/if}</span>
                        {/if}
                    {elseif $v.res_taxon_id!=''}
                        <span>{$v.target}</span>
                    {/if}
                        <i class="ion-arrow-right-c"></i>
                    </div>
                </div>
    		{/foreach}
		</div>
	</div>
    {include file="_taxa.tpl"}
</div>

<script>
$(".inline-image").click(function (e)
{
    e.stopPropagation();
});
</script>

{include file="../shared/footer.tpl"}

{include file="../shared/header.tpl"}

<style>
.l2_choice {
	cursor:default;
}
</style>

<div id="page-main">

    {include file="_taxa.tpl"}
	<div id="step">

    <p id="header-titles-small">
    	<span id="header-title" style="white-space:normal">{t}Step{/t} {$step.number}{if $step.number!=$step.title}. {$step.title}{/if}</span>
    </p>

		<div id="question"  style="width:550px;">
            <!--{if $step.image}<img alt="{$step.image}" src="{$projectUrls.uploadedMedia}{$step.image}" style="float:right;margin-left:5px" />{/if}-->
            {if $step.image}<img alt="" src="{$step.image}" style="float:right;margin-left:5px" />{/if}
            {if $step.content && $step.content!=$step.title}{$step.content}{/if}
		</div>

		<div id="choices">

		{foreach $choices v k}

            {* if $v.res_keystep_id!='' && $v.res_keystep_id!='-1'}
			<div class="l2_choice{if !$step.image} no_image{/if}" onclick="window.open('../key/index.php?choice={$v.id}&{$addedProjectIDParam}={$session.app.project.id}','_self');">
            {elseif $v.res_taxon_id!=''}
			<div class="l2_choice{if !$step.image} no_image{/if}" onclick="window.open('../species/taxon.php?id={$v.res_taxon_id}&{$addedProjectIDParam}={$session.app.project.id}','_self');">
            {/if *}

			<div class="l2_choice{if !$step.image} no_image{/if}">

                <div class="l2_text">
                	{$v.choice_txt}
                    {if $v.choice_img}<img style="max-width:100%" src="{$v.choice_img}">{/if}
				</div>

                {* <div class="target"> *}

                {if $v.res_keystep_id!='' && $v.res_keystep_id!='-1'}
                <div class="target" onclick="window.open('../key/index.php?choice={$v.id}&{$addedProjectIDParam}={$session.app.project.id}','_self');">
                {elseif $v.res_taxon_id!=''}
                <div class="target" onclick="window.open('../species/taxon.php?id={$v.res_taxon_id}&{$addedProjectIDParam}={$session.app.project.id}','_self');">
                {/if}

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

		</div>
	</div>
</div>

<script>
$(".inline-image").click(function (e)
{
    e.stopPropagation();
});
</script>

{include file="../shared/footer.tpl"}

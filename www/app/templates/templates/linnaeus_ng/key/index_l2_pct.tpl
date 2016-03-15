{* linnaeus 2 Picture key; l2 Text key is handled in index.tpl! *}	

{include file="../shared/header.tpl"}
{include file="_search-main-no-tabs.tpl"}
<div id="page-main">
{include file="_taxa.tpl"}
	<div id="step">
		<div id="img-choices">		
	{foreach $choices v k}
	{if $v.choice_img}

        <div class="choice-img-wrapper" 
        {if $v.res_keystep_id!='' && $v.res_keystep_id!='-1'}
            onclick="window.location.href='../key/index.php?choice={$v.id}&{$addedProjectIDParam}={$session.app.project.id}'"
        {elseif $v.res_taxon_id!=''}
            onclick="window.location.href='../species/taxon.php?id={$v.res_taxon_id}&{$addedProjectIDParam}={$session.app.project.id}'"
        {/if}
        style="
            position: absolute;
            left: {$v.choice_image_params.leftpos}px;
            top: {$v.choice_image_params.toppos}px;
            width: {$v.choice_image_params.width}px;">
        <img 
            src="{$projectUrls.uploadedMedia}{$v.choice_img|escape:'url'}" 
            width="{$v.choice_image_params.width}"
            height="{$v.choice_image_params.height}" />
		<div id="txt-choice-img-{$v.id}" style="width:{$v.choice_image_params.width}px;">
			<span class="text-choice-img">{$v.choice_txt|nl2br}</span>
		</div>
		</div>
	
	{/if}
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

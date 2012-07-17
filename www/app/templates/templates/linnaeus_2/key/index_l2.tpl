{* linnaeus 2 Picture key; l2 Text key is handled in index.tpl! *}	

{include file="../shared/header.tpl"}
{include file="_search-main-no-tabs.tpl"}
<div id="page-main">
{include file="_taxa.tpl"}
	<div id="step">
		<div id="img-choices">		
{foreach from=$choices key=k item=v}
	{if $v.choice_img}
        <div class="choice-img-wrapper" 
        {if $v.res_keystep_id!='' && $v.res_keystep_id!='-1'}
            onclick="{if $useJavascriptLinks}keyDoChoice({$v.id}){else}window.location.href='../key/index.php?choice={$v.id}'{/if}"
        {elseif $v.res_taxon_id!=''}
            onclick="{if $useJavascriptLinks}goTaxon({$v.res_taxon_id}){else}window.location.href='../species/taxon.php?id={$v.res_taxon_id}'{/if}"
        {/if}
        style="
            position: absolute;
            left: {$v.choice_image_params.leftpos}px;
            top: {$v.choice_image_params.toppos}px;
            width: {$v.choice_image_params.width}px;">
        <img 
            src="{$session.app.project.urls.uploadedMedia}{$v.choice_img|escape:'url'}" 
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

<!--
{literal}
<script>
var offset = $('#choices').offset();
var height = 0;
$("[id^='choice-img-']").each(function (i,e) {
	var d = $(e).offset();
	$(e).offset({ left: d.left + offset.left, top: d.top + offset.top });
	height = ($(e).height() > height ? $(e).height() : height);

	$('#txt-'+e.id).offset({ left: d.left + offset.left + 10, top: d.top + offset.top + height});
});

$('#choices').height($('#choices').height()+height < $('#taxa').height() ? $('#taxa').height() : $('#choices').height()+height);

</script>
{/literal}
-->
{include file="../shared/footer.tpl"}

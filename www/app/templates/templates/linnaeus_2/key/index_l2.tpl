{* linnaeus 2 legacy key *}	
{include file="../shared/header.tpl"}
{include file="_path.tpl"}
{include file="_taxa.tpl"}
<div id="page-main">
	<div id="step">
		<div id="question">
			<div id="head">
				<span id="step-nr">{$step.number}</span>.
				<span id="step-title">{$step.title}</span>
			</div>
		{if $step.image}
			<div>
				<img alt="{$step.image}" src="{$session.app.project.urls.uploadedMedia}{$step.image}" />
			</div>
		{/if}
			<div id="content">{$step.content}</div>
		</div>
		<div id="choices">		
{foreach from=$choices key=k item=v}
	{if $v.choice_img}
		{if $useJavascriptLinks}
		<img
			alt="{$v.choice_img}" 
			id="choice-img-{$v.id}"
			onclick="{if $v.res_keystep_id!='' && $v.res_keystep_id!='-1'}keyDoChoice({$v.id}){elseif $v.res_taxon_id!=''}goTaxon({$v.res_taxon_id}){/if}"
			style="
				position:absolute;
				left:{$v.choice_image_params.leftpos}px;
				top:{$v.choice_image_params.toppos}px;
				width:{$v.choice_image_params.width}px;
				height:{$v.choice_image_params.height}px;
			"/>
		{else}
			{if $v.res_keystep_id!='' && $v.res_keystep_id!='-1'}
				<a href="../key/index.php?choice={$v.id}">
			{elseif $v.res_taxon_id!=''}
				<a href="../species/taxon.php?id={$v.res_taxon_id}">
			{/if}
			<img
				alt="{$v.choice_img}" 
				id="choice-img-{$v.id}"
				src="{$session.app.project.urls.uploadedMedia}{$v.choice_img|escape:'url'}"
				style="
					position:absolute;
					left:{$v.choice_image_params.leftpos}px;
					top:{$v.choice_image_params.toppos}px;
					width:{$v.choice_image_params.width}px;
					height:{$v.choice_image_params.height}px;
				"/>
			</a>
		{/if}
			<br />
			<a href="javascript:showMedia('{$session.app.project.urls.uploadedMedia}{$v.choice_img|escape:'url'}','{$v.choice_img}');">{t}(enlarge image){/t}</a>
		<div id="txt-choice-img-{$v.id}" style="width:{$v.choice_image_params.width}px;text-align:left">
			<span class="marker">{$v.marker}</span>.
			<span class="text">{$v.choice_txt|nl2br}</span>

			<span class="target">
			{if $v.res_keystep_id!='' && $v.res_keystep_id!='-1'}
				<span class="arrow">&rarr;</span>
				<span class="target-step" onclick="keyDoChoice({$v.id})">{if $v.target_number}{t}Step{/t} {$v.target_number}: {/if}{$v.target}</span>
			{elseif $v.res_taxon_id!=''}
				<span class="arrow">&rarr;</span>
				
				{if $useJavascriptLinks}
				<span class="target-taxon" onclick="goTaxon({$v.res_taxon_id})">
					{t}Taxon:{/t} {$v.target}
					{if $v.is_hybrid==1}<span class="hybrid-marker" title="{t}hybrid{/t}">{$session.app.project.hybrid_marker}</span>{/if}
				</span>
				{else}
				<a class="target-taxon" href="../species/taxon.php?id={$v.res_taxon_id}">
					{t}Taxon:{/t} {$v.target}
				</a>
				{if $v.is_hybrid==1}<span class="hybrid-marker" title="{t}hybrid{/t}">{$session.app.project.hybrid_marker}</span>{/if}
				{/if}
			{/if}
			</span>

		</div>
	{/if}
{/foreach}
		</div>
	</div>
</div>

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

{include file="../shared/footer.tpl"}

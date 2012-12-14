{include file="../shared/header.tpl"}
{include file="_search-main-no-tabs.tpl"}

<div id="page-main">
    {include file="_taxa.tpl"}
	<div id="step">
		<div id="question">
		{if $step.content && $step.content!=$step.title}<div id="content">{$step.content}</div>{/if}
		</div>
		<div id="choices">
		
    <table id="choice-table">
	{foreach from=$choices key=k item=v name=henk}
		 <tr><td class="choice_cell">
			
		    <div class="choice{if $v.choice_img} choice-picture"{else}"{/if} 
		        {if $v.res_keystep_id!='' && $v.res_keystep_id!='-1'}
		            onclick="{if $useJavascriptLinks}keyDoChoice({$v.id}){else}window.location.href='../key/index.php?choice={$v.id}&{$addedProjectIDParam}={$session.app.project.id}'{/if}"
		        {elseif $v.res_taxon_id!=''}
		            onclick="{if $useJavascriptLinks}goTaxon({$v.res_taxon_id}){else}window.location.href='../species/taxon.php?id={$v.res_taxon_id}&{$addedProjectIDParam}={$session.app.project.id}'{/if}"
		        {/if}
		    >

            <table class="wrapper-choice{if $v.choice_img}-picture{/if}">
            <tr><td class="text-cell">
			<span class="marker">{$step.number}{$v.marker}</span>.
				<div class="text">{$v.choice_txt}</div>
			</div>
		
		</td>
		{if $v.choice_img}
			<td class="image-cell" rowspan="2">
				{if $useJavascriptLinks}
					<img
						alt="{t}Choice{/t} {$step.number}{$v.marker}"
						title="{t}Choice{/t} {$step.number}{$v.marker} - {t}Click to enlarge{/t}"
						class="choice-image"
						onclick="showMedia('{$session.app.project.urls.uploadedMedia}{$v.choice_img|escape:'url'}','{$v.choice_img}');" 
						src="{$session.app.project.urls.uploadedMedia}{$v.choice_img|escape:'url'}"
						/>
				{else}
					<a href="javascript:showMedia('{$session.app.project.urls.uploadedMedia}{$v.choice_img|escape:'url'}','{$v.choice_img}');">
					<img
						alt="{t}Choice{/t} {$step.number}{$v.marker}"
						title="{t}Choice{/t} {$step.number}{$v.marker} - {t}Click to enlarge{/t}"
						class="choice-image"
						src="{$session.app.project.urls.uploadedMedia}{$v.choice_img|escape:'url'}"
					/>
					</a>
				{/if}	
			</td>
		{/if}
		</tr>
		<tr><td class="target-cell">
			<div class="target">
			{if $v.res_keystep_id!='' && $v.res_keystep_id!='-1'}
				{if $v.target_number}
				<span class="arrow">&rarr;</span>
				<span>{t}Step{/t} {$v.target_number}{if $v.target_number!=$v.target}: {$v.target}{/if}</span>
				{/if}
			{elseif $v.res_taxon_id!=''}
				<span class="arrow">&rarr;</span>
				<span>{$v.target}
					{if $v.is_hybrid==1}<span class="hybrid-marker" title="{t}hybrid{/t}">{$session.app.project.hybrid_marker}</span>{/if}
				</span>
			{/if}
			</div>
		</td>
		{if $v.choice_img}<td></td>{/if}
		</tr></table>
		
		{if not $smarty.foreach.henk.last}<tr><td class="separator"></td></tr>{/if}

{/foreach}
   
    </td></tr></table>
		
		
		
		</div>
	</div>
</div>


{literal}
<script>

  $(document).ready(function(){
	$(".inline-image").click(function (e) {
	    e.stopPropagation();
	});

	$(".choice-image").click(function (e) {
	    e.stopPropagation();
	});

	var panelTop = $('#panel').offset().top;
	$('#page-main').scroll(function() {
		//alert(panelTop);  
		$('#panel').offset({top: panelTop});
	  });	
  });

</script>
{/literal}

{include file="../shared/footer.tpl"}

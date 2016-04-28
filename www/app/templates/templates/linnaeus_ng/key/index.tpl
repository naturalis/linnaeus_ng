{include file="../shared/header.tpl"}

<div id="page-main">
    {include file="_taxa.tpl"}
	<div id="step">

    <p id="header-titles-small">
    	<span id="header-title">{t}Step{/t} {$step.number}{if $step.number!=$step.title}. {$step.title}{/if}</span>
    </p>

		<div id="question">
		{if $step.content && $step.content!=$step.title}<div id="content">{$step.content}</div>{/if}
		</div>
		<div id="choices">
		
    <table id="choice-table">
	{foreach $choices v k choice_loop}
		 <tr><td class="choice_cell">
			
		    <div class="choice{if $v.choice_img} choice-picture"{else}"{/if} 
		        {if $v.res_keystep_id!='' && $v.res_keystep_id!='-1'}
		            onclick="window.location.href='../key/index.php?choice={$v.id}&{$addedProjectIDParam}={$session.app.project.id}'"
		        {elseif $v.res_taxon_id!=''}
		            onclick="window.location.href='../species/taxon.php?id={$v.res_taxon_id}&{$addedProjectIDParam}={$session.app.project.id}'"
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
                <a href="javascript:showMedia('{$projectUrls.uploadedMedia}{$v.choice_img|escape:'url'}','{$v.choice_img}');">
                <img
                    alt="{t}Choice{/t} {$step.number}{$v.marker}"
                    title="{t}Choice{/t} {$step.number}{$v.marker} - {t}Click to enlarge{/t}"
                    class="choice-image"
                    src="{$projectUrls.uploadedMedia}{$v.choice_img|escape:'url'}"
                />
                </a>
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
				<span class="arrow">&rarr;</span><span>{$v.target}</span>
			{/if}
			</div>
		</td>
		{if $v.choice_img}<td></td>{/if}
		</tr></table>
		
		{if not $smarty.foreach.choice_loop.last}<tr><td class="separator"></td></tr>{/if}

{/foreach}
   
    </td></tr></table>
		
		
		
		</div>
	</div>
</div>


<script>
$(document).ready(function()
{
    $(".inline-image").click(function(e)
    {
	    e.stopPropagation();
    });
    
    $(".choice-image").click(function(e)
    {
    	e.stopPropagation();
    });
    
    var panelTop = $('#panel').offset().top;
    $('#page-main').scroll(function()
    {
	    $('#panel').offset({top: panelTop});
    });	
});
</script>


{include file="../shared/footer.tpl"}

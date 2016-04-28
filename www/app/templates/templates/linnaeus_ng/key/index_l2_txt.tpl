{include file="../shared/header.tpl"}

<div id="page-main">
    {include file="_taxa.tpl"}
	<div id="step">

    <p id="header-titles-small">
    	<span id="header-title">{t}Step{/t} {$step.number}{if $step.number!=$step.title}. {$step.title}{/if}</span>
    </p>
  
		<div id="question">

        {if $step.image}
            <div id="step-image">
                <img alt="{$step.image}" src="{$projectUrls.uploadedMedia}{$step.image}" />
            </div>
        {/if}

		{if $step.content && $step.content!=$step.title}
            <div id="content" style="width:550px;padding:0 0 0 10px;">
                {$step.content}
            </div>
        {/if}

		</div>
		<div id="choices">

	{if $choices|@count > 2}
    <table id="choice-grid"><tr><td>
	{/if}

	{foreach $choices v k}
    {if $k==2}
        </td><td>
    {/if}

    <div class="l2_choice" 
        {if $v.res_keystep_id!='' && $v.res_keystep_id!='-1'}
            onclick="window.location.href='../key/index.php?choice={$v.id}&{$addedProjectIDParam}={$session.app.project.id}'"
        {elseif $v.res_taxon_id!=''}
            onclick="window.location.href='../species/taxon.php?id={$v.res_taxon_id}&{$addedProjectIDParam}={$session.app.project.id}'"
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

<script>
$(".inline-image").click(function (e)
{
    e.stopPropagation();
});
</script>

{include file="../shared/footer.tpl"}

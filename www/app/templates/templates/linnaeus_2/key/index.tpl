{include file="../shared/header.tpl"}
{include file="_path.tpl"}

<div id="page-main">
    {include file="_taxa.tpl"}
	<div id="step">
		<div id="question">
		{if $keyType=="lng"}
            <div id="content">{$step.content}</div>
	    {else}
            {if $step.image}
                <div id="step-image">
                    <img alt="{$step.image}" src="{$session.app.project.urls.uploadedMedia}{$step.image}" />
                </div>
            {/if}
	    {/if}
		</div>
		<div id="choices">

{if $keyType=="l2" && $choices|@count > 2}
    <table id="choice-grid"><tr><td>
{/if}
{foreach from=$choices key=k item=v}
    {if $keyType=="l2" && $k==2}
        </td><td>
    {/if}

    <div class="choice" 
        {if $v.res_keystep_id!='' && $v.res_keystep_id!='-1'}
            onclick="{if $useJavascriptLinks}keyDoChoice({$v.id}){else}window.location.href='../key/index.php?choice={$v.id}'{/if}"
        {elseif $v.res_taxon_id!=''}
            onclick="{if $useJavascriptLinks}goTaxon({$v.res_taxon_id}){else}window.location.href='../species/taxon.php?id={$v.res_taxon_id}'{/if}"
        {/if}
    >
	           {if $v.choice_img}
					<div class="choice-image-div">
					{if $useJavascriptLinks}
						<img
							alt="{$v.choice_img}"
							class="choice-image-small"
							onclick="{if $v.res_keystep_id!='' && $v.res_keystep_id!='-1'}keyDoChoice({$v.id}){elseif $v.res_taxon_id!=''}goTaxon({$v.res_taxon_id}){/if}" 
							src="{$session.app.project.urls.uploadedMedia}{$v.choice_img|escape:'url'}"
							/>
					{else}
					{if $v.res_keystep_id!='' && $v.res_keystep_id!='-1'}
						<a href="../key/index.php?choice={$v.id}">
					{elseif $v.res_taxon_id!=''}
						<a href="../species/taxon.php?id={$v.res_taxon_id}">
					{/if}
						<img
							alt="{$v.choice_img}"
							class="choice-image-small"
							src="{$session.app.project.urls.uploadedMedia}{$v.choice_img|escape:'url'}"
						/>
						</a>
					{/if}						
						
						
						<br />
						<a href="javascript:showMedia('{$session.app.project.urls.uploadedMedia}{$v.choice_img|escape:'url'}','{$v.choice_img}');">{t}(enlarge image){/t}</a>
					</div>
	{/if}
					{if $keyType=="lng"}<span class="marker">{$v.marker}</span>.{/if}
					<div class="text">{$v.choice_txt|nl2br}</div>
					

					<div class="target">
					{if $v.res_keystep_id!='' && $v.res_keystep_id!='-1'}
						<span class="arrow">&rarr;</span>
						{if $useJavascriptLinks}
						<span class="target-step" onclick="keyDoChoice({$v.id})">{if $v.target_number}{t}Step{/t} {$v.target_number}: {/if}{$v.target}</span>
						{else}
						<a class="target-step" href="../key/index.php?choice={$v.id}">
							{if $v.target_number}{t}Step{/t} {$v.target_number}: {/if}{$v.target}
						</a>
						{/if}
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


					</div>

			</div>
{/foreach}
{if $keyType=="l2" && $choices|@count > 2}
    </td></tr></table>
{/if}
		
		
		
		</div>
	</div>
</div>

{include file="../shared/footer.tpl"}

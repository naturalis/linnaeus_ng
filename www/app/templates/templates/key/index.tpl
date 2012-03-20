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
				<img alt="{$step.image}" src="{$session.app.project.urls.project_media}{$step.image}" />
			</div>
		{/if}
			<div id="content">{$step.content}</div>
		</div>
		<div id="choices">

{foreach from=$choices key=k item=v}
			<div class="choice">
	{if $v.choice_img}
					<div class="choice-image-div">
					{if $useJavascriptLinks}
						<img
							alt="{$v.choice_img}"
							class="choice-image-small"
							onclick="{if $v.res_keystep_id!='' && $v.res_keystep_id!='-1'}keyDoChoice({$v.id}){elseif $v.res_taxon_id!=''}goTaxon({$v.res_taxon_id}){/if}" 
							src="{$session.app.project.urls.project_media}{$v.choice_img|escape:'url'}"
							/>
					{else}
					{if $v.res_keystep_id!='' && $v.res_keystep_id!='-1'}
					keyDoChoice({$v.id})
					{elseif $v.res_taxon_id!=''}
						<a href="../species/taxon.php?id={$v.res_taxon_id}">
					{/if}
						<img
							alt="{$v.choice_img}"
							class="choice-image-small"
							src="{$session.app.project.urls.project_media}{$v.choice_img|escape:'url'}"
						/>
					{/if}						
						
						
						<br />
						<a href="javascript:showMedia('{$session.app.project.urls.project_media}{$v.choice_img|escape:'url'}','{$v.choice_img}');">{t}(enlarge image){/t}</a>
					</div>
	{/if}
					<span class="marker">{$v.marker}</span>.
					<span class="text">{$v.choice_txt|nl2br}</span>
					<br />
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
{/foreach}
		</div>
	</div>
</div>

{include file="../shared/footer.tpl"}

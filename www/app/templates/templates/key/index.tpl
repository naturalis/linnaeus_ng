{include file="../shared/header.tpl"}

<div id="path">
	<div id="concise">
	<span onclick="keyToggleFullPath()" id="toggle">{t}Path:{/t}</span>
	{foreach from=$keypath key=k item=v}
	{if $v.is_start==1 || $keypath|@count<=$keyPathMaxItems || ($keypath|@count>$keyPathMaxItems && $k>=$keypath|@count-2)}
		{if $v.is_start!=1}<span class="arrow">&rarr;</span>{/if}
		{$v.step_number}. <span class="item" onclick="keyDoStep({$v.id})">{$v.step_title}{if $v.choice_marker} ({$v.choice_marker}){/if}</span>
	{/if}
	{if $v.is_start==1 && $keypath|@count>$keyPathMaxItems}<span class="arrow">&rarr;</span><span class="abbreviation">[...]</span>{/if}
	{/foreach}
	</div>
	<div id="path-full" class="full-invisible">
	<table>
	{foreach from=$keypath key=k item=v}
		<tr>
			<td class="number-cell">{$v.step_number}. </td>
			<td><span class="item" onclick="keyDoStep({$v.id})">{$v.step_title}{if $v.choice_marker} ({$v.choice_marker}){/if}</span></td>
		</tr>
	{/foreach}
	</table>
	</div>
</div>

<div id="taxa">
{if $taxa|@count==1}{assign var=w value=taxon}{else}{assign var=w value=taxa}{/if}
<span id="header">{t _s1=$taxa|@count _s2=$w}%s possible %s remaining:{/t}</span>
	<select id="list" size="25">
{foreach from=$taxa key=k item=v}
		<option class="item" onclick="goTaxon({$v.id})">
			{$v.taxon}
			{if $v.is_hybrid==1}{$session.project.hybrid_marker}{/if}
		</option>
{/foreach}
	</select>
</div>
	
<div id="page-main">
	<div id="step">
		<div id="question">
			<div id="head">
				<span id="step-nr">{$step.number}</span>.
				<span id="step-title">{$step.title}</span>
			</div>
			<div id="content">{$step.content}</div>
		</div>
		<div id="choices">

{foreach from=$choices key=k item=v}
			<div class="choice">
	{if $v.choice_img}
					<img
						class="image-small"
						onclick="showMedia('{$session.project.urls.project_media}{$v.choice_img|escape:'url'}','{$v.choice_img}');" 
						src="{$session.project.urls.project_media}{$v.choice_img|escape:'url'}" />
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
						<span class="target-taxon" onclick="goTaxon({$v.res_taxon_id})">
							{t}Taxon:{/t} {$v.target}
							{if $v.is_hybrid==1}<span class="hybrid-marker" title="{t}hybrid{/t}">{$session.project.hybrid_marker}</span>{/if}
							</span>
					{/if}
					</span>

			</div>
{/foreach}
		</div>
	</div>
</div>

{include file="../shared/footer.tpl"}

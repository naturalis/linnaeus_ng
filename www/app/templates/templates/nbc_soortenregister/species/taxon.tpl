{include file="../shared/header.tpl"}
{literal}
<style>
.taxon-image-table {
	font-size:9px;
	color:#666;
}
</style>
{/literal}
<div id="dialogRidge">

	{include file="_left_column.tpl"}
	
	<div id="content">
		<div id="taxonHeader" class="hasImage">
			<div id="titles" class="">
				<h1>
				{if $names.list[$names.prefId]}
					{$names.list[$names.prefId].name}
				{elseif $names.list[$names.sciId]}
					{$names.list[$names.sciId].uninomial} {$names.list[$names.sciId].specific_epithet}
				{else}
					{$taxon.label}
				{/if}
				</h1>
				{if $names.list[$names.prefId] && $names.list[$names.sciId]}
					<h2 style="width:510px"><i>{$names.list[$names.sciId].uninomial} {$names.list[$names.sciId].specific_epithet}</i></h2>
				{else}
					<h2 style="width:510px">&nbsp;</h2>
				{/if}
			</div>
			{if $overviewImage.image}
			<div id="taxonImage">
				<img src="{$overviewImage.image}" />
				<div id="taxonImageCredits">
					<span class="photographer-title">Foto</span>
					{assign var=name value=", "|explode:$overviewImage.label} 
					{$name[1]} {$name[0]}
				</div>
			</div>
			{/if}
		</div>

		{if $activeCategory=='media'}

			<h4>Afbeelding{if $content|@count!=1}en{/if}: {$content|@count}</h4>
			<div>
				{foreach from=$content item=v}
				{assign var=name value=", "|explode:$v.description} 
				<div class="thumbholder">
					<div class="thumbnail">
						<a class="zoomimage" href="{$v.file_name}">
							<img src="{$v.thumb_name}" title="foto {$name[1]} {$name[0]}" alt="foto {$name[1]} {$name[0]}">
						</a>
					</div>
					<p class="author">
						<span class="photographer-title">Foto</span>
						{$name[1]} {$name[0]}
					</p>
				</div>
				{/foreach}
			</div>

		{else}
		<p>
		
			{if $categorySysList[$activeCategory]=='Nomenclature'}
				<p>
					<h2>Naamgeving</h2>
					<table>
						{assign var=prevExpert value=-1}
						{foreach from=$names.list item=v}
							<tr><td>{$v.nametype}</td><td><a>{$v.name}</a></td><td>{$v.language}</td></tr>
							{if $prevExpert!=$v.expert_id && $prevExpert!=-1}
							<tr><td>Expert</td><td colspan="2">{$v.expert_id}</td></tr>
							{/if}
							{assign var=prevExpert value=$v.expert_id}
						{/foreach}			
						<tr><td>Expert</td><td colspan="2">{$v.expert_id}</td></tr>
					</table>
				</p>

				<p>
				<h2>Indeling</h2>
					<table id="name-tree">
						{foreach from=$classification item=v key=x}
						{if $v.parent_id!=null}{* skipping top most level "life" *}
						{math equation="((x-2) * 5)" x=$x assign=buffercount}
						<tr><td>
							{if $x>1}
							{'&nbsp;'|str_repeat:$buffercount}
							<span class="classification-connector">&lfloor;</span>
							{/if}
							<span class="classification-preffered-name"><a href="?id={$v.id}">{$v.taxon}</a></span>
							<span class="classification-rank">[{$ranks[$v.rank_id].rank}]</span>
							{if $v.preferredName}<br />
							{if $x>1}
							{'&nbsp;'|str_repeat:$buffercount}
							<span class="classification-connector-invisible">&lfloor;</span>
							{/if}
							<span class="classification-preffered-name">{$v.preferredName}</span>{/if}
						</td></tr>
						{/if }
						{/foreach}			
					</table>
				</p>
			{/if}
		
			{if $content|@is_array}
			<ul>
			{foreach from=$content item=v key=k}
			{if $k>0}<li><a href="taxon.php?id={$v.id}">{$v.label}</a></li>{/if}
			{/foreach}
			</ul>
			{else}
			<p>
			{$content}
			</p>
			{/if}
		</p>
		<h2>Bron</h2>
		<p>
			<h4 class="source">Auteur(s)</h4>
		</p>
		<p>
			<h4 class="source">Publicatie</h4>
			<ul class="reference">
				<li></li>
			</ul>
		</p>
		{/if}
	</div>

	{include file="../shared/_right_column.tpl"}

</div>
	
	
    
{literal}
<script type="text/JavaScript">
$(document).ready(function(){
});
</script>
{/literal}

{include file="../shared/footer.tpl"}
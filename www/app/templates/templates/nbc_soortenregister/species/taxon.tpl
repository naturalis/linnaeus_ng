{include file="../shared/header.tpl"}
{literal}
<style>
#left li {
	list-style:inside;
	list-style-type:none;
    background:none;
    margin-bottom: 5px;
    margin-left: 0;
    padding-left: 0px;
    position: relative;
}
#left #categories {
	margin-bottom:25px;
}

table tr td {
	padding-right:25px;
}
.classification-name.smaller {
	font-size:10px;
}
.classification-connector {
	color:#666;
}
.classification-connector-invisible {
	visibility:hidden;
}
.classification-rank {
	font-size:9px;
	padding-left:2px;
}
.classification-rank .smaller{
	font-size:9px;
}
.classification-preffered-name {
	font-size:11px;
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
					<h2><i>{$names.list[$names.sciId].uninomial} {$names.list[$names.sciId].specific_epithet}</i></h2>
				{/if}
			</div>
			<div id="taxonImage">
				<img src="http://images.ncbnaturalis.nl/510x272/236381.jpg" />
				<div id="taxonImageCredits">
					<span class="photographer-title">Foto</span>
					Wijnand van Buuren, 5 juni 2013, Ermelo, Groevenbeekse heide
				</div>
			</div>
		</div>
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
			{$content}
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
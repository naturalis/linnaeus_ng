{include file="../shared/header.tpl"}
<div id="page-main">
{if !$isOnline}
{t}Your computer appears to be offline. Unfortunately, the map doesn't work without an internet connection.{/t}
{else}
	<div id="index">
		<p>
		{t}Click a species to examine{/t} ({t}switch to {/t}<a href="compare.php">{t}species comparison{/t}</a>{t} or {/t}<a href="search.php">{t}map search{/t}</a>)
		</p>
		<table>
		<tr>
			<th>{t}Taxon{/t}</th>
			<th>{t}Number of geo entries{/t}</td>
		</tr>
		{foreach name=taxonloop from=$taxa key=k item=v}
		<tr class="highlight">

			{if $useJavascriptLinks}
			<td class="a" onclick="goMap({$v.id})" style="width:250px;">
				{$v.taxon}
				{if $v.is_hybrid==1}<span class="hybrid-marker" title="{t}hybrid{/t}">{$session.app.project.hybrid_marker}</span>{/if}
			</td>
			{else}
			<td style="width:250px;">
				<a href="../mapkey/examine_species.php?id={$v.id}">
				{$v.taxon}
				{if $v.is_hybrid==1}<span class="hybrid-marker" title="{t}hybrid{/t}">{$session.app.project.hybrid_marker}</span>{/if}
				</a>
			</td>
			{/if}

			<td style="text-align:right">
				{$v.total}
			</td>
		</tr>
		{/foreach}
		</table>
	</div>
{if $prevStart!=-1 || $nextStart!=-1}
	<div id="navigation">
		{if $prevStart!=-1}
		<span class="a" onclick="goNavigate({$prevStart});">< {t}previous{/t}</span>
		{/if}
		{if $nextStart!=-1}
		<span class="a" onclick="goNavigate({$nextStart});">{t}next{/t} ></span>
		{/if}
	</div>
{/if}




{/if}
</div>

{include file="../shared/footer.tpl"}

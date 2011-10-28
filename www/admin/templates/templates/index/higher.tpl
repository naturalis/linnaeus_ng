{include file="../shared/admin-header.tpl"}

<div id="page-main">
	<div id="index">
		<table>
		{foreach name=taxonloop from=$taxa key=k item=v}
		<tr class="highlight">
			<td class="a" onclick="goTaxon({$v.id})">
				{$v.label}
				{if $v.is_hybrid==1}<span class="hybrid-marker" title="{t}hybrid{/t}">{$session.project.hybrid_marker}</span>{/if}
			</td>
			<td>({$ranks[$v.rank_id].rank})</td>
		</tr>
		{/foreach}
		</table>
	</div>
{if $prevStart!=-1 || $nextStart!=-1}
	<div id="navigation">
		{if $prevStart!=-1}
		<span class="pseudo-a" onclick="goNavigate({$prevStart});">< {t}previous{/t}</span>
		{/if}
		{if $nextStart!=-1}
		<span class="pseudo-a" onclick="goNavigate({$nextStart});">{t}next{/t} ></span>
		{/if}
	</div>
{/if}
</div>
<form name="theForm" id="theForm" method="post" action="">
</form>

{include file="../shared/admin-messages.tpl"}
{include file="../shared/admin-footer.tpl"}

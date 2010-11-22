{include file="../shared/admin-header.tpl"}

<div id="page-main">
<p>{t}
Below, you can define taxa of what rank or ranks will be part of your dichotomous key. Only taxa of ranks that are part of the "species module" can be part of the key, and only those are displayed here. To change the distinction between the "higher taxa" and "species" modules, click{/t} <a href="../species/ranks.php">{t}here{/t}</a>.<br />
</p>
<p>
{t}The taxa that are below the red line in the list below are available in your key. To change the selection, move the red line up or down by clicking the &uarr; and &darr; arrows. To include all ranks, move the line to the top of the list, above the first rank. As at least one rank is required to be included, the line cannot be moved below the lowest rank. When you are satisfied with your selection, click the save-button.{/t}
</p>
<p>
{t}Please note that changing this setting will not detach any taxa that have already been attached to an end-point of your key. Taxa that have a rank that is no longer part of the selection below will remain connected to the key, until you manually detach them.{/t} </p>
<form action="" method="post">
<input type="hidden" name="rnd" value="{$rnd}" />
<div id="selected-ranks" style="width:250px;margin-bottom:5px;"></div>
<input type="submit" value="{t}save{/t}" />
</form>
</div>

{literal}
<script type="text/JavaScript">
$(document).ready(function(){
{/literal}
{assign var=first value=true}
{section name=i loop=$projectRanks}
	keyAddRank({$projectRanks[i].rank_id},'{$projectRanks[i].rank}');
	{if $first && $projectRanks[i].keypath_endpoint == 1}
	keyRankBorder = {$projectRanks[i].rank_id};
	{assign var=first value=false}
	{/if}
{/section}
	keyShowRanks();
{literal}
});
</script>
{/literal}


{include file="../shared/admin-footer.tpl"}

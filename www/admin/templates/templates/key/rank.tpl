{include file="../shared/admin-header.tpl"}

<div id="page-main">
<p>
    {t}Below, you can define taxa of what rank or ranks will be part of your dichotomous key.{/t}<br />
    {t}The taxa that are of a rank below the red line in the list below are available in your key. To change the selection, move the red line up or down by clicking the &uarr; and &darr; arrows. To include all ranks, move the line to the top of the list, above the first rank. As at least one rank is required to be included, the line cannot be moved below the lowest rank. When you are satisfied with your selection, click the save-button.{/t}
</p>
<p>
	{t}Please note that changing this setting will not detach any taxa that have already been attached to an end-point of your key. Taxa that have a rank that is no longer part of the selection below will remain connected to the key, until you manually detach them.{/t}
</p>
<p>
	{t}Also, be aware that the same setting is used for determining taxa of which rank can be part of the multi-entry key (if you are using that).{/t}
</p>
<form action="" method="post">
<input type="hidden" name="rnd" value="{$rnd}" />
<div id="selected-ranks" style="width:250px;margin-bottom:5px;"></div>
<input type="submit" value="{t}save{/t}" />
</form>
</div>

<script type="text/JavaScript">
$(document).ready(function()
{
{assign var=first value=true}
{foreach $projectRanks v i}
	keyAddRank({$v.rank_id},'{$v.rank}');
	{if $first && $v.keypath_endpoint == 1}
	keyRankBorder = {$v.rank_id};
	{assign var=first value=false}
	{/if}
{/foreach}
	keyShowRanks();
});
</script>



{include file="../shared/admin-messages.tpl"}
{include file="../shared/admin-footer.tpl"}

{include file="../shared/admin-header.tpl"}
{include file="../shared/admin-messages.tpl"}

<div id="page-main">
<p>
Click the arrow next to a rank to add that rank to the selection used in this project. Currently selected ranks are shown on the right. To remove a rank from the selection, double click it in the list on the right. The uppermost rank, {$ranks[0].rank}, is mandatory and cannot be deleted.
<br />
Click <span onclick="taxonAddCoLRanks()" class="pseudo-a">here</span> to select all the ranks used in Catalogue Of Life, marked with an asterisk in the list below.
<br />
After you have made the appropriate selection, click the save-button.
Once you have saved the selection, you can change the ranks' names and provide translations <a href="ranklabels.php">here</a>.<br />
</p>
{if $projectRanks|@count>0}
<p>
<b>Please be advised:</b> deleting previously defined ranks to which taxa already have been assigned will leave those taxa without rank.
</p>
{/if}
<p>
<input type="button" value="save selected ranks" onclick="taxonSaveRanks()" />
</p>
<table id="ranks-table">
<tr><td colspan="2" class="rank-header">Ranks:</td></tr>
{assign var=first value=true}
{section name=i loop=$ranks}
{if $ranks[i].parent_id==-1 && $first}
<tr><td colspan="2">&nbsp;</td></tr>
{assign var=first value=false}
{/if}
<tr class="tr-highlight">
	<td><span class="rank-name{if $ranks[i].in_col==1}-col{/if}" id="rank-{$ranks[i].id}">{$ranks[i].rank}</span>{if $ranks[i].additional!=''}<span class="rank-additional">({$ranks[i].additional})</span>{/if}{if $ranks[i].in_col==1} *{/if}</td>
	<td onclick="taxonAddRank({$ranks[i].id}{if $ranks[i].parent_id==-1},true{/if});" class="add-arrow" id="arrow-{$ranks[i].id}">&rarr;</td>
</tr>
{/section}
</table>

<div id="floating-div" style="position:absolute;">Selected ranks <span ondblclick="taxonRemoveAll()">(double click to delete)</span>:<br />
	<select size="35" id="selected-ranks" style="width:250px;"></select></div>
</div>

<form method="post" id="theForm" action="">
</form>

<script type="text/javascript">
{literal}

$(document).ready(function(){

	var f = $('#floating-div');
	
	var offset = $('#ranks-table').offset();
	f.offset({left : offset.left + $('#ranks-table').width() + 25, top: offset.top});

	$(window).scroll(function() {

		var offset = $('#ranks-table').offset();
		var newtop = (offset.top > ($(window).scrollTop()+25)) ? offset.top : $(window).scrollTop() + 25;

		f.offset({left : offset.left + $('#ranks-table').width() +25, top: newtop });

	});
{/literal}
	taxonKingdom = [{$ranks[0].id},'{$ranks[0].rank}'];
{section name=i loop=$ranks}
{if $ranks[i].in_col==1}	taxonCoLRanks[taxonCoLRanks.length]=[{$ranks[i].id},'{$ranks[i].rank}'];
{/if}
{/section}
{if $projectRanks|@count>0}
{section name=i loop=$projectRanks}
	taxonAddRank({$projectRanks[i].rank_id});
{/section}
{else}
	taxonAddRank(taxonKingdom[0]);
{/if}
{literal}
});
{/literal}
</script>

{include file="../shared/admin-footer.tpl"}
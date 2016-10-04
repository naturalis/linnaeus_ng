{include file="../shared/admin-header.tpl"}

<div id="page-main">

    <p>
        {t _s1=$ranks[0].rank}Click the arrow next to a rank to add that rank to the selection used in this project. Currently selected ranks are shown on the right. To remove a rank from the selection, double click it in the list on the right. The uppermost rank, %s, is mandatory and cannot be deleted.{/t}
    </p>

    <p>
        <span onclick="taxonAddCoLRanks()" class="a">{t}Select all the ranks used in Catalogue Of Life, marked in blue in the list below{/t}</span>.
        <br />
        {t}After you have made the appropriate selection, click the save-button.  Once you have saved the selection, you can {/t}<a href="ranklabels.php">{t}change the ranks' names and provide translations{/t}</a>.<br />
    </p>

    <p>
        {t}In addition, you can specify where the distinction between the modules "higher taxa" and "species" will be. You can move the line by clicking the &uarr; and &darr; arrows. The setting is saved when you click{/t} "{t}save selected ranks{/t}".<br />
        {t}Be advised that this division is different from the one that defines which taxa can be the end-point of your keys. That division is defined in the single-access key and multi entry key modules.{/t}
    </p>
    
    {if $projectRanks|@count>0}
    <p>
        <b>{t}Please be advised:{/t}</b> {t}deleting previously defined ranks to which taxa already have been assigned will leave those taxa without rank.{/t}
    </p>
    {/if}

    <p>
        <form method="post" id="theForm" action="">
        <input type="hidden" name="rnd" value="{$rnd}" />
        <input type="button" value="{t}save selected ranks{/t}" onclick="taxonSaveRanks()" />
    </p>

</div>

{include file="../shared/admin-messages.tpl"}

<div class="page-generic-div">
	<table id="ranks-table">
		<tr>
        	<td colspan="2" class="rank-header">{t}Ranks:{/t}</td>
		</tr>
        {assign var=first value=true}
        {foreach $ranks v i}
            {if $v.parent_id==-1 && $first}
            {assign var=first value=false}
            <tr><td colspan="2">&nbsp;</td></tr>
            {/if}
        <tr class="tr-highlight" style="cursor:pointer" onclick="taxonAddRank({$v.id}{if $v.parent_id==-1},true{/if});" >
            <td{if $v.in_col==1} class="col-rank"{/if}>
                <span class="rank-name{if $v.in_col==1}-col{/if}" id="rank-{$v.id}">
                    {$v.rank}
                </span>{if $v.additional!=''}<span class="rank-additional">({$v.additional})</span>{/if}{* $v.ideal_parent_id *}
            </td>
            <td class="add-arrow" id="arrow-{$v.id}">
                >
            </td>
        </tr>
		{/foreach}
	</table>

<div id="floating-div" style="position:absolute; top: 0; left: 0; visibility: hidden;">{t}Selected ranks{/t} <span ondblclick="taxonRemoveAll()">{t}(double click to delete){/t}</span>:<br />
<div id="selected-ranks"></div>

</form>

<script type="text/javascript">

$(document).ready(function()
{
	var f = $('#floating-div');
	var offset = $('#ranks-table').offset();
	f.offset( { left : offset.left + $('#ranks-table').width() + 25, top: offset.top } );
	f.css('visibility', 'visible');
	
	$(window).scroll(function()
	{
		var offset = $('#ranks-table').offset();
		var newtop = (offset.top > ($(window).scrollTop()+25)) ? offset.top : $(window).scrollTop() + 25;
		f.offset( { left : offset.left + $('#ranks-table').width() +25, top: newtop } );
	});

	taxonKingdom = [{$ranks[0].id},'{$ranks[0].rank}'];
{foreach $ranks v}
{if $v.in_col==1}
	taxonCoLRanks[taxonCoLRanks.length]=[{$v.id},'{$v.rank}'];
{/if}
{/foreach}

{if $projectRanks|@count>0}
{assign var=first value=true}
{foreach $projectRanks v}
	taxonAddRank({$v.rank_id},false,false);
	{if $v.lower_taxon==1 && $first==true}
	taxonRankBorder = {$v.rank_id};
	{assign var=first value=false}
	{/if}
{/foreach}
	taxonShowSelectedRanks();
{else}
	taxonAddRank(taxonKingdom[0]);
{/if}

});

</script>

{include file="../shared/admin-footer.tpl"}

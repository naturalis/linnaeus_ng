{include file="../shared/admin-header.tpl"}
{literal}
<style>
#ranks-table{
	border-collapse:collapse;
}
.rank-header {
	text-decoration:underline;
}
.rank-name {
}
.rank-name-col{
	 font-weight:bold;
}
.rank-additional {
	color:#777;
	padding-left:6px;
}
.rank-add-arrow {
	font-size:16px;
	cursor:pointer;
	padding-left:7px;	
	padding-right:7px;
}
</style>
{/literal}

<div id="page-main">
<p>
Click the arrow next to a rank to add that rank to the selection used in this project. Currently selected ranks are shown on the right. To remove a rank from the selection, double click it in the list on the right. The uppermost rank, {$ranks[0].rank}, is mandatory and cannot be deleted.
</p>
<p>
Click <span onclick="taxonAddCoLRanks()" class="pseudo-a">here</span> to select the ranks used in Catalogue Of Life, marked with an asterisk in the list below.
</p>
<p>
After you have made the appropriate selection, click the save-button.
Once you have saved the selection, you can change the ranks' names and provide translations <a href="">here</a>.<br />
</p>
<p>
<input type="button" value="save selected ranks" />
</p>
<p>
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
	<td onclick="taxonAddRank({$ranks[i].id});" class="rank-add-arrow" id="arrow-{$ranks[i].id}">&rarr;</td>
</tr>
{/section}
</table>
</p>
<div id="floating-div" style="position:absolute;">Selected ranks <span ondblclick="taxonRemoveAll()">(double click to delete)</span>:<br />
	<select size="35" id="selected-ranks" style="width:250px;"></select></div>
		
<!--
		$this->smarty->assign('',$r);

		$this->smarty->assign('hybrids',$h);

		$this->smarty->assign('projectRanks',$pr);

		$this->smarty->assign('languages',$lp);
-->


</div>
{literal}
<script>

var taxonKingdom = Array();
var taxonAddedRanks = Array();
var taxonCoLRanks = Array();

function taxonAddCoLRanks() {
	taxonAddedRanks = taxonCoLRanks;
	taxonShowSelectedRanks();
}

function taxonAddRank(id) {
	taxonAddedRanks[taxonAddedRanks.length]=[id,$('#rank-'+id).html()];
	taxonOrderSelectedRanks();
	taxonShowSelectedRanks();
}

function taxonRemoveRank(id) {
	if (id==taxonKingdom[0]) return;
	var t = Array();

	for (var i=0;i<taxonAddedRanks.length;i++) {
		if (taxonAddedRanks[i][0]!=id && id != null) {
			t[t.length]=taxonAddedRanks[i];
		}
	}

	taxonAddedRanks = t;
	
	if (taxonAddedRanks.length==0) taxonAddRank(taxonKingdom[0]);

	taxonOrderSelectedRanks();
	taxonShowSelectedRanks();
}

function taxonRemoveAll() {
	taxonRemoveRank();
	taxonShowSelectedRanks();	
}

function sortRankArray(a,b) {

	return (a[0] > b[0] ? 1 : (a[0] < b[0] ? -1 : 0));

}

function taxonOrderSelectedRanks() {
	var d = '|';
	var t = Array();
	for (var i=0;i<taxonAddedRanks.length;i++) {
		if (d.indexOf('|'+taxonAddedRanks[i][0]+'|')==-1) {
			d = d + taxonAddedRanks[i][0] + '|';
			t[t.length]=taxonAddedRanks[i];
		}
	}

	t.sort(sortRankArray)

	taxonAddedRanks = t;

}

function taxonShowSelectedRanks() {
	
	$('#selected-ranks').children().remove();

	for (var i=0;i<taxonAddedRanks.length;i++) {
		$('<option id="sel-rank-'+taxonAddedRanks[i][0]+'">').val(taxonAddedRanks[i][0]).text(taxonAddedRanks[i][1]).appendTo('#selected-ranks');
		$('#sel-rank-'+taxonAddedRanks[i][0]).dblclick( function () { taxonRemoveRank(this.value); });
		
	}

}


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
{if $ranks[i].in_col==1}taxonCoLRanks[taxonCoLRanks.length]=[{$ranks[i].id},'{$ranks[i].rank}'];
{/if}
{/section}
taxonAddRank(taxonKingdom[0]);
{literal}
});
{/literal}
</script>
{include file="../shared/admin-messages.tpl"}
{include file="../shared/admin-footer.tpl"}

{include file="../shared/admin-header.tpl"}

<div id="page-main">

<span id="message-container" style="float:right;"></span>
<br />

<table>
<tr>
	<td></td>
{section name=i loop=$languages}
{if $languages[i].def_language=='1'}
	<td {$languages[i].language_id}>{$languages[i].language} *</td>
{/if}
{/section}
<td id="language-tabs">(languages)</td>
</tr>
{section name=i loop=$projectRanks}
<tr>
	<td>{$projectRanks[i].rank}</td>
	<td><input type="text" id="default-{$projectRanks[i].id}" maxlength="64" onblur="taxonSaveRankLabel({$projectRanks[i].id},this.value,'default')" /></td>
	<td><input type="text" id="other-{$projectRanks[i].id}" maxlength="64" onblur="taxonSaveRankLabel({$projectRanks[i].id},this.value,'other')" /></td>
</tr>
{/section}
</table>



<script type="text/javascript">
{literal}

var taxonRanks = Array();

function taxonAddRank(rank) {

	taxonRanks[taxonRanks.length] = rank;

}

function taxonSwitchRankLanguage(language) {

	taxonActiveLanguage = language;
	taxonDrawRankLanguages();
	taxonGetRankLabels(taxonActiveLanguage);

}

function taxonDrawRankLanguages() {
	
	var b='';

	for(var i=0;i<taxonLanguages.length;i++) {
		if (taxonLanguages[i][2]!=1) {
			b = b + 
				'<span class="rank-language'+(taxonLanguages[i][0]==taxonActiveLanguage ? '-active' : '' )+'" onclick="taxonSwitchRankLanguage('+ taxonLanguages[i][0] +')">' + 
				taxonLanguages[i][1] + 
				'</span>&nbsp;';
		} else {
			taxonDefaultLanguage = taxonLanguages[i][0];
		}
	}

	$('#language-tabs').html(b);
}

function taxonSaveRankLabel(id,label,type) {

	allAjaxHandle = $.ajax({
		url : "ajax_interface.php",
		type: "POST",
		data : ({
			'action' : 'save_rank_label' ,
			'id' : id , 
			'label' : label , 
			'language' : type=='default' ? taxonDefaultLanguage : taxonActiveLanguage ,
			'time' : allGetTimestamp()
		}),
		success : function (data) {
			allSetMessage(data);
		}
	})
	
}

function taxonSetRankLabels(obj,language) {

	for(var i=0;i<taxonRanks.length;i++) {
		if (language==taxonDefaultLanguage) {
			$('#default-'+taxonRanks[i]).val('');
		} else {
			$('#other-'+taxonRanks[i]).val('');
		}
	}
	
	for(var i=0;i<obj.length;i++) {
		if (language==taxonDefaultLanguage) {
			$('#default-'+obj[i].project_rank_id).val(obj[i].label);
		} else {
			$('#other-'+obj[i].project_rank_id).val(obj[i].label);
		}
	}

}

function taxonGetRankLabels(language) {

	allAjaxHandle = $.ajax({
		url : "ajax_interface.php",
		type: "POST",
		data : ({
			'action' : 'get_rank_labels' ,
			'language' : language ,
			'time' : allGetTimestamp()
		}),
		success : function (data) {
			obj = $.parseJSON(data);
			taxonSetRankLabels(obj,language);
		}
	})
	
}

$(document).ready(function(){
{/literal}
{section name=i loop=$projectRanks}
taxonAddRank({$projectRanks[i].id});
{/section}
{section name=i loop=$languages}
taxonAddLanguage([{$languages[i].language_id},'{$languages[i].language}',{if $languages[i].def_language=='1'}1{else}0{/if}]);
{/section}
taxonActiveLanguage = {if $languages[1].language_id!=''}{$languages[1].language_id}{else}false{/if};
taxonDrawRankLanguages();
taxonGetRankLabels(taxonDefaultLanguage);
taxonGetRankLabels(taxonActiveLanguage);

{literal}
});
{/literal}
</script>

</div>

{include file="../shared/admin-messages.tpl"}
{include file="../shared/admin-footer.tpl"}



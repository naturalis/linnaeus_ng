{include file="../shared/admin-header.tpl"}
{literal}
<script>

var originalSynonyms=Array();
var treatedSynonyms=Array();
var originalAuthors=Array();


function fixSplitElement(p) {
	return '<span splitpoint="'+p+'" onmouseover="highlightSplit(this,true)" onmouseout="highlightSplit(this,false)" ondblclick="splitClick(this)" style="cursor:pointer">&nbsp; &nbsp;</span>';
}

function fixSplitElements() {
	$('split').each(function(e){
		$(this).replaceWith(fixSplitElement($(this).attr('p')));
	});
}


function highlightSplit(ele,mode) {
	if (mode)
		$(ele).css('background-color','orange');
	else
		$(ele).css('background-color','transparent');
}

function splitClick(ele) {

var sp = $(ele).attr('splitpoint');
var id = $(ele).parent().attr('id').replace('s-','');
var s = originalSynonyms[id];
$('#s-'+id).html(s.substring(0, sp).trim());
$('#a-'+id).html($('#a-'+id).html()+s.substring(sp).trim());

}

function splitUndo(id) {

$('#s-'+id).html('').html(treatedSynonyms[id]);
$('#a-'+id).html('').html(originalAuthors[id]);
fixSplitElements();

}

</script>
{/literal}
<div id="page-main">

{if $synonyms|@count==0}
{t}No synonyms have been defined.{/t}
{else}
<p class="instruction-text">
Double-click on the spaces between words to split a synonym in a synonym and an author 
</p>
<table id="syn-table">
	<tr>
		<th style="width:10px" title="{t}corresponding taxon{/t}">taxon</th>
		<th style="width:450px">{t}synonym{/t}</th>
		<th style="width:250px">{t}author{/t}</th>
		<th colspan="2"></th>
	</tr>
	{foreach from=$synonyms item=v}
	<tr class="tr-highlight" id="syn-{$v.id}">
		<td><a href="synonyms.php?id={$v.taxon_id}" style="color:#777">{$v.taxon}</a></td>
		<td id="s-{$v.id}">{$v.splitter}</td>
		<td id="a-{$v.id}">{$v.author}</td>
		<td onclick="splitUndo({$v.id});" class="a">undo</td>
		<td onclick="splitSave({$v.id});" class="a">save</td>
	</tr>
	{/foreach}
</table>
{/if}
</div>


            
{literal}
<script type="text/JavaScript">
$(document).ready(function(){
{/literal}

	allLookupNavigateOverrideUrl('synonyms.php?id=%s');

	$('#syn-table').disableSelection();
	
	{foreach from=$synonyms item=v}
	originalSynonyms[{$v.id}] = '{$v.synonym|replace:"'":"\'"}';
	{if $v.author}originalAuthors[{$v.id}] = '{$v.author|replace:"'":"\'"}';
{/if}
	treatedSynonyms[{$v.id}] = '{$v.splitter|replace:"'":"\'"}';
	{/foreach}

	fixSplitElements();

{literal}
});
</script>
{/literal}


{include file="../shared/admin-messages.tpl"}
{include file="../shared/admin-footer.tpl"}
{include file="../shared/admin-header.tpl"}

<style>
a.edit {
	color:#03F;
	font-size:0.8em;
	margin-left:5px;
}
ul {
    padding:0 0 4px 13px;
}
ul[id^=level] {
	display:none;
}
ul[id=level0] {
	display:block;
}
table tr td {
	vertical-align:top;
}
td {
	width:400px;
}
td:last-child {
	border-left:1px solid #999;
	padding-left:10px;
	width:500px;
}
</style>

<script>

function addTaxonToGroup(id,label)
{
	label=label?label:$('#taxon'+id).html();
	
	if ($('#selected'+id).length==0)
	{
		$('#selection').append(
			'<li id="selected'+id+'" value="'+id+'">'+
			label+
			'<a href="#" class="edit" onclick="removeTaxonFromGroup('+id+');false;">remove</a></li>'
		);
		$('#add'+id).toggle(false);
	}
}

function removeTaxonFromGroup(id)
{
	$('#selected'+id).remove();
	$('#add'+id).toggle(true);
}

function doTaxongroupTaxaFormSubmit()
{
	$( "#selection").find("li").each(function( index ) {
		$("#theForm").append('<input type="hidden" name="taxa[]" value="'+ $( this ).attr("value") +'" />');
	});
	$("#theForm").append('<input type="hidden" name="action" value="save" />');
	$("#theForm").submit();
}

</script>

<div id="page-main">

	<h3>group: {$group.sys_label}</h3>
    
    <p>
    <form id="theForm" method="post">
    <input type="hidden" name="rnd" value="{$rnd}" />
    <input type="hidden" name="group_id" value="{$group.id}" />
    <input type="button" id="save" value="save" />
    </form>
    </p>
    
    <table><tr>
    <td>
	    Taxa in group:
	    <ul id="selection">
        </ul>
        {if $groups|@count==0}(none){/if}
	</td>
    <td>
        All taxa:
        {function menu level=0}
          <ul id="level{$level}">
          {foreach $data as $entry}
                <li>
                    {if $entry.children}<a href="#" onclick="$(this).nextUntil('ul').next().toggle();return false;">{/if}<span id="taxon{$entry.id}">{$entry.taxon} ({$entry.rank})</span>{if $entry.children}</a>{/if}
                     <a href="#" id="add{$entry.id}" class="edit" onclick="addTaxonToGroup({$entry.id});return false;">add</a>
                    {if $entry.children}{menu data=$entry.children level=$level+1}{/if}
                </li>
          {/foreach}
          </ul>
        {/function}
        
        {menu data=$taxa}
	</td>
    </tr></table>

    
    <p>
    	<a href="taxongroups.php">back</a>
    </p>

</div>


<script type="text/JavaScript">
$(document).ready(function(){
	{foreach $taxongroupTaxa as $v}
	addTaxonToGroup({$v.id},'{$v.taxon|@escape}');
	{/foreach}

	$('#save').bind('click',function() { doTaxongroupTaxaFormSubmit(); } );
});
</script>

{include file="../shared/admin-messages.tpl"}
{include file="../shared/admin-footer.tpl"}
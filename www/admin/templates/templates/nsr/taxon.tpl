{include file="../shared/admin-header.tpl"}
<script>

var taxonid=null;
var taxonrank=null;
var values=new Array();

function toggleedit(ele)
{
	var mode=$(ele).next('span').is(':visible') ? 'cancel' : 'edit';
	$(ele).html(mode=='edit' ? 'cancel' : 'edit');
	$(ele).next().toggle();

	if (mode=='cancel')
	{
		setnewvalue($(ele).attr('rel'));
	}
}

function setnewvalue(name,value)
{
	for (i in values)
	{
		if (values[i].name==name)
		{
			if (value)
			{	
				values[i].new=value;
			}
			else
			{
				delete values[i].new;
			}
		}
	}
	console.dir(values);
}

function storedata(ele)
{
	setnewvalue($(ele).attr('id'),$(ele).val());
}

function saveform()
{
	form = $("<form method=post></form>");
	form.append('<input type="hidden" name="id" value="'+taxonid+'" />');
	form.append('<input type="hidden" name="action" value="save" />');

	for (i in values)
	{
		var val=values[i];
		if (val.new && val.new!=val.current)
		{
			form.append('<input type="hidden" name="'+val.name+'[current]" value="'+val.current+'" />');
			form.append('<input type="hidden" name="'+val.name+'[new]" value="'+val.new+'" />');
		}
	}
	
	$('body').append(form);
	form.submit();
}

function setparent(ele)
{
	setnewvalue('parent_taxon_id',$(ele).attr('id'));
	$( '#parent' ).html($(ele).text());
}

function editparent(ele)
{
	var mode=$(ele).next('span').is(':visible') ? 'cancel' : 'edit';

	if (mode=='edit')
	{
		$( '#parent' ).html('');
		closedropdownlist();
		setnewvalue($(ele).attr('rel'));
	}
	else
	{
		$( '#parent' ).html('<input type="text" id="parent-list-input" value="" placeholder="type to find"/>');
		$( '#parent-list-input' ).bind('keyup', function(e) { 
			specieslookuplist({
				e:e,
				data : {
					'action' : 'species_lookup' ,
					'search' : $(this).val(),
					'get_all' : 0,
					'match_start' : 1,
					'taxa_only': 1,
					'max_results': 100,
					'formatted': 0,
					'rank_above': taxonrank
				},
				callback : buildparentlist 
			} )
		} );
		$( '#parent-list-input' ).focus();
	}
}

function closedropdownlist()
{
	$( '#dropdown-list-content' ).html('');
	$( '#dropdown-list' ).toggle(false);
}

function specieslookuplist(p) 
{
	e=p.e;
	data=p.data;
	callback=p.callback;

	if (e.keyCode == 27)
	{ 
		closedropdownlist();
		return;
	}

	if (data.search.length<3)
		return;

	data.time = allGetTimestamp();

	$.ajax({
		url : "ajax_interface.php",
		type: "POST",
		data : data,
		success : function (data)
		{
			callback($.parseJSON(data));
		}
	});

}

function buildparentlist(data)
{
	var buffer=Array();
	for(var i in data.results)
	{
		var t=data.results[i];
		if (t.label && t.id)
		{
			buffer.push('<li><a href="#" onclick="setparent(this);closedropdownlist();" id="'+t.id+'">'+t.label+'</a></li>');
		}
	}

	$('#dropdown-list-content').html('<ul>'+buffer.join('')+'</ul>');

	allStickElementUnderElement('parent','dropdown-list');

	$('#dropdown-list').toggle(true);
	
}

function editexpert(ele)
{
	var mode=$(ele).next('span').is(':visible') ? 'cancel' : 'edit';

	if (mode=='edit')
	{
		$( '#expert' ).html();
		closeexpertlist();
		setnewvalue($(ele).attr('rel'));
	}
	else
	{
		$( '#expert' ).html('<input type="text" id="dropdown-list-input" value="" placeholder="type to find"/>');
		$( '#dropdown-list-input' ).bind('keyup', function(e) { expertlookuplist(e); } );
		$( '#dropdown-list-input' ).focus();
	}
}








</script>


<style>
h2,h3,h4 {
    padding: 0;
    margin: 0;
}
h3 {
	font-style:italic;
}
h2,h4 {
    margin-top: 10px;
}
ul {
    margin-top:0;
	list-style:outside none;
	padding-left:0;
}
a.edit {
	margin-left:10px;
	font-size:9.5px;
	color:#06C;
}
.editspan {
	display:none;
	margin-left:10px;
}
select {
	width:auto;
	max-width:250px;
}
#dropdown-list {
	background-color:white;
	border:1px solid #666;
	display:none;
	width:350px;
	max-height:350px;
	overflow:hidden;
}
#dropdown-list-close {
	text-align:right;
	background-color:#eee;
	padding-right:5px;
	font-weight:bold;
	border-bottom:1px solid #ccc;
}
#dropdown-list-content {
	width:350px;
	max-height:350px;
	overflow-y:scroll;
	overflow-x:hidden;
}
</style>


<div id="page-main">

<form>
	<input type="text" id="allLookupBox" onkeyup="allLookup()" placeholder="type to find"/>
</form>

<form id="data">
<h2>{$concept.taxon}</h2>
<h3>{$names.preffered_name}</h3>

<p>
	<h4>concept</h4>
	nsr id: {$concept.nsr_id}<br />

	ouder: {$concept.parent.taxon}
	<a class="edit" href="#" onclick="toggleedit(this);editparent(this);return false;" rel="parent_taxon_id">edit</a>
	<span class="editspan" id="parent">
	</span>
	<input type="hidden" id="parent_taxon_id" value="{$concept.parent.id}" />

	<br />

	rang: 	{foreach from=$ranks item=v}{if $v.id==$concept.rank_id}{$v.rank}{/if}{/foreach} 
	<a class="edit" href="#" onclick="toggleedit(this);return false;" rel="concept_rank_id">edit</a>
	<span class="editspan">
		<select id="concept_rank_id" onchange="storedata(this);" >
		{foreach from=$ranks item=v}
			<option value="{$v.id}" {if $v.id==$concept.rank_id} selected="selected"{/if}>{$v.rank}</option>
		{/foreach}
		</select>
	</span>

</p>

<p>
	<h4>voorkomen</h4>
	status:
	{if $presence.presence_id}
		<span title="{$presence.presence_information_one_line}">{$presence.presence_index_label}. {$presence.presence_label}</span>
		<a class="edit" href="#" onclick="toggleedit(this);return false;" rel="presence_presence_id">edit</a>
		<span class="editspan">
			<select id="presence_presence_id" onchange="storedata(this);" >
			{assign var=first value=true}
			{foreach from=$statuses item=v}
				{if $v.index_label==99 && $first==true}
				<option value="" disabled="disabled">&nbsp;</option>
				{assign var=first value=false}
				{/if}
				<option value="{$v.id}" {if $v.id==$presence.presence_id} selected="selected"{/if}>{$v.index_label}. {$v.label}</option>
			{/foreach}
			</select>
		</span>
	{else}n.v.t.{/if}

<br />

	endemisch: 
	{if $presence.presence_id!=''}
		{if $presence.is_indigenous=='1'}ja{else if $presence.is_indigenous=='0'}nee{else}n.v.t.{/if}
		<a class="edit" href="#" onclick="toggleedit(this);return false;" rel="presence_is_indigenous">edit</a>
		<span class="editspan">
			<select id="presence_is_indigenous" onchange="storedata(this);" >
				<option value="1" {if $presence.is_indigenous==1} selected="selected"{/if}>ja</option>
				<option value="0" {if $presence.is_indigenous==0} selected="selected"{/if}>nee</option>
				<option value="-1" {if $presence.is_indigenous==''} selected="selected"{/if}>n.v.t.</option>
			</select>
		</span>
	
	{else}n.v.t.{/if}

<br />

	habitat: 
	{if $presence.habitat_id!=''}
		{$presence.habitat_label}
		<a class="edit" href="#" onclick="toggleedit(this);return false;" rel="presence_habitat_id">edit</a>
		<span class="editspan">
			<select id="presence_habitat_id" onchange="storedata(this);" >
			{foreach from=$habitats item=v}
				<option value="{$v.id}" {if $v.id==$presence.habitat_id} selected="selected"{/if}>{$v.label}</option>
			{/foreach}
			</select>
		</span>

	
	{else}n.v.t.{/if}

<br />

	expert:
	{if $presence.expert_id!=''}
		{$presence.expert_name}{if $presence.organisation_id} ({$presence.organisation_name}){/if}
		<a class="edit" href="#" onclick="toggleedit(this);editexpert(this);return false;" rel="presence_expert_id">edit</a>
		<span class="editspan" id="expert">
		</span>
		<input type="hidden" id="presence_expert_id" value="{$presence.expert_id.id}" />
		
	{else}n.v.t.{/if}

<br />

publicatie:
{if $presence.reference_id!=''}{$presence.reference_author}, "{$presence.reference_label}"{if $presence.reference_date} ({$presence.reference_date}){/if}{else}n.v.t.{/if}

</p>

<p>


<h4>namen</h4>
<ul>
{foreach from=$names.list item=v}
<li>
{$v.name} ({$v.language_label}) <i>{$v.nametype}</i>
</li>
{/foreach}
</ul>

{* $v.id}
{$v.uninomial}
{$v.specific_epithet}
{$v.infra_specific_epithet}
{$v.authorship}
{$v.name_author}
{$v.authorship_year}
{$v.nametype}
{$v.language_id}
{$v.language}
{$v.language_label}
{$v.sort_criterium}
{$v.nametype_label}
{$v.reference}
{$v.reference_id}
{$v.expert.id}
{$v.expert.name}
{$v.expert.name_alt}
{$v.expert.homepage}
{$v.expert.gender}
{$v.expert.is_company}
{$v.expert.employee_of_id}
{$v.organisation.id}
{$v.organisation.name}
{$v.organisation.name_alt}
{$v.organisation.homepage}
{$v.organisation.gender}
{$v.organisation.is_company}
{$v.organisation.language_id}
{$v.organisation.language *}

</p>

<input type="button" value="save" onclick="saveform();" />
</form>

<p>
	<a href="taxon.php?id={$concept.id}">main page</a>
</p>

</div>

<div id="dropdown-list">
	<div id="dropdown-list-close"><a href="#" onclick="closedropdownlist();return false;" />X</a></div>
	<div id="dropdown-list-content"></div>
</div>

{*
	var previousValues=Array();
	{foreach from=$data item=v key=k}
	previousValues.push( { name: '{$k}', value: {$v.current} } );
	{/foreach}
*}



<script>
$(document).ready(function()
{
	allLookupNavigateOverrideUrl('taxon.php?id=%s');
	taxonid={$concept.id};
	taxonrank={$concept.base_rank};
	$('#data :input[type!=button]').each(function(key,value) {
		values.push( { name:$(this).attr('id'),current:$(this).val() } );
	});
	//console.dir(values);
});
</script>

{include file="../shared/admin-messages.tpl"}
{include file="../shared/admin-footer.tpl"}

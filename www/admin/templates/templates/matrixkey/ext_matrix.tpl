{include file="../shared/admin-header.tpl"}

<script>

var currentId=null;
var parentLabel=null;
var parentId=null;
var taxa=[];
var projectLanguages=[];

function dialogAddGroup( data )
{
	var id=+ new Date();
	var buffer=[];
	buffer.push(
		fetchTemplate( 'labelInputTpl' )
			.replace( /%LABEL%/g, _('internal') )
			.replace( '%NAME%', 'sys_name' )
			.replace( '%VALUE%', data ? data.sys_name : "" )
			.replace( '%CLASS%', "" )
			.replace( '%ID%', "lead-" + id )
	);
	for(var i=0;i<projectLanguages.length;i++)
	{
		var value="";
		if (data && data.labels)
		{
			for(var j=0;j<data.labels.length;j++)
			{
				if (data.labels[j].language_id==projectLanguages[i].id)
				{
					value=data.labels[j].label;
				}
			}
		}

		buffer.push(
			fetchTemplate( 'labelInputTpl' )
				.replace( /%LABEL%/g, projectLanguages[i].label )
				.replace( '%NAME%', projectLanguages[i].id )
				.replace( '%VALUE%', value )
				.replace( '%CLASS%', "follow-" + id  )
				.replace( '%ID%', "" )
		);
	}

	if (data)
	{
		var cCount=data.characters ? data.characters.length : 0;
		buffer.push(
			fetchTemplate( 'groupCharacterCountTpl' )
				.replace( '%COUNT%', cCount )
		);
	}

	var content=
		fetchTemplate( 'editGroupTpl' )
			.replace( '%FORM%', buffer.join("\n").replace(/%SAVE_GROUP%/g,id))
			.replace('%HEADER%',data ? fetchTemplate( 'editGroupHeaderTpl' ) :  fetchTemplate( 'newGroupHeaderTpl' ) );

	var extraData={ savegroup: id };
	if (data)
	{
		extraData.id = data.id;
	}

	var msg = _("Are you sure?" + (cCount>0 ? "\n" + _("(grouped characters will not be deleted)") : "" ));

	saveDialog({
		title: data ? sprintf(_('Edit group: %s'),data.sys_name) : _('Add group'), 
		content: content, 
		callback: saveGroup, 
		data: extraData,
		buttons: data ?
			{ Delete : function()
				{
					if( confirm(msg) )
					{
						deleteGroup(data.id);
					}
					$( this ).dialog( "close" ); } 
			} : null
	});

	$('#lead-' + id).on('blur',function()
	{
		$('.follow-' + id).each(function()
		{
			$(this).val( $(this).val().length==0 ? $('#lead-' + id).val() : $(this).val() );
		})
	})
}

function dialogEditGroup()
{
	if (currentId==null) return;

	$.ajax({
		url : 'ext_ajax_interface.php',
		data : ({
			'action' : 'get_group',
			'id' : currentId ,
			'time' : allGetTimestamp()
		}),
		success : function( data )
		{
			data=$.parseJSON( data );
			dialogAddGroup( data );
		}
	})
}

function dialogAddCharacter()
{
	saveDialog( { title : 'add character to "' + parentLabel + '"', callback:saveCharacter} );
}

function dialogAddState()
{
	saveDialog( { title : 'add state to "' + parentLabel + '"', callback:saveState} );
}

function dialogTaxaForState()
{
	taxa=[];
	getTaxaForState();
}

function saveDialog( p )
{

	var buttons = {
			Save: function() {
				if (p.callback) {
					if (p.callback(p.data ? p.data : p.data)) {
						$( this ).dialog( "close" );
					}
				}
				else {
					$( this ).dialog( "close" );
				}
			},
			Cancel: function() {
				$( this ).dialog( "close" );
			}
		};

	if ( p.buttons )
	{
		$.extend( buttons, p.buttons);
	}

	$( "#dialog-confirm" ).dialog({
		title: p.title,
		resizable: false,
		height: "auto",
		width: 400,
		modal: true,
		position: { my: "middle", at: "middle", of: $('#character-tree') },
		buttons: buttons
	}).find(".content").html( p.content );
}

function saveCharacter()
{
	parentLabel=null;
	parentId=null;
	return false;
}

function saveState()
{
	parentLabel=null;
	parentId=null;
	return false;
}

function saveTaxa()
{
	parentLabel=null;
	parentId=null;
	return false;
}

function getTaxaForState()
{
	$.ajax({
		url : 'ext_ajax_interface.php',
		data : ({
			'action' : 'get_taxa_for_state',
			'id' : parentId ,
			'time' : allGetTimestamp()
		}),
		success : function( data )
		{
			taxa=$.parseJSON( data );
			buffer=[];
			for(var i=0;i<taxa.length;i++)
			{
				buffer.push(fetchTemplate( 'taxonListItemTpl' ).replace( '%NAME%', taxa[i].name ));
			}
			content=fetchTemplate( 'taxonListTpl' ).replace( '%ITEMS%', buffer.join("\n") );

			saveDialog( { title : 'taxa"' + parentLabel + '"' + parentId, callback:saveTaxa, content: content } );
		}
	})
}


var initialSortOrder="";
var orderChanged=false;

function setInitialSortOrderHash()
{
	initialSortOrder=calculateSortOrderHash();
}

function checkOrderChanged()
{
	orderChanged=(initialSortOrder!=calculateSortOrderHash());
}

function calculateSortOrderHash()
{
	var x=[];
	$('#character-tree li').each(function()
	{
		x.push($(this).attr('data-id'));
	})
	x=x.filter(function(a) { return a!=null; } );
	return JSON.stringify(x);
}

function shouldWarnUnsavedValues()
{
	return orderChanged;
}

function makeForm( p )
{
	var form=$('<form action="" method="post"></form>').appendTo('body');	
	if ( p.vars && p.vars.action )
	{
		form.append('<input type="hidden" name="action" value="'+p.vars.action+'">');
	}
	return form;
}

function submitForm( form )
{
	$( window ).off( 'beforeunload')
	form.submit();
}

function saveSortOrder()
{
	if ( !orderChanged )
	{
		$('#saveMessage').html('nothing to save').fadeOut(3000, function(){ $('#saveMessage').html('').show(); });
		return;
	}

	var form=makeForm( { vars: { action: 'saveSortOrder' } } );

	$('#character-tree li').each(function()
	{
		var type="";
		if ($(this).hasClass('group'))
		{
			type='group';
		}
		else
		if ($(this).hasClass('character'))
		{
			type='character';
		}
		else
		if ($(this).hasClass('state'))
		{
			type='state';
		}

		form.append("<input type='hidden' name='newOrder[]' value='"+JSON.stringify({ type: type, id: $(this).attr('data-id') })+"''>");
	})

	submitForm( form );
}

function saveGroup( data )
{
	var errors=[];
	var form=makeForm( { vars: { action: 'saveGroup' } } );
	var newSysName="";

	if ( data.savegroup )
	{
		$('[data-savegroup='+data.savegroup+']').each(function()
		{
			if ($(this).val().length==0)
			{
				errors.push(sprintf(_("Missing value: %s"),$(this).attr("placeholder")));
			}
			else
			{
				form.append("<input type='hidden' name='newNames[]' value='"+JSON.stringify({ id:$(this).attr("name"),value:$(this).val()})+"''>");
				if ( $(this).attr("name")=="sys_name")
				{
					newSysName=$(this).val();
				}
			}
		})
	}

	if (newSysName.length>0)
	{
		$('li.group').each(function()
		{
			if ($(this).attr("data-sys_name")==newSysName && (!data || (data && data.id != $(this).attr("data-id"))))
			{
				errors.push( sprintf( _("Group name already exists: %s"), newSysName ));
			}
		});
	}

	if (errors.length!=0)
	{
		errors.unshift( _("Errors occurred!") );
		alert( errors.join("\n") );
		return false;
	}

	if (data && data.id) form.append("<input type='hidden' name='groupId' value='"+data.id+"''>");

	submitForm( form );
}

function deleteGroup( id )
{
	var errors=[];
	var form=makeForm( { vars: { action: 'deleteGroup' } } );
	form.append("<input type='hidden' name='groupId' value='"+id+"''>");
	submitForm( form );
}

</script>



<style>

.grip {
	cursor:move;
	margin-right:10px;
	display: inline-block;
}

.controls {
	display: inline-block;
}

.symbol, .label {
	cursor: pointer;
}

.symbol.grip {
	cursor: move;
}

.symbol.grip::after {
	content: '\25A8';
}

.symbol.add::after {
	content: '\FF0B';
}

.symbol.edit::after {
	content: '\2756';
}

.symbol.taxa::after {
	content: '\4DC0';
}

.controls.master {
	border-bottom: 1px solid #eee;
	width: 500px;
	padding-bottom:10px;
}


li.group, li.character, li.state {
	padding:2px;
	margin:10px 0 10px 0;
}

li.group {
	color:black;
	border:1px dotted #ccf;
	background-color: #fdfdff;
	width: 450px;
	padding-right:15px;
}

li.character {
	color:green;
	border:1px dotted #ccf;
	background-color: #fdfffd;
	max-width: 450px;
	padding-right:15px;
}

li.state {
	color:red;
	border:1px dotted #ccf;
	background-color: #fffdfd;
	max-width: 450px;
}

.character-tree ul {
    list-style-type: none;
    margin-left: 20px;
    padding: 0;
}

.character-tree ul li {
    margin: 3px 0 3px 0;
}

.character-tree li.group {
}

.character-tree li.character {
}

.character-tree li.state {
}

form {
	line-height: 30px;
}

input[type=button] {
	height:25px;
}



</style>


{function printCharacter}
<li class="character" data-id="{$data.id}">
	<div class="symbol grip"></div>
	<span class="label">{if $data.default_label}{$data.default_label}{else}{$data.sys_name}{/if}</span>
	<div class="controls" data-group-id="states-{$data.id}" data-id="{$data.id}">
		<span class="symbol edit character"></span>
		<span class="symbol add state"></span>
	</div>
	<ul class="sortable states-{$data.id}" style="display:none">
		{foreach $data.states s}
		<li class="state" data-id="{$s.id}">
			<div class="symbol grip"></div>
			<span class="label">{if $s.default_label}{$s.default_label}{else}{$s.sys_name}{/if}</span>
			<span class="symbol edit state"></span>
			<span class="symbol taxa state"></span>
		</li>
		{/foreach}
	</ul>
</li>
{/function}

{function printGroup}
<li class="group" data-id="{$data.id}" data-sys_name="{$data.sys_name}">
	<div class="symbol grip"></div>
	<span class="label">{if $data.default_label}{$data.default_label}{else}{$data.sys_name}{/if}</span>
	<div class="controls" data-group-id="characters-{$data.id}" data-id="{$data.id}">
		<span class="symbol edit group"></span>
		<span class="symbol add character"></span>
	</div>
	<ul class="sortable characters-{$data.id}" style="display:none">
		{foreach $data.characters c}
		{printCharacter data=$c dataid=$data.id}
		{/foreach}
	</ul>
</li>
{/function}

<div id="page-main">

	<h2>{if $matrix.default_label}{$matrix.default_label}{else}{$matrix.sys_name}{/if}</h2>

	<div class="controls master">
		<span class="symbol up display-toggle master" style="display:none">&#9650; hide all child nodes</span>
		<span class="symbol down display-toggle master">&#9660; show all child nodes</span>
	</div>

	<div id="character-tree" class="character-tree">

		<ul class="sortable" style="margin-left:0;">
		{foreach $groupsCharactersStates v}
			{if $v.ref_type=='char'}
				{printCharacter data=$v.item}
			{else}
				{printGroup data=$v.item}
			{/if}
		{/foreach}
		</ul>

	</div>

	<p>
		<input type="button" value="{t}save sort order{/t}" onclick="saveSortOrder();" />
		<span id="saveMessage"></span>
	</p>

	<div class="controls">
		<span class="symbol group add"> add a group</span><br />
		<span class="symbol character add"> add a character</span><br />
	</div>

</div>

<script>
$(document).ready(function()
{
	$('span.label').on('click',function()
	{
		$('.'+$(this).next('div').attr('data-group-id')).toggle();
	});

	$('.controls span.display-toggle.up.master').on('click',function()
	{
		//$('span.label').trigger('click');
		$('span.label').each(function()
		{
			$('.'+$(this).next('div').attr('data-group-id')).toggle(false);
		});

		$('.controls span.display-toggle.master').toggle();
	});

	$('.controls span.display-toggle.down.master').on('click',function()
	{
		//$('span.label').trigger('click');
		$('span.label').each(function()
		{
			$('.'+$(this).next('div').attr('data-group-id')).toggle(true);
		});

		$('.controls span.display-toggle.master').toggle();
	});

	$('.controls > .group.add').on('click',function()
	{
		dialogAddGroup();
	})

	$('.edit.group').on('click',function()
	{
		currentId=$(this).parent().attr('data-id');
		dialogEditGroup();
	})

	$('.add.character').on('click',function()
	{
		parentLabel=$(this).parent().prev(".label").html();
		parentId=$(this).parent().attr('data-id');
		dialogAddCharacter();
	})

	$('.edit.character').on('click',function()
	{
		dialogEditCharacter();
	})

	$('.add.state').on('click',function()
	{
		parentLabel=$(this).parent().prev(".label").html();
		parentId=$(this).parent().attr('data-id');
		dialogAddState();
	})

	$('.edit.state').on('click',function()
	{
		parentLabel=$(this).parent().prev(".label").html();
		parentId=$(this).parent().attr('data-id');
		dialogEditState();
	})

	$('.taxa.state').on('click',function()
	{
		parentLabel=$(this).prev(".label").html();
		parentId=$(this).parent().attr('data-id');
		dialogTaxaForState();
	})






	$('.controls span.display-toggle.up').attr('title','hide nodes');
	$('.controls span.display-toggle.down').attr('title','show nodes');

	$('.symbol').each(function()
	{
		if ($(this).hasClass('edit'))
		{
			$(this).attr('title','edit')
		}
		if ($(this).hasClass('add'))
		{
			$(this).attr('title','add')
		}
	})

    $( ".sortable" ).sortable(
    {
		 handle: ".grip",
		 stop: function( event, ui )
		 {
		 	checkOrderChanged();
		 }
	});
    
    $( ".sortable" ).disableSelection();

	$(window).bind('beforeunload', function()
	{
		if (shouldWarnUnsavedValues()) return true;
	});

	{foreach $languages v}
	projectLanguages.push( { id: {$v.language_id}, label: '{$v.language}', default: {$v.def_language==1}} )
	{/foreach}

    setInitialSortOrderHash();

});
</script>

<div id="dialog-confirm" title="action" style="display: none;">
	<p class="content"></p>
</div>

<div id="taxonListTpl" class="inline-templates">
<!--
	<ul>%ITEMS%</ul>
-->
</div>

<div id="taxonListItemTpl" class="inline-templates">
<!--
	<li>%NAME%</li>
-->
</div>

<div id="editGroupTpl" class="inline-templates">
<!--
	<form>
	%HEADER%
	<table>
	%FORM%
	</table>
	</form>
-->
</div>

<div id="newGroupHeaderTpl" class="inline-templates">
<!--
	{t}Enter the new group's names:{/t}
-->
</div>

<div id="editGroupHeaderTpl" class="inline-templates">
<!--
	{t}Edit group names:{/t}
-->
</div>

<div id="labelInputTpl" class="inline-templates">
<!--
	<tr><td>%LABEL%:</td><td><input type="text" name="%NAME%" placeholder="%LABEL% {t}name{/t}" id="%ID%" value="%VALUE%" class="%CLASS%" data-savegroup="%SAVE_GROUP%" /></td></tr>
-->
</div>


<div id="groupCharacterCountTpl" class="inline-templates">
<!--
	<tr><td>{t}Grouped characters:{/t}</td><td>%COUNT%</td></tr>
-->
</div>



{include file="../shared/admin-messages.tpl"}
{include file="../shared/admin-footer.tpl"}

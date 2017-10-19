{include file="../shared/admin-header.tpl"}

<script>

var parentLabel=null;
var parentId=null;
var taxa=[];

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
	$( "#dialog-confirm" ).dialog({
		title: p.title,
		resizable: false,
		height: "auto",
		width: 400,
		modal: true,
		position: { my: "middle", at: "middle", of: $('#character-tree') },
		buttons: {
			"Save": function() {
				if (p.callback) {
					if (p.callback()) {
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
		}
	}).find(".content").html( p.content );
}

function saveCharacter()
{
	return false;
	parentLabel=null;
	parentId=null;
}

function saveState()
{
	return false;
	parentLabel=null;
	parentId=null;
}

function saveTaxa()
{
	return false;
	parentLabel=null;
	parentId=null;
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


function saveSortOrder()
{
	if (! orderChanged)
	{
		$('#saveMessage').html('nothing to save').fadeOut(3000, function(){ $('#saveMessage').html('').show(); });
		return;
	}

	var form=$('<form action="" method="post"></form>').appendTo('body');	
	form.append('<input type="hidden" name="action" value="saveSortOrder">');

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

	$( window ).off( 'beforeunload')
	form.submit();	
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
			<span class="symbol taxa"></span>
		</li>
		{/foreach}
	</ul>
</li>
{/function}

{function printGroup}
<li class="group" data-id="{$data.id}">
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
		<span class="symbol add"> add a group</span><br />
		<span class="symbol add"> add a character</span><br />
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

	$('.controls .add.character').on('click',function()
	{
		parentLabel=$(this).parent().prev(".label").html();
		parentId=$(this).parent().attr('data-id');
		dialogAddCharacter();
	})

	$('.controls .add.state').on('click',function()
	{
		parentLabel=$(this).parent().prev(".label").html();
		parentId=$(this).parent().attr('data-id');
		dialogAddState();
	})

	$('.controls .edit.character').on('click',function()
	{
		parentLabel=$(this).parent().prev(".label").html();
		parentId=$(this).parent().attr('data-id');
		dialogAddCharacter();
	})

	$('.controls .edit.state').on('click',function()
	{
		parentLabel=$(this).parent().prev(".label").html();
		parentId=$(this).parent().attr('data-id');
		dialogAddState();
	})

	$('.symbol.taxa').on('click',function()
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

    $( ".sortable" ).sortable({
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

    acquireInlineTemplates();
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

{include file="../shared/admin-messages.tpl"}
{include file="../shared/admin-footer.tpl"}

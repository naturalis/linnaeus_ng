{include file="../shared/admin-header.tpl"}

<style>
tr {
	vertical-align:top;
}
th {
	text-align:right;
	font-weight:normal;
}
input[type=text].normal {
	width:200px;
}
input[type=text].small {
	width:50px;
}
</style>
<script>

var name0Focused=false;

function duplicateSysLabel()
{
	if ($('#sysname').val().length>0 && !name0Focused)
	{
		$('#name').val($('#sysname').val());
	}
}

function setName0Focused()
{
	name0Focused=true;
}

function checkAndSaveForm()
{
	var buffer=Array();

	$(':input').each(function()
	{
		if ($(this).attr('mandatory')=='mandatory' && $(this).val().length<1)
		{
			var id=$(this).attr('id');
			var label=$("label[for='"+id+"']").html();
			label=label.length<1 ? id : label;
			buffer.push(label);
		}
	});
	
	if (buffer.length>0)
	{
		alert("Values are missing for the following mandatory field(s):\n"+buffer.join("\n"));
		return false;
	}
	else
	{
		if ($('#project_type_id option:selected').attr('sysname').indexOf('date')!==0)
		{
			$('#date_format_id').remove();
		}
		$('#theForm').submit();
	}
		
}

</script>


<div id="page-main">

	<p>

    <h3>{t _s1=$group.name}%s: new trait{/t}</h3>

    <form id="theForm" method="post">
    <input name="trait_group_id" type="hidden" value="{$group.id}">
    <input type="hidden" name="action" value="save">

    <table>
        <tr>
            <th>{t}data type:{/t}</th>
            <td>
            <select 
                name="project_type_id" 
                id="project_type_id" 
                mandatory="mandatory"
                onchange="
                    $('#project_type_id_description').html($('#project_type_id option:selected').attr('desc'));
                    $('.date-format').toggle($('#project_type_id option:selected').attr('sysname').indexOf('date')===0);
                    $('.allow-select-multiple').toggle($('#project_type_id option:selected').attr('allow_select_multiple')==1);
                    ">
            	{foreach $datatypes as $datatype}
                {if $datatype.project_type_id}
                <option
                	value="{$datatype.project_type_id}" 
                    desc="{$datatype.description|@escape}"
                    allow_select_multiple="{$datatype.allow_select_multiple}" 
                    sysname="{$datatype.sysname}">{$datatype.name}</option>
				{/if}
            	{/foreach}
            </select>
            <span id="project_type_id_description"></span>
            </td>
        </tr>
        <tr class="date-format">
            <th>{t}date format:{/t}</th>
            <td>
                <select 
                    name="date_format_id" 
                    id="date_format_id" 
                    onchange="$('#project_type_id_description').html($('#project_type_id option:selected').attr('desc'));">
                    {foreach $dateformats as $dateformat}
                    <option value="{$dateformat.id}">{$dateformat.sysname} ({$dateformat.format})</option>
                    {/foreach}
                </select>
            </td>
        </tr>
       
		{function text}
		<tr>
        	<th><label for="{$data.name}">{t}{$data.label}{/t}</label>:</th>
            <td>
            	{if $data.class=='textarea'}
                <textarea value="" name="{$data.name}" id="{$data.name}"{if $data.mandatory} mandatory="mandatory"{/if} ></textarea>
                {else}
	            <input class="{$data.class}" type="text" value="" name="{$data.name}" id="{$data.name}"{if $data.mandatory} mandatory="mandatory"{/if} />
                {/if}
                {if $data.mandatory} *{/if}</td>
		</tr>
        {/function}

		{$array = [
            ['label'=>'system name','name'=>'sysname','mandatory'=>true,'class'=>'normal'],
            ['label'=>'name','name'=>'name','mandatory'=>true,'class'=>'normal'],
            ['label'=>'code','name'=>'code','class'=>'small'],
            ['label'=>'description','name'=>'description','class'=>'textarea'],
            ['label'=>'unit','name'=>'unit','class'=>'small']
		]}
        
        {foreach $array as $v}
        {text data=$v}
        {/foreach}

		{function boolean}
        <tr{if $data.row_class} class="{$data.row_class}"{/if}>
        	<th>{$data.label}:</th>
            <td>
            	<label><input type="radio" value="y" name="{$data.name}" />{t}yes{/t}</label>
                <label><input type="radio" value="n" name="{$data.name}" checked="checked" />{t}no{/t}</label>
			</td>
		</tr>
        {/function}
        
		{$array = [
            ['label'=>'can be null','name'=>'can_be_null'],
            ['label'=>'can select multiple','name'=>'can_select_multiple','row_class'=>'allow-select-multiple'],
            ['label'=>'can include comment','name'=>'can_include_comment'],
            ['label'=>'show index numbers','name'=>'show_index_numbers']
		]}
        
        {foreach $array as $v}
        {boolean data=$v}
        {/foreach}
    </table>
    
    </form>
    </p>
    <p>
    	<input type="button" value="save" onclick="checkAndSaveForm();" />
    </p>
    <p>
    	<a href="traitgroup_traits.php?group={$group.id}">back</a><br />
    	<a href="index.php">index</a><br />
    </p>
</div>


<script type="text/JavaScript">
$(document).ready(function()
{
	$('#sysname').bind('keyup',function() { duplicateSysLabel(); } );
	$('#name').bind('focus',function() { setName0Focused(); } );
	$('#page-block-messages').fadeOut(2000);
	$('#project_type_id').trigger('change'); 

});
</script>

{include file="../shared/admin-messages.tpl"}
{include file="../shared/admin-footer.tpl"}
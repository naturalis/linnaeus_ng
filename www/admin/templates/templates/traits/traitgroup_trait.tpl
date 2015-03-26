{include file="../shared/admin-header.tpl"}

<div id="page-main">

	<p>
        <h3>
        {if $trait}
        {t _s1=$group.name _s2=$trait.sysname}%s: %s{/t}
        {else}
        {t _s1=$group.name}%s: new trait{/t}
        {/if}
    </h3>

    <form id="theForm" method="post">
    <input name="trait_group_id" type="hidden" value="{$group.id}">
    <input type="hidden" name="rnd" value="{$rnd}" />
    {if $trait}
    <input name="id" type="hidden" value="{$trait.id}">
    {/if}
    <input type="hidden" name="action" id="action" value="save">

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
                    $('.allow-values').toggle($('#project_type_id option:selected').attr('allow_values')==1);
                    $('.allow-max-length').toggle($('#project_type_id option:selected').attr('allow_max_length')==1);
                    $('.allow-unit').toggle($('#project_type_id option:selected').attr('allow_unit')==1);
                    $('.allow-ranges').toggle($('#project_type_id option:selected').attr('allow_ranges')==1);
                    ">
            	{foreach $datatypes as $datatype}
                {if $datatype.project_type_id}
                <option
                	value="{$datatype.project_type_id}" 
                    desc="{$datatype.description|@escape}"
                    allow_select_multiple="{$datatype.allow_select_multiple}" 
                    allow_values="{$datatype.allow_values}" 
                    allow_max_length="{$datatype.allow_max_length}" 
                    allow_unit="{$datatype.allow_unit}" 
                    allow_ranges="{$datatype.allow_ranges}" 
                    sysname="{$datatype.sysname}"
                    {if $trait.project_type_id==$datatype.project_type_id}selected="selected"{/if}>
                    	{$datatype.name}
                    </option>
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
                    <option value="{$dateformat.id}" {if $trait.date_format_id==$dateformat.id}selected="selected"{/if}>
                    	{$dateformat.sysname} ({$dateformat.format_hr})
					</option>
                    {/foreach}
                </select>
            </td>
        </tr>

       
		{function text}
		<tr{if $data.row_class} class="{$data.row_class}"{/if}>
        	<th><label for="{$data.name}">{t}{$data.label}{/t}</label>:</th>
            <td>
            	{if $data.class=='textarea'}
                <textarea name="{$data.name}" id="{$data.name}"{if $data.mandatory} mandatory="mandatory"{/if} >{$data.value}</textarea>
                {else}
	            <input class="{$data.class}" type="text" value="{$data.value}" name="{$data.name}" id="{$data.name}"{if $data.mandatory} mandatory="mandatory"{/if} />
                {/if}
                {if $data.mandatory} *{/if}</td>
		</tr>
        {/function}

		{function text_language}
		{foreach from=$languages item=v}
		<tr{if $data.row_class} class="{$data.row_class}"{/if}>
        	<th class="language-labels">
            	<label for="{$data.name}">{$v.language} {t}{$data.label}{/t}</label>:
			</th>
            <td class="language-labels">
            	{if $data.class=='textarea'}
                <textarea
                	name="{$data.name}[{$v.language_id}]" 
                    id="{$data.name}-{$v.language_id}"
                    {if $data.mandatory} mandatory="mandatory"{/if} 
				>{$trait.language_labels[$data.name][$v.language_id]}</textarea>
                {else}
	            <input
                	class="{$data.class}" 
                    type="text" 
                    value="{$trait.language_labels[$data.name][$v.language_id]|@escape}" 
                    name="{$data.name}[{$v.language_id}]" 
                    id="{$data.name}-{$v.language_id}"
                    {if $data.mandatory} mandatory="mandatory"{/if}
				/>
                {/if}
                {if $data.mandatory} *{/if}</td>
		</tr>
		{/foreach}        
        {/function}
        
        
		{$array = [
            ['label'=>'system name','name'=>'sysname','mandatory'=>true,'class'=>'normal','value'=>$trait.sysname]
		]}
       
        {foreach $array as $v}
        {text data=$v}
        {/foreach}

		{$array = [
            ['label'=>'name','name'=>'name','mandatory'=>true,'class'=>'normal','value'=>$trait.name],
            ['label'=>'code','name'=>'code','class'=>'small','value'=>$trait.code],
            ['label'=>'description','name'=>'description','class'=>'textarea','value'=>$trait.description]
		]}
       
        {foreach $array as $v}
        {text_language data=$v}
        {/foreach}

		{$array = [
            ['label'=>'max. length','name'=>'max_length','class'=>'small','value'=>$trait.max_length,'row_class'=>'allow-max-length'],
            ['label'=>'unit','name'=>'unit','class'=>'small','value'=>$trait.unit,'row_class'=>'allow-unit']
		]}
       
        {foreach $array as $v}
        {text data=$v}
        {/foreach}

		{function boolean}
        <tr{if $data.row_class} class="{$data.row_class}"{/if}>
        	<th>{$data.label}:</th>
            <td>
            	<label><input type="radio" value="y" name="{$data.name}" {if $data.value==1}checked="checked"{/if} />{t}yes{/t}</label>
                <label><input type="radio" value="n" name="{$data.name}" {if $data.value!=1}checked="checked"{/if} />{t}no{/t}</label>
			</td>
		</tr>
        {/function}
        
		{$array = [
            ['label'=>'can be null','name'=>'can_be_null','value'=>$trait.can_be_null],
            ['label'=>'can select multiple','name'=>'can_select_multiple','row_class'=>'allow-select-multiple','value'=>$trait.can_select_multiple],
            ['label'=>'can have range','name'=>'can_have_range','value'=>$trait.can_have_range,'row_class'=>'allow-ranges'],
            ['label'=>'can include comment','name'=>'can_include_comment','value'=>$trait.can_include_comment]
		]}
            {*['label'=>'show index numbers','name'=>'show_index_numbers','value'=>$trait.show_index_numbers]*}
        
        {foreach $array as $v}
        	{boolean data=$v}
        {/foreach}
    </table>
    
    </form>
    </p>
    <p>
    	<input type="button" value="save" onclick="checkAndSaveForm();" />
	{if $trait}&nbsp;<input type="button" value="delete" onclick="deleteItem();" />{/if}

    </p>
    <p>
    	<a href="traitgroup_trait_values.php?trait={$trait.id}">values</a>&nbsp;&nbsp;
    	<a href="traitgroup_traits.php?group={$group.id}">back</a>&nbsp;&nbsp;
    	<a href="index.php">index</a><br />
    </p>
    
    maybe add max. items as well?
    
	explanation: max length is max of prefined values OR the user-specified value when free value

</div>



<script type="text/JavaScript">
$(document).ready(function()
{
//	{if !$trait} $('#sysname').bind('keyup',function() { duplicateSysLabel(); } );{/if}
	$('#name').bind('focus',function() { setName0Focused(); } );
	$('#page-block-messages').fadeOut(2000);
	$('#project_type_id').trigger('change'); 

});
</script>

{include file="../shared/admin-messages.tpl"}
{include file="../shared/admin-footer.tpl"}
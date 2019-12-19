{include file="../shared/admin-header.tpl"}

<style>
th {
	text-align:right;
}
tr {
	vertical-align:top;
}
input, textarea {
	font-family:inherit;
	font-size:12px;
}
textarea {
	width:300px;
	height:100px;
}

</style>

<script>

var name0Focused=false;

function duplicateSysLabel()
{
	if ($('#sys_label').val().length>0 && !name0Focused)
	{
		$('#names-0').val($('#sys_label').val());
	}
}

function setName0Focused()
{
	name0Focused=true;
}

function checkFormMandatory(form)
{
	var messages=[];
	for(var i=0;i<form.elements.length;i++)
	{
		var ele=$(form.elements[i]);
		if (ele.attr('mandatory')=='mandatory' && ele.val().length==0)
		{
			var label=$("label[for='"+ele.attr('id')+"']").html();
			messages.push(label);
		}
	}
	
	if (messages.length>0)
	{
		alert(_("Please fill out all required fields:\n")+messages.join("\n"))
	}
	
	return messages.length==0;
}

function deleteGroup()
{
	if (confirm(_('Are you sure?')))
	{
		$('#action').val('delete');
		$('#theForm').submit();
	}
}


</script>

<div id="page-main">
	<h3>{if $newgroup}new group{else}edit group {$group.sys_label}{/if}</h3>
    
    <form id="theForm" method="post">
    <input type="hidden" name="rnd" value="{$rnd}" />
    <input type="hidden" name="action" id="action" value="save" />
    <input type="hidden" name="id" id="id" value="{$group.id}" />

    <table>
        <tr>
        	<th><label for="sys_label">system name</label>:</th>
        	<td><input type="text" maxlength="64" name="sys_label" id="sys_label" placeholder="max. 64 characters" value="{$group.sys_label}" mandatory="mandatory"> *</td>
        </tr>

        <tr>
        	<th>parent group:</th>
        	<td>

            {function groupselect level=0}
              {foreach $data as $entry}
                    <option 
                        value="{$entry.id}"
                        {if $group.parent_id==$entry.id} selected="selected"{/if}
                        {if $group.id==$entry.id} disabled="disabled"{/if}
                        >
                        {'&nbsp;&nbsp;'|@str_repeat:$level}
                        {$entry.sys_label}
                        </option>
                    {if $entry.children}{groupselect data=$entry.children level=$level+1}{/if}
                {/foreach}
            {/function}
            
            <select name="parent_id">
            <option value=""{if !$group || $group.parent_id==''} selected="selected"{/if}>-</option>
            {groupselect data=$groups}
            </select>
    
            </td>
        </tr>

    
        {foreach from=$languages item=v key=k}
        <tr>
            <td></td>
        	<td style="padding-top:10px;"><i>{$v.language}</i></td>
		</tr>
        <tr>
        	<th>name:</th>
            <td><input id="names-{$k}" name="names[{$v.language_id}]" type="text" value="{$group.names[{$v.language_id}]}" placeholder="max. 64 characters"></td>
		</tr>
        <tr>
        	<th>description:</th>
            <td>
            <textarea id="descriptions-{$k}" name="descriptions[{$v.language_id}]" maxlength="255" placeholder="max. 255 characters">{$group.descriptions[{$v.language_id}]}</textarea>
			</td>
		</tr>
        {/foreach}
	</table>
    
    <p>
    <input type="submit" value="save" />
	{if !$newgroup}&nbsp;<input type="button" value="delete" onclick="deleteGroup();" />{/if}
    </p>

    
    </form>

    <p>
    	<a href="taxongroups.php">back</a>
    </p>

</div>


<script type="text/JavaScript">
$(document).ready(function(){
	
	{if $newgroup}$('#sys_label').bind('keyup',function() { duplicateSysLabel(); } );{/if}
	$('#names-0').bind('focus',function() { setName0Focused(); } );
	$('#theForm').bind('submit',function() { return checkFormMandatory(this); } );

});
</script>

{include file="../shared/admin-messages.tpl"}
{include file="../shared/admin-footer.tpl"}
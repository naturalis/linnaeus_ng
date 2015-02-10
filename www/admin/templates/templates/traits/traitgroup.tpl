{include file="../shared/admin-header.tpl"}

<div id="page-main">
	<h3>{if $newgroup}new group{else}edit group {$group.sysname}{/if}</h3>
    
    <form id="theForm" method="post">
    <input type="hidden" name="rnd" value="{$rnd}" />
    <input type="hidden" name="action" id="action" value="save" />
    <input type="hidden" name="id" id="id" value="{$group.id}" />

    <table>
        <tr>
        	<th><label for="sysname">system name</label>:</th>
        	<td><input type="text" maxlength="64" name="sysname" id="sysname" placeholder="max. 64 characters" value="{$group.sysname}" mandatory="mandatory"> *</td>
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
                        {$entry.sysname}
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
	{if !$newgroup}&nbsp;<input type="button" value="delete" onclick="deleteItem();" />{/if}
    </p>

    
    </form>

    <p>
    	<a href="traitgroups.php">back</a><br />
    	<a href="index.php">index</a>
    </p>

</div>


<script type="text/JavaScript">
$(document).ready(function()
{
	//{if $newgroup}$('#sysname').bind('keyup',function() { duplicateSysLabel(); } );{/if}
	$('#names-0').bind('focus',function() { setName0Focused(); } );
	$('#theForm').bind('submit',function() { return checkAndSaveForm(this); } );

});
</script>

{include file="../shared/admin-messages.tpl"}
{include file="../shared/admin-footer.tpl"}
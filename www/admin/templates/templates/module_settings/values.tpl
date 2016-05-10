{include file="../shared/admin-header.tpl"}

<style>
table tr {
	vertical-align:top;
}
table tr.second-line td {
	border-bottom:1px dotted #bbb;
}
table th {
	background-color:#eee;
}
table tr td.setting-name, table tr th.setting-name {
	text-align:right;
	font-weight:bold;
}
table tr td.setting-name  {
	cursor:pointer;
}
table tr td.setting-value {
	width:200px;
}
table tr td.setting-info {
	width:400px;
	color:#666;
}
table tr td.setting-delete {
	width:100px;
}
input[type=text] {
	width:200px;
}
tr.empty-value {
	color:#999;
}
tr.info-line {
	display:none;
}
</style>


<div id="page-main">

<h3>setting values for "{$module.module}"</h3>

<form id="theForm" method="post">
<input type="hidden" id="action" name="action" value="save" />
<input type="hidden" name="id" value="{$module.id}" />
<table>
	<tr class="tr-highlight">
    	<th class="setting-name">setting</th>
    	<th class="setting-value">value</th>
    	<th class="setting-delete"></th>
    	<th class="setting-delete"></th>
	</tr>
{foreach $settings v}
    {assign var=value value=""}
    {foreach $values u}
    {if $u.setting_id==$v.id}{assign var=value value=$u.value}{/if}
    {/foreach}
	<tr class="tr-highlight{if $value==""} empty-value{/if}">
    	<td class="setting-name" title="{$v.info|@escape}">{$v.setting}</td>
    	<td class="setting-value">
        	<input type="text"  name="value[{$v.id}]" id="value-{$v.id}" value="{$value|@escape}" />
        </td>
    	<td class="setting-delete" title="{$v.default_value|@escape}">
        	{if $v.default_value!=""}
        	<a href="#" class="add-default" onclick="
            	if ($('#value-{$v.id}').val().length==0)
                {
	            	$('#value-{$v.id}').val( '{$v.default_value|@addslashes}' );
    	            return false;
				}">use default</a>
            {else}
            <span style="color:#999">(no default)</span>
            {/if}
            </td>
    	<td class="setting-delete">
	        {if $value!=""}
        	<a href="#" onclick="$('#value-{$v.id}').val( '' );$('#theForm').submit();return false;">delete</a>
            {/if}
		</td>
	</tr>
	<tr class="info-line">
    	<td></td>
    	<td colspan="3" class="setting-info">{$v.info|@escape}</td>
	</tr>    
	<tr class="second-line">
    	<td colspan="4"></td>
	</tr>    
    
{/foreach}
{if $settings|@count==0}
	<tr class="tr-highlight">
    	<td class="setting-name">(none)</td>
	</tr>
{/if}
</table>
<p>
	<a href="#" onclick="$('.add-default').trigger('click');return false;">add all defaults</a> (doesn't overwrite non-empty values)
</p>
<p>
	<input type="submit" value="save" />
</p>

</form>

<a href="settings.php?id={$module.id}">settings</a> | <a href="index.php">index</a>

</div>

{include file="../shared/admin-messages.tpl"}

<script>
$(document).ready(function()
{
	$('#page-block-messages').fadeOut(3000);
	$('td.setting-name').on('click',function() { $(this).closest('tr').next('tr').toggle(); } );
	
});
</script>

{include file="../shared/admin-footer.tpl"}

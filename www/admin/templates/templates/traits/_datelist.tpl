<!--
	datelist
	datelistfree
-->

{assign var=maxlength value=$trait.date_format_format_hr|@strlen}

<script>
var maxlength={$maxlength};

function verifyDate()
{
	var v=$('#newvalue').val();

	$.ajax({
		url:  "ajax_interface.php",
		data: {
			action: 'verifydate',
			date: v,
			format: '{$trait.date_format_format}'
		}
	})
	.always(function(data)
	{
		var d=$.parseJSON(data);
	
		e=Array();
		var r=true;

		if (d===true)
		{
			if (valuelist.indexOf(v)!=-1)
			{
				e.push(_('duplicate value'));
			}
		}
		else
		{
			r=false;
			e.push(d);
		}
		addTraitValue({ result:r, remarks:e });
		updateValueList();
		updateValueCount();
	})
	
}
</script>



<p>
	Add values, optionally drag and drop values to change their show order, and click "save" to save them.
</p>

<form id="theForm">
	<table>
    	<tr>
        	<td>new value:</td>
            <td>
            	<input 
                	type="text" 
                	name="newvalue" 
                    id="newvalue" 
                    maxlength="{$maxlength}" 
                    class="small" 
                    style="text-align:right" 
                    placeholder="{$trait.date_format_format_hr}"
				/>
			    <input type="button" value="add" onclick="verifyDate()" />
			    <span id="remarks"></span><br />
			</td>
		</tr>
		<tr>
        	<td></td><td class="comment"><span id="character-count">{$maxlength}</span> characters left</td>
		</tr>
	</table>

		{t _s1='<span id="value-count">0</span>'}current values (%s):{/t}
        <ul id="valuelist" class="sortable">
        </ul>

    <input type="button" value="save" onclick="setUserSave();saveValues()" />
</form>

<script type="text/JavaScript">
$(document).ready(function()
{
	{foreach from=$trait.values item=v}
	doAddTraitValue( { id: {$v.id}, value: {$v.date|@escape}, usage_count: {$v.usage_total_count} } );
	{/foreach}

	traitValuesInitialise();

});
</script>
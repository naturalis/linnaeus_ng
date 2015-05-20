<!--
	floatlist
	floatlistfree
-->

{if $trait.max_length}{assign var=maxlength value=$trait.max_length}{else}{assign var=maxlength value=$defaultMaxLengthFloatValue}{/if}

<script>
var maxlength={$maxlength};

function checkTraitValue(v)
{
	e=Array();
	r=true;
	
	if (parseFloat(v)==NaN || parseFloat(v)!=v)
	{
		if (v.indexOf(",")!=-1)
			e.push(_('not a floating point number; use a period as decimal mark.'));
		else
			e.push(_('not a floating point number'));
		r=false;
	}

	if (valuelist.indexOf(v)!=-1)
	{
		e.push(_('duplicate value'));
	}
	
	return { result:r, remarks:e };
}

function characterCount()
{
	$('#character-count').html(maxlength-$('#newvalue').val().replace(".","").length);
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
            	<input type="text" name="newvalue" id="newvalue" maxlength="{$trait.max_length}" class="small" style="text-align:right" />
			    <input type="button" value="add" onclick="addTraitValue();updateValueList();updateValueCount();" />
			    <span id="remarks"></span><br />
			</td>
		</tr>
		<tr>
        	<td></td><td class="comment"><span id="character-count">{$maxlength}</span> characters left (excluding decimal point)</td>
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
	doAddTraitValue( { id: {$v.id}, value:{$v.numerical_value|@escape}, usage_count: {$v.usage_total_count} } );
	{/foreach}

	traitValuesInitialise();

});
</script>
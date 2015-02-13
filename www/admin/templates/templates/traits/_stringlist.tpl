<!--
	stringlist
	stringlistfree
-->

<style>
#valuelist li {
	margin-bottom:10px;
}
.language-labels {
	font-size:0.9em;
}
.language-labels input {
	font-size:0.9em;
	margin:0 10px 0 2px;
	width:125px;
}
</style>

{if $trait.max_length}{assign var=maxlength value=$trait.max_length}{else}{assign var=maxlength value=$defaultMaxLengthStringValue}{/if}

<script>
havelabels=true;

var maxlength={$maxlength};
function checkTraitValue(v)
{
	e=Array();
	r=true;
	
/*	
	if (valuelist.indexOf(v)!=-1)
	{
		e.push(_('duplicate value'));
	}
*/	
	return { result:r, remarks:e };
}

</script>

<p>
	Add values, optionally drag and drop values to change their show order, and click "save" to save them.<br />
    The value is the string that will be recognized when importing data sheets, with the language labels you
    can specify how the value will be presented in the front-end of the site.
</p>
    
<form id="theForm">
	<table>
    	<tr>
        	<td>new value:</td>
            <td>
            	<input type="text" name="newvalue" id="newvalue" maxlength="{$maxlength}" />
			    <input type="button" value="add" onclick="addTraitValue();updateValueList();updateValueCount();" />
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
	{foreach from=$languages item=v}
	doAddTraitLanguage( { id:{$v.language_id|@escape}, language: '{$v.language|@escape}' } );
	{/foreach}

	{foreach from=$trait.values item=v}
	var labels=[];
	{foreach from=$v.language_labels item=l key=k}
	labels.push( { language:{$k},label:'{$l|@escape}' } );
	{/foreach}
	doAddTraitValue( { id: {$v.id}, value:'{$v.string_value|@escape}', labels: labels } );
	{/foreach}

	traitValuesInitialise();
});
</script>
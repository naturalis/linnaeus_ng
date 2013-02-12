<div id="graphicValueSelector">

	{*<p id="dialogHeader">
		<span id="state-header">{$c.label}:</span>{if $c.info}<br />{$c.info}{/if}
	</p>*}
	{if $c.info}
	<p id="dialogHeader">
		{$c.info}
	</p>
    {/if}

	<p id="dialogSelectorWindow">
    <div id="dialog-content-inner-inner">

	{if $c.type=='range'}

		{if $states[$c.id][0].value}{assign var=prevRangeValue value=$states[$c.id][0].value}{/if}

            <input style="text-align:right" id="state-value" name="state-value" type="text" value="{$prevRangeValue}" onkeyup="nbcStatevalue=$(this).val();">&nbsp;
            <a href="#" class="clearRange" onclick="nbcClearStateValue($('#state-id').val());return false;">waarde wissen</a>

	{elseif $c.type=='media'}

        <table id="graphicValuesTable">
            <tr>
                {foreach from=$s item=v name=foo}
                
                    {if $states[$c.id][$v.id]}{assign var=selected value=true}{else}{assign var=selected value=false}{/if}
                    {if $remainingStateCount!='*' && !$remainingStateCount[$v.id]}{assign var=irrelephant value=true}{else}{assign var=irrelephant value=false}{/if}
                    
                    <td style="text-align:center;vertical-align:top"{if $selected} class="selectedValue"{/if}{if $irrelephant} class="irrelevant"{/if}>
                        {if !$irrelephant}<a href="#" onclick="{if $selected}nbcClearStateValue{else}nbcSetStateValue{/if}('{$c.prefix}:{$c.id}:{$v.id}');return false;">{/if}
                        <div class="state-image-cell">
                            <img class="state-image" src="{$session.app.project.urls.projectMedia}{$v.file_name}" />
                            {if !$irrelephant}</a>{/if}
                            <br /><br />
                            {$v.label}
                        </div>
                        {if $remainingStateCount[$v.id] && !$selected}({$remainingStateCount[$v.id]}){/if}
                    </td>
                {if ($smarty.foreach.foo.index+1)%$stateImagesPerRow==0}
                </tr><tr>
                {/if}
            {/foreach}
            {math equation="(counter+1) % columns" counter=$smarty.foreach.foo.index columns=$stateImagesPerRow assign=x}
                {'<td>&nbsp;</td>'|str_repeat:$x}
            </tr>
        </table>
        
	{elseif $c.type=='text'}
   
        <ul class="facetListType">
            {foreach from=$s item=v key=k}
            {if $states[$c.id][$v.id]}{assign var=selected value=true}{else}{assign var=selected value=false}{/if}
            {if $remainingStateCount!='*' && !$remainingStateCount[$v.id]}{assign var=irrelephant value=true}{else}{assign var=irrelephant value=false}{/if}
            <li {if $irrelephant}class="irrelevant"{/if}>
                <span class="selected" style="{if $selected}font-weight:bold{/if}">
                   
                    <a href="#" onclick="{if $selected}nbcClearStateValue{else}nbcSetStateValue{/if}('{$c.prefix}:{$c.id}:{$v.id}');closeDialog();return false;">
                    <img 
                    	src="{$session.app.system.urls.systemMedia}orange_checkbox_{if $selected}on{else}off{/if}.png"
                        style="margin-right:10px"
                        >{$v.label}</a>

                </span>
                {if $remainingStateCount[$v.id] && !$selected}({$remainingStateCount[$v.id]}){/if}
            </li>
            {/foreach}
        </ul>

	{/if}
    </div>
	<span id="state-id" class="hidden">{$c.prefix}:{$c.id}</span>
	</p>
	

{*        

	<div id="dialogFooter">
		<p>
			<span class="toolBar">[ <a href="#" onclick="nbcSetStateValue();return false;">ok</a> | <a href="#" onclick="closeDialog();return false;">sluiten</a> ]</span>
		</p>
	</div>
*}

</div>

{literal}
<script type="text/JavaScript">
$(document).ready(function(){
	nbcBindDialogKeyUp();
	$('#state-value').focus();
	$('#state-value').select();
});
</script>
{/literal}

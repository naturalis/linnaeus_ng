<div id="graphicValueSelector">

	<p id="dialogHeader">
		{assign var=foo value="|"|explode:$c.label}{if $foo[0] && $foo[1]}{assign var=cLabel value=$foo[0]}{assign var=cText value=$foo[1]}{else}{assign var=cLabel value=$c.label}{assign var=cText value=''}{/if}
		<span id="state-header">{$cLabel}:</span>{if $cText}<br />{$cText}{/if}
	</p>

	<span id="state-id" class="hidden">{$c.prefix}:{$c.id}</span>
	
	<p id="dialogSelectorWindow">
	{if $c.type=='range'}

		{if $states[$c.id][0].value}{assign var=prevRangeValue value=$states[$c.id][0].value}{/if}

		<div id="dialog-content-inner-inner">
            <input style="text-align:right" id="state-value" name="state-value" type="text" value="{$prevRangeValue}" onkeyup="nbcStatevalue=$(this).val();">&nbsp;
            <a href="#" class="clearRange" onclick="nbcClearStateValue($('#state-id').val())">waarde wissen</a>
		</div>

	{elseif $c.type=='media'}

		<div id="dialog-content-inner-inner">
			<table style="border:1px solid #111">
				<tr>
					{foreach from=$s item=v name=foo}
					
                        {if $states[$c.id][$v.id]}{assign var=selected value=true}{else}{assign var=selected value=false}{/if}
                        {if $remainingStateCount!='*' && !$remainingStateCount[$v.id]}{assign var=irrelephant value=true}{else}{assign var=irrelephant value=false}{/if}
                        
                        <td style="text-align:center;vertical-align:top"{if $selected} class="selectedValue"{/if}{if $irrelephant} class="irrelevant"{/if}>
                            {if !$irrelephant}<a href="#" onclick="{if $selected}nbcClearStateValue{else}nbcSetStateValue{/if}('{$c.prefix}:{$c.id}:{$v.id}');">{/if}
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
		</div>
        
	{elseif $c.type=='text'}
		<div id="dialog-content-inner-inner">
            <table>
                {foreach from=$s item=v key=k}
                {if $states[$c.id][$v.id]}{assign var=selected value=true}{else}{assign var=selected value=false}{/if}
                {if $remainingStateCount!='*' && !$remainingStateCount[$v.id]}{assign var=irrelephant value=true}{else}{assign var=irrelephant value=false}{/if}
                <tr>
                    <td{if $irrelephant} style="opacity: 0.25;"{/if}>
                        <label style="{if $selected}font-weight:bold{/if}">
                            <input onchange="{if $selected}nbcClearStateValue{else}nbcSetStateValue{/if}('{$c.prefix}:{$c.id}:{$v.id}')" type="checkbox"{if $selected} checked="checked"{/if}>{$v.label}
                        </label>
                        {if $remainingStateCount[$v.id] && !$selected}({$remainingStateCount[$v.id]}){/if}
                    </td>
                </tr>
                {/foreach}
            </table>
		</div>
	{/if}

		<!-- p id="resultsCounter">
			0 resultaten in hudige selectie
		</p -->

	</p>

	<div id="dialogFooter">
		<p>
			<span class="toolBar">[ <a href="#" onclick="nbcSetStateValue();">ok</a> | <a href="#" onclick="closeDialog();">sluiten</a> ]</span>
		</p>
	</div>

</div>

{literal}
<script type="text/JavaScript">
$(document).ready(function(){
{/literal}
nbcBindDialogKeyUp();
$('#state-value').focus();
{literal}
});
</script>
{/literal}

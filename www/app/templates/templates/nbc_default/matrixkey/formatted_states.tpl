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
			<p>
            <input id="state-value" name="state-value" type="text" value="{$prevRangeValue}" onkeyup="nbcStatevalue=$(this).val();">&nbsp;
            <a href="#" class="clearRange" onclick="nbcClearStateValue($('#state-id').val());return false;">waarde wissen</a>
            </p>
			{if $c.min && $c.max}
            <p id="state-value-extra">
            	{t _s1=$c.min_display _s2=$c.max_display _s3=$c.unit}Kies een waarde tussen %s en %s%s.{/t}
			</p>
           {/if}
            
	{elseif $c.type=='media'}

        <table id="graphicValuesTable">
            <tr>
                {foreach from=$s item=v name=foo}
                    {if $states[$c.id][$v.id]}{assign var=selected value=true}{else}{assign var=selected value=false}{/if}
                    {if $remainingStateCount!='*' && !$remainingStateCount[$v.id]}{assign var=irrelephant value=true}{else}{assign var=irrelephant value=false}{/if}
                    <td{if $selected} class="selectedValue"{/if}{if $irrelephant} class="irrelevant"{/if}>
                        <div class="state-image-cell" style="padding:0">
							{if !$irrelephant}<a href="#" onclick="{if $selected}nbcClearStateValue{else}nbcSetStateValue{/if}('{$c.prefix}:{$c.id}:{$v.id}');return false;">{/if}
                            <img class="state-image" src="{if $v.file_name}{$session.app.project.urls.projectMedia}{$v.file_name}{else}{$session.app.project.urls.systemMedia}missing.jpg{/if}" />
							{if !$irrelephant}</a>{/if}
                            <p>{$v.label}</p>
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

</div>

{literal}
<script type="text/JavaScript">
$(document).ready(function(){
	if (typeof nbcInit=='function') {
		nbcInit();
	}
	nbcBindDialogKeyUp();
	$('#state-value').focus();
	$('#state-value').select();
});
</script>
{/literal}

<div id="graphicValueSelector">

	{*<p id="dialogHeader">
		<span id="state-header">{$character.label}:</span>{if $character.info}<br />{$character.info}{/if}
	</p>*}

	<p id="dialogSelectorWindow">

        <div id="dialog-content-inner-inner">
        
            {if $character.info}
            <p id="state-info">{$character.info}</p>
            {/if}
        
        {if $character.type=='range'}

            <p>
            	<input
                	id="state-value" 
                    name="state-value" 
                    type="text" 
                    value="{$states_selected[$character.id].value}" 
                    onkeyup="tempstatevalue=$(this).val();"
				>&nbsp;
            	<a 
                	href="#" 
                    class="clearRange" 
                    onclick="clearStateValue($('#state-id').val());$('#state-value').val('');return false;"
				>{t}waarde wissen{/t}</a>
            </p>

            {if $character.min && $character.max}

            <p id="state-value-extra">
                {t _s1=$character.min_display _s2=$character.max_display _s3=$character.unit}Kies een waarde tussen %s en %s%s.{/t}
            </p>

	        {/if}

        {elseif $character.type=='media'}

            <table id="graphicValuesTable">
                <tr>
                    {foreach from=$states item=v name=foo}
                    
                    {if $states_remain_count!=null && !isset($states_remain_count[{$v.id}])}
	                    {assign var=irrelevant value=true}
                    {else}
    	                {assign var=irrelevant value=false}
                    {/if}
                    
                    {if isset($states_selected[{$v.id}])}
	                    {assign var=selected value=true}
                    {else}
    	                {assign var=selected value=false}
                    {/if}
                    
					<td class="{if $selected}selectedValue{/if}{if $irrelevant}irrelevant{/if}">
						<div class="state-image-cell" style="padding:0;">
                        {if !$irrelevant}
                        {if $selected}
                            <a href="#" onclick="clearStateValue('{$character.prefix}:{$character.id}:{$v.id}');jDialogCancel();return false;">
                        {else}
                            <a href="#" onclick="setStateValue('{$character.prefix}:{$character.id}:{$v.id}');jDialogCancel();return false;">
                        {/if}
                        {/if}
                                <img
                                	class="state-image{if $irrelevant} state-image-irrelevant{/if}"
                                    {if $v.file_name}
                                    	src="{$projectUrls.projectMedia}{$v.file_name}"
                                    {else}
                                    	src="{$projectUrls.systemMedia}missing.jpg"
									{/if}
                                   />
                        {if !$irrelevant}
							</a>
						{/if}
                            <p class="state-image-caption">{$v.label}</p>
						</div>

                        {if !isset($states_selected[{$v.id}]) && isset($states_remain_count[{$v.id}])}
                           ({$states_remain_count[{$v.id}]})
                        {/if}
					</td>
                    {if ($smarty.foreach.foo.index+1)%$state_images_per_row==0}
                    </tr><tr>
                    {/if}
                	{/foreach}

	                {math equation="(counter+1) % columns" counter=$smarty.foreach.foo.index columns=$state_images_per_row assign=x}
                    {'<td>&nbsp;</td>'|str_repeat:$x}

                </tr>
            </table>
           
        {elseif $character.type=='text'}

            <ul class="facetListType">

                {foreach from=$states item=v key=foo}
                    
                    {if $states_remain_count!=null && !isset($states_remain_count[{$v.id}])}
	                    {assign var=irrelevant value=true}
                    {else}
    	                {assign var=irrelevant value=false}
                    {/if}
                    
                    {if isset($states_selected[{$v.id}])}
	                    {assign var=selected value=true}
                    {else}
    	                {assign var=selected value=false}
                    {/if}

				<li{if $irrelevant} class="irrelevant"{/if}>
                	<span class="selected" {if $selected}style="font-weight:bold"{/if}>
                    	{if !$irrelevant}
                        <a href="#" 
                        	onclick="{if $selected}
                            clearStateValue('{$character.prefix}:{$character.id}:{$v.id}');
                            {else}
                            setStateValue('{$character.prefix}:{$character.id}:{$v.id}');
                            {/if}
                            closeDialog();
                            return false;" >
						{/if}
                            <img 
                            	src="{$session.app.system.urls.systemMedia}orange_checkbox_{if $selected}on{else}off{/if}.png" 
                                style="margin-right:10px">{$v.label}
						{if !$irrelevant}
						</a>
                        {/if}
					</span>
                    {if $states_remain_count[{$v.id}] && !$selected}({$states_remain_count[{$v.id}]}){/if}
                </li>
                {/foreach}
            </ul>
    
        {/if}

        </div>

        <input id="state-id" type="hidden" value="{$character.prefix}:{$character.id}">

	</p>

</div>

{literal}
<script type="text/JavaScript">
$(document).ready(function()
{
	if (typeof matrixInit=='function')
	{
		matrixInit();
	}

	bindDialogKeyUp();

	$('#state-value').focus();
	$('#state-value').select();
});
</script>
{/literal}

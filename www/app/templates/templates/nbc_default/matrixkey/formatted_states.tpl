<div id="graphicVaslueSelector">

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


			<div class="state-images-container">

            {foreach from=$states item=v name=foo key=key}
            
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
           
                <div class="state-image-container{if $selected} state-image-selected{/if}{if $irrelevant} state-image-irrelevant{/if}">
                	<div class="state-image-buffer">
                        {if !$irrelevant}{if $selected}
                            <a href="#" onclick="clearStateValue('{$character.prefix}:{$character.id}:{$v.id}');jDialogCancel();return false;">
                        {else}
                            <a href="#" onclick="setStateValue('{$character.prefix}:{$character.id}:{$v.id}');jDialogCancel();return false;">
                        {/if}{/if}
                    	<img
                        	class="state-image" 
                            key="{$key}"
                            {if $v.file_name}
                            src="{$projectUrls.projectMedia}{$v.file_name}"
                            {else}
                            src="{$projectUrls.projectMedia}missing.jpg"
                            {/if}                    
                        >{if !$irrelevant}</a>{/if}<a id="full-size-link-{$key}" rel="prettyPhoto[states]" href="{$projectUrls.projectMedia}{$v.file_name}" pTitle="{$character.label|@escape}: {$v.label|@escape}" title="" style="display:none;"><img class="full-size-icon" src="{$image_root_skin}full-size-icon.png"></a>
					</div>
					<div class="state-image-caption">{$v.label}
                    {if !isset($states_selected[{$v.id}]) && isset($states_remain_count[{$v.id}])}
                        <br />
                       ({$states_remain_count[{$v.id}]})
                    {/if}
                    </div>
                </div>
                
			{/foreach}
            
            </div>
    

		{*
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
                    
					<td class="{if $selected}selectedValue{/if}{if $irrelevant}irrelevant{/if}" style="max-width:200px;">
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
                                    src="{$projectUrls.projectMedia}missing.jpg"
                                {/if}
                               />
                        {if !$irrelevant}
							</a>
						{/if}

                        <div class="state-image-caption" style="width:100%">{$v.label}
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
		*}
           
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

				<li class="{if $irrelevant}irrelevant{/if}">
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
                            	src="{$image_root_skin}orange_checkbox_{if $selected}on{else}off{/if}.png" 
                                style="margin-right:10px"
							>{$v.label}
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

<script type="text/JavaScript">
$(document).ready(function()
{
	if(jQuery().prettyPhoto)
	{
		prettyPhotoInit();
	}
	
	bindDialogKeyUp();

	$('#state-value').focus();
	$('#state-value').select();
	
    $(".state-image").load(function()
	{
		// dimensions as shown
		var h=this.height,w=this.width;
		var actualHeight=this.height,actualWidth=this.width;

		// dimensions of full image
		var img = new Image();
		img.src = $(this).attr("src");
		var fullHeight=img.height,fullWidth=img.width;

		if (actualHeight<img.height || actualWidth<img.width)
		{
			$("#full-size-link-"+$(this).attr("key")).toggle(true);
		}
    })
});
</script>

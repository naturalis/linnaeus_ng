<div id="graphicVaslueSelector">

	<p id="dialogSelectorWindow">

        <div id="dialog-content-inner-inner">

        {if $character.info}
        <p id="state-info">
            {$character.info}
        </p>
        {/if}
        
        {if $character.type=='range'}
        
        	<div style="text-align:left">

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
                
                <script>
				$('#state-value').bind('keyup',function()
				{
					{if $character.min}var min={$character.min};{/if}
					{if $character.max}var max={$character.max};{/if}

					var val=$(this).val();
					
					if (val.length==0) return;
					
					val=parseFloat(val);

					if (val==NaN || (min && val<min) || (max && val>max))
					{
						$(this).css('color','red');
					}
					else
					{
						$(this).css('color','black');
					}
				});
				</script>
    
                {/if}
            
            </div>

        {elseif $character.type=='media'}

        <div id='dialog-content-inner-inner'>
          <div id='graphicValues'>

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

			<div class="state-image-cell {if $selected}active{elseif $irrelevant}disabled{else}selectable{/if}" 
            	onclick="
                {if $selected}
                	clearStateValue('{$character.prefix}:{$character.id}:{$v.id}');jDialogCancel();return false;
				{else}
                	setStateValue('{$character.prefix}:{$character.id}:{$v.id}');jDialogCancel();return false;
                {/if}
                ">
				<img class="state-image" 
                    {if $v.file_name}
                    src="{$projectUrls.projectMedia}{$v.file_name}"
                    {else}
                    src="{$image_root_skin}missing.jpg"
                    {/if}                 
                />
				<p class="state-image-caption">
					{$v.label}
				</p>
				<p class='state-count'>
                    {if !isset($states_selected[{$v.id}]) && isset($states_remain_count[{$v.id}])}({$states_remain_count[{$v.id}]}){/if}
				</p>
			</div>
                
			{/foreach}
            
            </div>
		</div>

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
                	{if $selected}<span class="selected" style="font-weight:bold">{/if}
                    	{if !$irrelevant}
                        <a href="#" 
                        	onclick="{if $selected}
                            clearStateValue('{$character.prefix}:{$character.id}:{$v.id}');
                            {else}
                            setStateValue('{$character.prefix}:{$character.id}:{$v.id}');
                            {/if}
                            closeDialog();
                            return false;" >
                        {else}
                        <a>
						{/if}
                            <img 
                            	class="orange_checkbox"
                            	src="{$image_root_skin}orange_checkbox_{if $selected}on{else}off{/if}.png" 
                                style="margin-right:10px"
							>{$v.label}

						</a>

                	{if $selected}</span>{/if}
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

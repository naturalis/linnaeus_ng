	  <div id="graphicValueSelector" class="{if $c.type=='media'}layout-grid{else}layout-list{/if}">

        <p id='dialogSelectorWindow'></p>

        {if $c.info}
        <p id="state-info">
            {$c.info}
        </p>
        {/if}
    
	{if $c.type=='range'}

		{if $states[$c.id][0].value}{assign var=prevRangeValue value=$states[$c.id][0].value}{/if}

			<p>
            <input id="state-value" name="state-value" type="text" value="{$prevRangeValue}" onkeyup="nbcStatevalue=$(this).val();">&nbsp;
            <a href="#" class="clearRange" onclick="nbcClearStateValue($('#state-id').val());return false;">{t}waarde wissen{/t}</a>
            </p>
			{if $c.min && $c.max}
            <p id="state-value-extra">
            	{t _s1=$c.min_display _s2=$c.max_display _s3=$c.unit}Kies een waarde tussen %s en %s%s.{/t}
			</p>
           {/if}
            
	{elseif $c.type=='media'}

        <div id='dialog-content-inner-inner'>
          <div id='graphicValues'>

                {foreach from=$s item=v name=foo}
                    {if $states[$c.id][$v.id]}{assign var=active value=true}{else}{assign var=active value=false}{/if}
                    {if $remainingStateCount!='*' && !$remainingStateCount[$v.id]}{assign var=disabled value=true}{else}{assign var=disabled value=false}{/if}

			<div class="state-image-cell {if $active}active{elseif $disabled}disabled{else}selectable{/if}" onclick="{if $active}nbcClearStateValue{else}nbcSetStateValue{/if}('{$c.prefix}:{$c.id}:{$v.id}');">
				<img class="state-image" src="{if $v.file_name}{$projectUrls.projectMedia}{$v.file_name}{else}{$projectUrls.systemMedia}missing.jpg{/if}" />
				<p class="state-image-caption">
					{$v.label}
				</p>
				<p class='state-count'>
					{if !$active}({if $remainingStateCount[$v.id]}{$remainingStateCount[$v.id]}{else}0{/if}){/if}
				</p>
			</div>

    	        {/foreach}

        </div>
      </div>

       
	{elseif $c.type=='text'}
   
        <ul class="facetListType">
            {foreach from=$s item=v key=k}
			
            {if $states[$c.id][$v.id]}{assign var=selected value=true}{else}{assign var=selected value=false}{/if}
            {if $remainingStateCount!='*' && !$remainingStateCount[$v.id]}{assign var=irrelephant value=true}{else}{assign var=irrelephant value=false}{/if}

            <li {if $selected}class="active"{elseif $irrelephant}class="irrelevant"{/if}>

				{if $irrelephant && !$selected}
					<a>{$v.label}</a>
				{else}
                    <a href="#" onclick="{if $selected}nbcClearStateValue{else}nbcSetStateValue{/if}('{$c.prefix}:{$c.id}:{$v.id}');closeDialog();return false;">
					{$v.label}
					</a>
				{/if}

                {if $remainingStateCount[$v.id] && !$selected}({$remainingStateCount[$v.id]}){/if}
            </li>
            {/foreach}
        </ul>

	{/if}
	<span id='state-id' class='hidden'>{$c.prefix}:{$c.id}</span>
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

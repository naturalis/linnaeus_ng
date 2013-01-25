<div id="graphicValueSelector">

	<form id="theForm" method="post" action="identify.php">
	<input type="hidden" id="state-id" name="state" value="{$c.prefix}:{$c.id}" />
	
	<p id="dialogHeader">
		{assign var=foo value="|"|explode:$c.label}{if $foo[0] && $foo[1]}{assign var=cLabel value=$foo[0]}{assign var=cText value=$foo[1]}{else}{assign var=cLabel value=$c.label}{assign var=cText value=''}{/if}
		<span id="state-header">{$cLabel}:</span>{if $cText}<br />
		{$cText}{/if}
	</p>
	<p id="dialogSelectorWindow">
	{if $c.type=='range'}
		{if $states[$c.id][0].value}{assign var=prevRangeValue value=$states[$c.id][0].value}{/if}
		<input style="text-align:right" id="range-value" name="state-value" type="text" value="{$prevRangeValue}">&nbsp;
		<a href="#" class="removeBtn" onclick="$('#range-value').val('');$('#action2').val('clear');$('#id2').val('{$s.key}');$('#form2').submit();">waarde wissen</a>
		{elseif $c.type=='media'}
		<div id="dialog-content-inner-inner">
			<table style="border:1px solid #111">
				<tr>
					{foreach from=$s item=v name=foo}
					
					{if $states[$c.id][$v.id]}{assign var=selected value=true}{else}{assign var=selected value=false}{/if}
					{if $remainingStateCount!='*' && !$remainingStateCount[$v.id]}{assign var=irrelephant value=true}{else}{assign var=irrelephant value=false}{/if}
					
					<td style="text-align:center;vertical-align:top"{if $selected} class="selectedValue"{/if}{if $irrelephant} class="irrelevant"{/if}>
						{if !$irrelephant}<a href="identify.php?state={$c.prefix}:{$c.id}:{$v.id}">{/if}
						<div class="state-image-cell">
							<img 
								class="state-image" 
								src="{$session.app.project.urls.projectMedia}{$v.file_name}"  
							/>
						{if !$irrelephant}</a>{/if}<br /><br />
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
		<table>
			{foreach from=$s item=v key=k}
			<tr>
				<td>
					<label><input onchange="window.open('identify.php?state={$c.prefix}:{$c.id}:{$v.id}','_self');" type="checkbox">{$v.label} </label>
				</td>
			</tr>
			{/foreach}
		</table>
		{/if}
	</p>
	
	<p id="resultsCounter">
		<span>{$results|@count} resultaten in hudige selectie</span>
	</p>

	<p id="dialogFooter">
		[ <a href="#" onclick="$('#theForm').submit();">ok</a> | <a href="#" onclick="closeDialog();">sluiten</a> ]
	</p>
	</form>

</div>

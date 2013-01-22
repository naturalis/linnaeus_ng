<div style="padding:0px 10px 0px 10px;">

	<form id="theForm" method="post" action="identify.php">
	<input type="hidden" id="state-id" name="state" value="{$c.prefix}:{$c.id}" />
	
	<p>
		{assign var=foo value="|"|explode:$c.label}{if $foo[0] && $foo[1]}{assign var=cLabel value=$foo[0]}{assign var=cText value=$foo[1]}{else}{assign var=cLabel value=$c.label}{assign var=cText value=''}{/if}
		<span id="state-header">{$cLabel}:</span>{if $cText}<br />
		{$cText}{/if}
	</p>
	
	<p>
	{if $c.type=='range'}
		<input id="range-value" name="state-value" type="text" value="">&nbsp;<a href="#">waarde wissen</a>
	{elseif $c.type=='media'}
	<div id="dialog-content-inner-inner">
		<table style="border:1px solid #111">
			<tr>
				{foreach from=$s item=v key=k}
				<td style="text-align:center;">
					<div class="state-image-cell">
						<img 
							class="state-image" 
							src="{$session.app.project.urls.projectMedia}{$v.file_name}" 
							onclick="$('#state-id').val('{$c.prefix}:{$c.id}:{$v.id}');$('#theForm').submit();" 
						/><br /><br />
						{$v.label}
					</div>
					(0)
				</td>
			{if ($k+1)%$stateImagesPerRow==0}
			</tr><tr>
			{/if}
			{/foreach}
			{math equation="(counter+1) % columns" counter=$k columns=$stateImagesPerRow assign=x}
				{'<td>&nbsp;</td>'|str_repeat:$x}
			</tr>
		</table>
	</div>
	{elseif $c.type=='text'}
		<table>
			{foreach from=$s item=v key=k}
			<tr>
				<td>
					<label><input type="checkbox">{$v.label}</label>
				</td>
			</tr>
			{/foreach}
		</table>
	{/if}
	</p>
	
	<p>
		<span>{$results|@count} resultaten in hudige selectie</span>
	</p>
	<p style="border-top:1px dotted black;padding:10px 0px 0px 0px;text-align:center;">
		[ <a href="#" onclick="$('#theForm').submit();">ok</a> | <a href="#" onclick="closeDialog();">sluiten</a> ]
	</p>
	</form>

</div>
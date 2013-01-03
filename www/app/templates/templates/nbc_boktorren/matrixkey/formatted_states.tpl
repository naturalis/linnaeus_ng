{assign var=foo value="|"|explode:$c.label}{if $foo[0] && $foo[1]}{assign var=cLabel value=$foo[0]}{assign var=cText value=$foo[1]}{else}{assign var=cLabel value=$c.label}{assign var=cText value=''}{/if}
<div style="height:200x;padding:10px;overflow-y:scroll;">
{literal}
<style>
.state-image {
	border:2px solid #eee;
}
.state-image:hover {
	border:2px solid #666;
}
</style>
{/literal}
<form mehtod="post">
<input type="hidden" name="c" value="{$c.id}" />
<p>
	<b>{$cLabel}:</b>
	{if $cText}<br />{$cText}{/if}
</p>
<p>
{if $c.type=='range'}
	<input id="range-value" type="text" value="">&nbsp;<a href="#">waarde wissen</a>
{elseif $c.type=='media'}
	<table>
	<tr>
	{foreach from=$s item=v key=k}
	<td style="text-align:center;">
	<div style="margin: 15px;">
		<img class="state-image" src="{$session.app.project.urls.projectMedia}{$v.file_name}" /><br /><br />
		{$v.label}
	</div>
	</td>
	{if ($k+1)%$stateImagesPerRow==0}
	</tr><tr>
	{/if}
	{/foreach}
	{math equation="(counter+1) % columns" counter=$k columns=$stateImagesPerRow assign=x}
	{'<td>&nbsp;</td>'|str_repeat:$x}
	</tr>
	</table>
{elseif $c.type=='text'}
	<table>
	{foreach from=$s item=v key=k}
	<tr>
	<td>
		<input type="checkbox">
		{$v.label}
	</td>
		</tr>
	{/foreach}
	</table>
{/if}
</p>
</div>
<p>
	<span>{$results|@count} resultaten in hudige selectie</span>
</p>
<p style="border-top:1px dotted black;padding:10px 0px 0px 0px;text-align:center;">
	[ <a href="#">ok</a> | <a href="#" onclick="closeDialog();">cancel</a> ]
</p>
</form>
</div>
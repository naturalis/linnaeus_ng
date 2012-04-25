{include file="../shared/header.tpl"}

<div id="page-main">

	{include file="_navigator-menu.tpl"}

	<table>
		<tr>
		{assign var=i value=1}
		{foreach from=$menu key=k item=v}
		{if $v.type=='regular' && $v.show_in_public_menu==1}
		<td style="width:100px;height:100px;text-align:center">
			<a class="menu-item" href="../{$v.controller}/">
				<img alt="{$v.module}" style="width:50px;height:50px;border:1px solid grey" src="../../media/system/module_icons/{$v.icon}" />
				<br />
				{t}{$v.module}{/t}
			</a>
		</td>
		{assign var=i value=$i+1}
		{elseif $v.show_in_public_menu==1}
		<td style="width:100px;height:100px;text-align:center">
			{if $useJavascriptLinks}
			<span class="a" onclick="goMenuModule({$v.id});">
				<img alt="{$v.module}" style="width:50px;height:50px;border:1px solid grey" src="../../media/system/module_icons/custom.png" />
				<br />
				{t}{$v.module}{/t}
			</span>
			{else}
			<a href="../module/?modId={$v.id}">
				<img alt="{$v.module}" style="width:50px;height:50px;border:1px solid grey" src="../../media/system/module_icons/custom.png" />
				<br />
				{t}{$v.module}{/t}
			</a>
			{/if}
		</td>
		{assign var=i value=$i+1}
		{/if}
		{if $i % 3 ==1}</tr><tr>{/if}
		{/foreach}
		</tr>
	</table>
</div>

{include file="../shared/footer.tpl"}

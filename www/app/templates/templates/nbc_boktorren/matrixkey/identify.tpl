<h2>BOKTORREN!</h2>

<i>en hoe gaan we dat eigenlijk doen met projectspecifieke javascripts, zo in het nieuwe jaar? </i>

<br /><br />

<div id="menu">
{foreach from=$groups item=v}
	<div id="menu-group-{$v.id}">
		<span style="font-weight:bold" onclick="toggleGroup({$v.id})">{$v.label}</span><br />
		{foreach from=$v.chars item=c}
		{$c.label}<br />
		{/foreach}
	</div>
{/foreach}
</div>
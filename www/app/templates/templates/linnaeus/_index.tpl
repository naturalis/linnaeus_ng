<div id="page-main">
	<table>
		<tr>
			<td class="category{if $subject=='Welcome'}-active{/if}" onclick="goContentPage('Welcome')">{t}Welcome{/t}</td><td class="space"></td>
			<td class="category{if $subject=='Contributors'}-active{/if}" onclick="goContentPage('Contributors')">Contributors</td><td class="space"></td>
			<td class="category{if $subject=='About ETI'}-active{/if}" onclick="goContentPage('About ETI')">About ETI</td>
		</tr>
	</table>
	<div id="general-content">
	{$content}
	</div>
</div>


<table>
	<tr>
		<td class="navigator{if $subject==''}-active{/if}" onclick="goNavigator()">{t}Content{/t}</td><td class="space"></td>
		<td class="navigator{if $subject=='Welcome'}-active{/if}" onclick="goContentPage('Welcome')">{t}Welcome{/t}</td><td class="space"></td>
		<td class="navigator{if $subject=='Contributors'}-active{/if}" onclick="goContentPage('Contributors')">Contributors</td><td class="space"></td>
		<td class="navigator{if $subject=='About ETI'}-active{/if}" onclick="goContentPage('About ETI')">About ETI</td>
	</tr>
</table>

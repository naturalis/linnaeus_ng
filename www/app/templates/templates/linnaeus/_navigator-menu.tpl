<table>
	<tr>
	{if $useJavascriptLinks}
		<td class="navigator{if $subject==''}-active{/if}" onclick="goNavigator()">{t}Content{/t}</td><td class="space"></td>
		<td class="navigator{if $subject=='Welcome'}-active{/if}" onclick="goContentPage('Welcome')">{t}Welcome{/t}</td><td class="space"></td>
		<td class="navigator{if $subject=='Contributors'}-active{/if}" onclick="goContentPage('Contributors')">{t}Contributors{/t}</td><td class="space"></td>
		<td class="navigator{if $subject=='About ETI'}-active{/if}" onclick="goContentPage('About ETI')">{t}About ETI{/t}</td>
	{else}
		<td class="navigator{if $subject==''}-active{/if}"><a href="../linnaeus/index.php">{t}Content{/t}</a></td><td class="space"></td>
		<td class="navigator{if $subject=='Welcome'}-active{/if}"><a href="../linnaeus/content.php?sub=Welcome">{t}Welcome{/t}</a></td><td class="space"></td>
		<td class="navigator{if $subject=='Contributors'}-active{/if}"><a href="../linnaeus/content.php?sub=Contributors">{t}Contributors{/t}</a></td><td class="space"></td>
		<td class="navigator{if $subject=='About ETI'}-active{/if}"><a href="../linnaeus/content.php?sub=About ETI">{t}About ETI{/t}</a></td>
	{/if}
	</tr>
</table>

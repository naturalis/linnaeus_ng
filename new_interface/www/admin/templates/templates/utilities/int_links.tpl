<script>

intLinkSetLanguage({$language});
{foreach from=$internalLinks key=k item=v}
intLinkStoreLinks('{$v.label}','{$v.controller}','{if $v.url}{$v.url}{else}index.php{/if}'{if $v.params},'{$v.params}'{/if});
{/foreach}
</script>
<table id="int-link-selectors">
<thead>
	<tr>
		<th colspan="2">{t}Insert a link to:{/t}</td>
	</tr>
</thead>
<tbody>
	<tr>
		<td style="width:100px;">{t}Module:{/t}</td>
		<td>
			<select id="module-selector" onchange="intLinkModuleSelectorChange()">
			{foreach from=$internalLinks key=k item=v}
			<option value="{$k}">{$v.label}</option>
			{/foreach}
			</select>
		</td>
	</tr>
</tbody>
</table>
<p>
<input type="button" value="{t}insert link{/t}" onclick="intLinkInsertLink()" />
<input type="button" value="{t}close{/t}" onclick="$('#dialog-close').click()" />
</p>
{literal}
<script type="text/JavaScript">
$(document).ready(function(){

	intLinkModuleSelectorChange();

});
</script>
{/literal}

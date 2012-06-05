<div id="path">
<script>
	var tmp = '<table>';
	{foreach from=$keypath key=k item=v}
		tmp = tmp + '<tr>'+
			'<td style="text-align:right;padding-right:5px">{$v.step_number|@escape}. </td>'+
			'<td><a href="javascript:void(0);keyDoStep({$v.id})">{$v.step_title|@escape}{if $v.choice_marker} ({$v.choice_marker|@escape}){/if}</a></td>'+
			'</tr>';
	{/foreach}
	tmp = tmp + '</table>';
</script>
	<a href="javascript:void(0);showDialog('Path',tmp);" id="toggle">{t}Path{/t}</a>
</div>
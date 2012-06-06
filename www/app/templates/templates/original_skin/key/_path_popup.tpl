<div id="path">
<script type="text/javascript">
    var tmp = '<table>';
    {if $keypath|@count > 1}
        {foreach from=$keypath key=k item=v name=pathPopup}
            {if !$smarty.foreach.pathPopup.last}
            tmp = tmp + '<tr>'+
                '<td style="text-align:right;padding-right:5px">{$v.step_number|@escape}. </td>'+
                '<td><a href="javascript:void(0);keyDoStep({$v.id})">{$v.step_title|@escape}{if $v.choice_marker} ({$v.choice_marker|@escape}){/if}</a></td>'+
                '</tr>';
            {/if}
        {/foreach}
    {else}
        tmp = tmp + '<tr><td>{t}No choices made yet{/t}</td></tr>';
    {/if}
    tmp = tmp + '</table>';
</script>
	<a href="javascript:void(0);showDialog(_('Path'),tmp);" id="toggle">{t}Path{/t}</a>
</div>
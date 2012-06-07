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

<div id="path">
    <div id="path-search-box">
        {include file="../shared/_search-box.tpl"}
    </div>
	<div id="concise">
	<span onclick="showDialog('{t}Decision path{/t}',tmp);" id="toggle" class="selectIcon">{t}Path{/t}</span>
	{foreach from=$keypath key=k item=v}
	{if $v.is_start==1 || $keypath|@count<=$keyPathMaxItems || ($keypath|@count>$keyPathMaxItems && $k>=$keypath|@count-2)}
		{if $v.is_start!=1}<span class="arrow">&rarr;</span>{/if}
		{$v.step_number}. <span class="item" onclick="keyDoStep({$v.id})">{$v.step_title}{if $v.choice_marker} ({$v.choice_marker}){/if}</span>
	{/if}
	{if $v.is_start==1 && $keypath|@count>$keyPathMaxItems}<span class="arrow">&rarr;</span><span class="abbreviation">[...]</span>{/if}
	{/foreach}
	</div>
</div>
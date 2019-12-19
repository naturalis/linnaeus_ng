<div id="keypath">
	<span style="color:#888;cursor:pointer" onclick="keyToggleFullKeyPath()" title="{t}show entire path{/t}">&nabla;</span>
	<span id="keypath-title">{t}Decision path{if $keyPath[0].is_start==0} {t}(subsection){/t}{/if}{/t}: </span>

{assign var=first value=true}
{foreach $keyPath v i}
	{if $i==$keyPath|@count-1}
		<span class="a" onclick="$('#pathNext').val({$v.id});$('#pathForm').submit();">{if $v.number}{$v.number}. {/if}{$v.title}</span>
	{else}	
		{if $keyPath|@count>3 && 
			$i!=0 &&
			$i!=$keyPath|@count-1 &&
			$i!=$keyPath|@count-2}
		{if $first}[...]&nbsp;&rarr;&nbsp;{/if}{assign var=first value=false}
		{else}
		<i>
			<span class="a" onclick="$('#pathNext').val({$v.id});$('#pathForm').submit();">{$v.number}. {$v.title}</span>{if $v.choice} ({$v.choice_marker})
			{/if}
		</i>
		{if $keyPath|@count>1}&nbsp;&rarr;&nbsp;{/if}
		{/if}
	{/if}
{/foreach}
</div>

<div id="keypath-full" class="keypath-full-invisible">
    <fieldset>
    <legend>{if $keyPath[0].is_start==0}{t}Full subsection keypath:{/t}{else}{t}Full keypath{/t}{/if}</legend>
    <table style="width:100%">
        {foreach $keyPath v i}
        <tr>
            {if $i==$keyPath|@count-1}
            <td style="text-align:right;width:10px;">{if $v.number}{$v.number}.{/if}</td><td>{$v.title}</td>
            {else}	
            <td class="a" onclick="$('#pathNext').val({$v.id});$('#pathForm').submit();" style="text-align:right;width:10px;">{$v.number}.</td>
            <td class="a" onclick="$('#pathNext').val({$v.id});$('#pathForm').submit();">{$v.title}</span>{if $v.choice} ({$v.choice_marker}){/if}</td>
            {/if}
            <td></td>
        </tr>
        {/foreach}
        <tr><td colspan="3" style="padding-top:10px">[<span class="a" onclick="keyToggleFullKeyPath();">{t}close{/t}</span>]</td></tr>
    </table>
    <form method="get" action="step_show.php" id="pathForm"><input type="hidden" name="id" id="pathNext" value="" /></form>
    </fieldset>
</div>

	<p>
		<h2 id="name-header">Exoteninformatie</h2>

        {foreach from=$content.result.data item=v key=k}
        <p>
        {if $v.trait.type=='stringfree'}
			<strong>{$v.trait.name}</strong><br />
			{$v.values[0].value_start}{if $v.values[0].value_end} - {$v.values[0].value_end}{/if}
        {else if $v.values|@count==1}
			<strong>{$v.trait.name}:</strong>
			{$v.values[0].value_start}{if $v.values[0].value_end} - {$v.values[0].value_end}{/if}
        {else}
			<strong>{$v.trait.name}:</strong>
        	<ul>
        	{foreach from=$v.values item=l}
            <li>
            {$l.value_start}{if $l.value_end} - {$l.value_end}{/if}
            </li>
            {/foreach}
            </ul>
        {/if}
        </p>
        {/foreach}
	</p>

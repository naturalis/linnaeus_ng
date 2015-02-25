<style>
ul li {
	padding:0;
	margin:0;
	list-style:inside;
}
</style>
	<div>

		<h2 id="name-header">Exotenpaspoort</h2>

        {foreach from=$content.result.data item=v key=k}
        {if $v.values|@count==1}

			<strong>{$v.trait.name}</strong><br />
			{$v.values[0].value_start}{if $v.values[0].value_end} - {$v.values[0].value_end}{/if}
            <br />
            <br />

        {else}

			<strong>{$v.trait.name}</strong><br />
        	<ul>
        	{foreach from=$v.values item=l}
            <li>
            {$l.value_start}{if $l.value_end} - {$l.value_end}{/if}
            </li>
            {/foreach}
            </ul>
            <br />

        {/if}

        {/foreach}


	</div>
	<p>
		<h2 id="name-header">Exoteninformatie</h2>

		<table>
        {foreach from=$content.result.data item=v}
        <tr>
        	<td>{if $v.trait.name!=$prevTrait}{$v.trait.name}{/if}</td>
        	<td>{$v.values.value_start}</td>
        	<td>{$v.values.value_end}</td>
        </tr>
        {assign var=prevTrait value=$v.trait.name}
        {/foreach}
		</table>
	</p>

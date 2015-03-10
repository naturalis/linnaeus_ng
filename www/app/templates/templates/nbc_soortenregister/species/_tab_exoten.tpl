<style>
.exotica {
	width:510px;	
	border-collapse:collapse;
}
.exotica td {
	padding:1px 0 1px 0;
}
.legend-cell {
	width:200px;
}
.last-row {
	border-bottom:1px solid #eee;
	padding-bottom:0px;
}
</style>
	<div>

		<h2 id="name-header">Exotenpaspoort
		<a
        	href="http://www.nederlandsesoorten.nl/content/exotenpaspoort" 
            target="_blank"  
            title="{t}klik voor help over dit onderdeel{/t}" 
            class="help"
            style="float:right"
		>&nbsp;</a>
        </h2>
		<table class="exotica">
        {foreach from=$content.result.data item=v}
        	{foreach from=$v.values item=l key=k}
            {capture "value"}{$l.value_start}{if $l.value_end} - {$l.value_end}{/if}{/capture}
			<tr>
				<td class="legend-cell">{if $k==0}{$v.trait.name}{/if}</td>
                <td>{if $v.values|@count>1}&#149; {/if}{$smarty.capture.value}</td>
			</tr>
            {/foreach}
			<tr><td class="last-row" colspan="2"></td></tr>
        {/foreach}
       
        </table>


	</div>
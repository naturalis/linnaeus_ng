<style>
table.exotica {
	width:510px;	
	border-collapse:collapse;
}
table.exotica td {
	padding:1px 0 1px 0;
}
table.exotica td li {
	list-style-position: inside;
}
.legend-cell {
	width:200px;
}
.last-row {
	border-bottom:1px solid #eee;
	padding-bottom:0px;
}
ul.exotica {
	list-style-type: disc;
	list-style-position: outside;
}
ul.exotica li {
	margin-left:12px;
}
</style>
	<div style="margin-bottom:10px">

		<h2 id="name-header">Exotenpaspoort
		<a
        	href="http://www.nederlandsesoorten.nl/content/exotenpaspoort" 
            target="_blank"  
            title="{t}klik voor help over dit onderdeel{/t}" 
            class="help"
            style="float:right"
		>&nbsp;</a>
        </h2>

        {if $content.content.content}
        <div>
            {$content.content.content}
        </div>
        {/if}

		<table class="exotica">
        {foreach from=$content.result.data item=v}
        	{foreach from=$v.values item=l key=k}
            {capture "value"}{$l.value_start}{if $l.value_end} - {$l.value_end}{/if}{/capture}
			<tr>
				<td class="legend-cell">{if $k==0}{$v.trait.name}{/if}</td>
                <td>{if $v.values|@count>1}<li>{/if}{$smarty.capture.value}</li></td>
			</tr>
            {/foreach}
			<tr><td class="last-row" colspan="2"></td></tr>
        {/foreach}
		</table>

		{if $content.result.references}
        <br />
        <h4 class="source">Publicatie{if $content.result.references|@count>1}s{/if}</h4>
		<ul class="exotica">
        {foreach from=$content.result.references item=v}
			<li><a href="../literature2/reference.php?id={$v.id}">{if $v.citation}{$v.citation}{else}{$v.label}{/if}</a></li>
        {/foreach}
		{/if}
        </ul>

	</div>
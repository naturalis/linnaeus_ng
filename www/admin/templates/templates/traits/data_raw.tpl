{include file="../shared/admin-header.tpl"}

<style>
table {
	border-collapse:collapse;
}
td {
	border:1px solid #999;
	padding:0.5px;
}
.no-trait {
	color: red;
}
.no-data {
	opacity:0.5;
}
.irrelevant {
	opacity:0.5;
}
.identified-trait, .identified-trait a {
	color:green;
}
.cell-warning {
	background-color:#0CF;
}
.cell-ok, .taxon {
	background-color:#9F9;
}
.cell-error {
	background-color:#F66;
}
.no-taxon {
	background-color: red;
	color:white;
}
</style>

<div id="page-main">
	<p>
	    <a href="?action=clear">upload another file</a>
	</p>

   <p>
        <table>
        {foreach from=$data.lines item=line}
	        {if $line.has_data || $line.trait.sysname}
            <tr class="{if !$line.has_data}no-data{/if}{if !$line.trait}no-trait{/if}{if $line.trait.sysname==''}irrelevant{/if}">
                <td class="{if $line.trait}identified-trait {/if}">
                {if $line.trait.sysname!=$sysColSpecies && $line.trait.sysname!=$sysColReferences && $line.trait.sysname!=$sysColNsrId}
	                <a href="traitgroup_trait.php?id={$line.trait.id}" target="_trait">{$line.trait.sysname}</a>
                {else}
    	            {$line.trait.sysname}
                {/if}
                </td>

                {foreach from=$line.cells item=v key=k}
                {if $line.trait.sysname==$sysColSpecies}
                    <td class="{if $k>0}{if !$data.taxa[$k]}no-taxon{else}taxon{/if}{/if}" title="{if !$data.taxa[$k] && $k>0}unknown taxon{/if}">
                        {$v}
                    </td>                
                {else}
                    <td class="
                        {if $line.cell_status[$k]}
                            {if $line.cell_status[$k].pass==1}
                                {if $line.cell_status[$k].warning}cell-warning
                                {else}cell-ok
                                {/if}
                            {else}
                                cell-error
                            {/if}
                        {/if}
                        {if !$data.taxa[$k] && $k>0}no-taxon{/if}
                        "
                        title="{if $line.cell_status[$k].warning}{$line.cell_status[$k].warning|@escape} {/if}{if $line.cell_status[$k].error}{$line.cell_status[$k].error|@escape}{/if}">
                        {$v}
                    </td>
				{/if}
                {/foreach}
            </tr>
            {/if}
        {/foreach}
        </table>
	</p>
</div>

{include file="../shared/admin-messages.tpl"}
{include file="../shared/admin-footer.tpl"}
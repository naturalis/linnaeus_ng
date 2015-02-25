{include file="../shared/admin-header.tpl"}

<style>
table {
	border-collapse:collapse;
}
td {
	border:1px solid #999;
	padding:0.5px;
	font-size:0.9em;
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
.cell-warning  {
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
div.legend {
    max-width:250px;
	white-space:nowrap;
    overflow:hidden;
}
div.data {
	width:150px;
    width:150px;
    overflow:hidden;
	-white-space:nowrap;
}
td.identified-trait {
	min-width:150px;
}

.with-error {
	background-image: url(data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAIAAAACCAYAAABytg0kAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAABZJREFUeNpi2r9//38gYGAEESAAEGAAasgJOgzOKCoAAAAASUVORK5CYII=);
}

</style>

<div id="page-main">
	<p>
    	<input type="button" value="save data" onclick="saveRawData();" />
	</p>
   <p>
        <table>
        {foreach from=$data.lines item=line key=l}
	        {if $line.has_data || $line.trait.sysname}
            <tr class="{if !$line.has_data}no-data{/if}{if !$line.trait}no-trait{/if}{if $line.trait.sysname==''}irrelevant{/if}">

                <td class="{if $line.trait}identified-trait{/if}">
                {if $line.trait.sysname!=$sysColSpecies && $line.trait.sysname!=$sysColReferences && $line.trait.sysname!=$sysColNsrId}
	                <a href="traitgroup_trait.php?id={$line.trait.id}" target="_trait">{$line.trait.sysname}</a>
                {else}
    	            {$line.trait.sysname}
                {/if}
                
				{if $line.trait.id==$prevtrait && $line.trait.can_have_range}
				<br /><label style="font-size:0.9em"><input class="joinrows" name="joinrows[]" value="[{$prevrow},{$l}]" type="checkbox" />range with prev. row</label>
                {/if}
                </td>

                {foreach from=$line.cells item=v key=k}
                {if $k==0}{assign var=currValue value=$v}{/if}
                {if $line.trait.sysname==$sysColSpecies || $line.trait.sysname==$sysColNsrId}
                    <td 
                    	row-id="{$l}"
                        col-id="{$k}"
                    	class="{if $k>0}{if !$data.taxa[$k].have_taxon}no-taxon{else if !$data.taxa[$k].match}cell-warning{else}taxon{/if}{/if}"
                    	title="{if $k>0}{if !$data.taxa[$k].have_taxon}unknown taxon{else if !$data.taxa[$k].match}taxon name and ID do not match; using {$data.taxa[$k].will_use} (from {$data.taxa[$k].will_use_source}){/if}{/if}">
                        {$v}
                    </td>                
                {else}
                    <td
                    	row-id="{$l}"
                        col-id="{$k}"
						{if $k>0}
						data-pass="{$line.cell_status[$k].pass}"
                        data-trait="{$line.trait.id}"
                        data-trait-has-values="{$line.trait.values|@count>0}"
                        data-value="{$line.cell_status[$k].value_id}"
						data-taxon="{$data.taxa[$k].will_use_id}"
                        {/if}
						class="{if $line.cell_status[$k]}{if $line.cell_status[$k].pass==1}{if $line.cell_status[$k].warning}cell-warning{else}cell-ok{/if}{else}cell-error{/if}{/if}{if !$data.taxa[$k] && $k>0}no-taxon{/if}"
                        title=
"{if $data.taxa[$k].will_use}taxon: {$data.taxa[$k].will_use}
{/if}
value: {$line.trait.sysname}: {$currValue}
{if $line.cell_status[$k].warning}warning: {$line.cell_status[$k].warning|@escape} {/if}{if $line.cell_status[$k].error}
error: {$line.cell_status[$k].error|@escape}{/if}">
                        <div class="data-container {if $k==0}legend{else}data{/if}">
                        {$v|@utf8_encode} 
                        </div>
                    </td>
				{/if}
                {/foreach}
            </tr>
            {assign var=prevtrait value=$line.trait.id}
            {assign var=prevrow value=$l}
            {/if}
        {/foreach}
        </table>
	</p>

	<p>
    	<input type="button" value="save data" onclick="saveRawData();" />
	</p>
	<p>
    	data not looking right?<br />
	    <a href="?action=rotate">rotate sheet</a><br />
	    <a href="?action=clear">upload another file</a>
	</p>
    
</div>

{include file="../shared/admin-messages.tpl"}
{include file="../shared/admin-footer.tpl"}
{include file="../shared/admin-header.tpl"}
<script>

function saveRawData()
{	
	var form=$('<form method="post"></form>').appendTo('body');
	form.append('<input type="hidden" name="action" value="save" />');
	form.submit();
}

</script>
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
</style>

<div id="page-main">
	<p>
    	<input type="button" value="save data" onclick="saveRawData();" />
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
                {if $k==0}{assign var=currValue value=$v}{/if}
                {if $line.trait.sysname==$sysColSpecies || $line.trait.sysname==$sysColNsrId}
                    <td class="{if $k>0}{if !$data.taxa[$k].have_taxon}no-taxon{else if !$data.taxa[$k].match}cell-warning{else}taxon{/if}{/if}" \
                    	title="{if $k>0}{if !$data.taxa[$k].have_taxon}unknown taxon{else if !$data.taxa[$k].match}taxon name and ID do not match; using {$data.taxa[$k].will_use} (from {$data.taxa[$k].will_use_source}){/if}{/if}">
                        {$v}
                    </td>                
                {else}
                    <td
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
            {/if}
        {/foreach}
        </table>
	</p>
</div>

{include file="../shared/admin-messages.tpl"}
{include file="../shared/admin-footer.tpl"}
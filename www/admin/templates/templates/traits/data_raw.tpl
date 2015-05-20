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
.valid-ref {
	color:#390;
}
.invalid-ref {
	color:#F44;
}
</style>

<script>
function doLitRefList( expunge )
{
	if ( expunge )
	{
		$('<form method=post></form>').append ( '<input type=hidden name=action value=clear_ref_codes>' ).appendTo('body').submit();
	}
	else
	{
		litRefDialog();
	}
}
function litRefDialog()
{
	prettyDialog({
		height: 400,
		title: _( "Upload reference # / literature ID data" ),
		content : " \
			<div style=\"margin:5px 0 5px 0;font-size:0.8em\"><p> \
			" + _( "Upload a file with databases ID's matched to your own reference numbers as they appear in your data sheet:" ) +" \
			</p><form method='post' enctype='multipart/form-data'> \
				<input type=hidden name=action value=ref_codes> \
				<input name=file type=file><p> \
				" + _( "Format needs to be plain text, containing two columns. Column order can be ID,ref# or vice versa." ) +" \
				" + _( "Values can be separated by TAB, comma, semi-colon or space(s)." ) +" \
				<br /> \
				" + _( "You can also paste the information into the area below, using the same format." ) +" \
				</p> \
				<textarea name=lines></textarea><p> \
				<input type=submit value=upload> \
				</p> \
				</div> \
			</form>"
	});
}
</script>    
    
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
                {else if $line.trait.sysname==$sysColReferences}
                    <td>
                    
{if $data.references[$k]}
{foreach $data.references[$k].valid v vk}<span class="valid-ref" title="{$v.label|@escape}">{$vk} &#9654; {$v.id}</span><br />{/foreach}
{foreach $data.references[$k].invalid v vk}<span class="invalid-ref" title="{t}unresolved reference{/t}">{$v} &#9654; ?</span><br />{/foreach}
{/if}

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
		<a href="#" onclick="doLitRefList();return false;">{t}upload a file with reference # / literature ID data{/t}</a>
        {if $reflist}
		(<a href="#" onclick="doLitRefList(true);return false;">{t}clear last upload{/t}</a>)
        {else}
        <br />
        <span class="comment">
        you can match your reference #'s to literature ID's
        <a href="../literature2/bulk_upload.php">by uploading your literature references and matching them to existing database entries</a>.<br />
        if you do this now, and return here during the same session, your uploaded trait data printed above will still be available.
        </span>
        {/if}
	</p>

	<p>
    	<input type="button" value="save data" onclick="saveRawData();" />
	</p>

	<p>
    	data not looking right?<br />
	    <a href="?action=rotate">rotate sheet</a><br />
	    <a href="?action=clear">upload a different file</a>
	</p>
    
</div>

{include file="../shared/admin-messages.tpl"}
{include file="../shared/admin-footer.tpl"}
{include file="../shared/admin-header.tpl"}


<h1>USE DD-MM-YYYY for dates!!</h1>


{function colselect}
<select name="fields[{$data}]">
<option value="">ignore</option>
{foreach from=$cols item=col key=c}
{if $col.sys_label|@strlen==0}
<option value="" disabled="disabled"></option>
{else}
<option value="{$col.sys_label}" {if $fields[{$data}]==$col.sys_label}selected="selected"{/if}>{$col.sys_label}</option>
{/if}
{/foreach}
</select>
{/function}

<form method="post" id="theForm">

<div id="page-main" class="literature-match">

	<div style="border-bottom:1px dotted #666;padding-bottom:10px;margin-bottom:20px;">
        <input type="hidden" name="action" value="" id="action" />
        <input type="hidden" name="value" value="" id="value" />
        <span class="raw" onclick="$('.raw').toggle();" style="display:none">show raw data</span>
        <span class="raw">
            <span onclick="$('.raw').toggle();">raw referece data (TAB separated, like copy/pasted excel cells):</span>
            <textarea name="raw" style="width:100%;height:200px;font-size:0.8em;overflow:scroll">{$raw}</textarea>
            <p>
            <label><input type="checkbox" value="1" name="ignorefirst" {if $ignorefirst} checked="checked"{/if}/>first line has titles</label>
            </p>
            <p>
            <input type="submit" value="parse" />
            </p>
	    </span>
    </div>


    {if $lines}
    <script>
	$('.raw').toggle();
	</script>
	<div style="border-bottom:1px dotted #666;padding-bottom:10px;margin-bottom:20px;">
        <span class="lines" onclick="$('.lines').toggle();" style="display:none">show lines</span>
        <span class="lines">
	        <span onclick="$('.lines').toggle();">found {$lines|@count} lines (showing 5).</span>
            <p>
            select appropriate fields per column; mutiple columns of the same field will be concatenated.
            </p>
            
            <table>
            {foreach from=$lines item=line key=k}{if $k<5}
                {if $k==0}
				{if $firstline}
                <tr class="admin-row">
                    {foreach from=$firstline item=cell key=c}
                    <td style="font-weight:bold"> {$cell}</td>
                    {/foreach}
                </tr>
				{/if}
                <tr class="admin-row">
                    {foreach from=$line item=cell key=c}
                    <td>{colselect data=$c}</td>
                    {/foreach}
                </tr>       
                {/if}
				{if !$ignorefirst || ($ignorefirst && $k>0)}
                <tr>
                    {foreach from=$line item=cell key=c}
                    <td>{$cell}</td>
                    {/foreach}
                </tr>
                {/if}
            {/if}{/foreach}
            </table>
            <input type="submit" value="process" />
        </span>
    </div>

	{/if}

</div>

{include file="../shared/admin-messages.tpl"}

{if $matches}
    <script>
	$('.lines').toggle();
	</script>

<div class="page-generic-div literature-match">

	<div style="padding-bottom:10px;margin-bottom:20px;">

        <p>
        matches:
        </p>
<style>
table {
}
th, td {
	white-space:nowrap;
	max-width:200px;
	overflow:hidden;
	text-overflow: ellipsis;
	border:0px;
}
th {
	font-weight:bold;
	text-align:left;
	border-bottom:1px dotted #666;
}
.meta-data-ok {
	color:green;
}
.meta-data-error {
	color:red;
}
.meta-data-warning {
	color:#F60;
}
.meta-data-unassigned {
	color:#999;
}
.row-wont-save {
	text-decoration:line-through;
}
.meta-data-format-error {
	background-color:#FF6;
	text-decoration:line-through;
}

</style>

		<table class="image-meta-data">
        {foreach $lines ls lsk}
			{assign var=wontsaverow value=false}
	        <tr>
            {foreach $ls l lk}
            	{if $lsk==0 && $ignorefirst}
            	<th>{$l}</th>
                {else}

                {assign var=class value=""}
                {assign var=message value=""}

                {if $lk==$col_NSR_ID && !$matches.taxa[$lsk].taxon_id}
					{assign var=class value="meta-data-error"}
					{assign var=message value="could not resolve NSR ID; line will not be saved"}
                    {assign var=wontsaverow value=true}
                {elseif $lk==$col_NSR_ID}
					{assign var=class value="meta-data-ok"}
					{assign var=message value=$matches.taxa[$lsk].taxon}
                {elseif $lk==$col_file_name && !$matches.files[$lsk].exists}
					{assign var=class value="meta-data-warning"}
					{assign var=message value="image not found at\n%s\nline will be saved nonetheless. be sure to upload the image later!"}
                {elseif $lk==$col_file_name}
					{assign var=class value="meta-data-ok"}
					{assign var=message value="image present at\n%s"}
                {elseif $fields[$lk]==""}
					{assign var=class value="meta-data-unassigned"}
					{assign var=message value="field will not be stored as meta-data"}
                {else}
					{assign var=class value=""}
					{assign var=message value="field will be stored as `$fields[$lk]`"}
                {/if}
                
                {if $wontsaverow && $lk!=$col_NSR_ID} 
					{assign var=class value="`$class` row-wont-save"}
                {/if}
                
                {if !$checks[$lsk][$lk]|@is_null && $checks[$lsk][$lk].pass==false}
					{assign var=class value="`$class` meta-data-format-error"}
                    {assign var=message value="format error; field will not be saved"}
                {/if }
                
            	<td class="{$class}" title="{$message|@sprintf:$matches.files[$lsk].url}">
                	{$l}
				</td>
				{/if}
            {/foreach}
            </tr>
        {/foreach}
        </table>

        <p>
	        <input type="button" value="save" onclick="$('#theForm').attr('action','image_meta_bulk_save.php');$('#theForm').submit();" />
        </p>

	</div>

</div>

{/if}

</form>

{include file="../shared/admin-footer.tpl"}

{include file="../shared/admin-header.tpl"}

{function colselect}
<select name="fields[{$data}]">
<option value="">ignore</option>
{foreach from=$cols item=col key=c}
<option value="{$c}" {if $fields[{$data}]==$c}selected="selected"{/if}>{$col}</option>
{/foreach}
</select>
{/function}
        
<style>
table {
	border-collapse:collapse;
}

td {
	font-size:0.8em;
	border:1px dotted #666;
	padding-left:1px;
}
.admin-row {
	background-color:#eee;
}
.small-link
{
	font-size:0.8em;
}
</style>

<div id="page-main">
<form method=post id=theForm>

    <input type="hidden" name="action" value="" id="action" />
    <input type="hidden" name="value" value="" id="value" />
    <span onclick="$('#raw').toggle();">enter data</span> <span id="raw"> (TAB separated, like copy/pasted excel cells)
    <textarea name="raw" style="width:100%;height:200px;font-size:0.8em;overflow:scroll">{$raw}</textarea>
    </span>
    <p>
        <input type="submit" value="parse" />
        <label><input type="checkbox" value="1" name="ignorefirst" {if $ignorefirst} checked="checked"{/if}/>first line has titles</label>
    </p>


    {if $lines}
	<span onclick="$('#lines').toggle();">found {$lines|@count} lines (showing 5).</span>
    <span id="lines">
    select appropriate fields per column; mutiple columns of the same field will be concatenated.
    choose 'ignore' to skip a column, or click 'remove column' to ignore and hide it from view.
    <table>
    {foreach from=$lines item=line key=k}{if $k<5}
        {if $k==0}
        <tr class="admin-row">
            {foreach from=$line item=cell key=c}{if $delcols[$c]!==true}
            <td>
            	{colselect data=$c}
            </td>
            {/if}{/foreach}
        </tr>
        <tr class="admin-row">
            {foreach from=$line item=cell key=c}{if $delcols[$c]!==true}
            <td {if $emptycols[$c]==true} title="empty column"{/if}>
                <a href="#" onclick="$('#action').val('delcol');$('#value').val({$c});$('#theForm').submit();return false;">remove column</a>
                {if $emptycols[$c]==true}<span title="empty column"> * </span>{/if}
            </td>
	        {/if}{/foreach}
        </tr>        
        {/if}
    
        <tr>
            {foreach from=$line item=cell key=c}
	        {if $delcols[$c]!==true}
            <td>{$cell}</td>
            {/if}
            {/foreach}
        </tr>
    {/if}{/foreach}
    </table>
    <a class="small-link" href="#" onclick="$('#action').val('delcolreset');$('#theForm').submit();return false;">reset removed columns</a>
    </span>
    <p>
        <input type="submit" value="process" />
        <span style="margin-left:10px">
        	threshold match percentage: <input type="text" value="{$threshold}" name="threshold" style="width:25px;text-align:right" />%
		</span>
    </p>
	{/if}
</form>
</div>

{include file="../shared/admin-messages.tpl"}

{if $matches}
<div class="page-generic-div">
matches:
{foreach from=$lines item=line key=k}{if !$ignorefirst || ($ignorefirst && $k>0)}
<p>
	{$k}. <a href="#" onclick="$('#matches{$k}').toggle();return false;">{$line[$field_author]}, <i>{$line[$field_label]}</i> ({$line[$field_date]})</a>
    
    {assign var=m value=$matches[$k]}

    <p id="matches{$k}" style="_display:none">
    {if $m.labels|@count>0}
    {foreach from=$m.labels item=match}
        <label>
            <input type="radio"  data-id="{$k}" name="match[{$k}]" value="{$match.id}" />
            {$match.match.label|@round}%: {$match.names.label} ({$match.date})
			/


            {if $match.authors|@count==0}(no authors){/if}
            {foreach from=$match.authors item=auth}{$auth.name}{/foreach}
            
        </label>
        <br />
    {/foreach}
        <label>
            <input type="radio" name="match[{$k}]" data-id="{$k}" value="" checked="checked" />
                none of the above;
        </label>
        <label>
                create as new? <input type="checkbox" data-id="{$k}" name="new[{$k}]" />
        </label>
    {else}
        <label>
            no matches; create as new? <input type="checkbox" name="new[{$k}]" />
        </label>
    {/if}
	</p>
</p>
{/if}{/foreach}

</div>

{/if}

<script>
$(document).ready(function()
{
	$('input[type=radio]').each(function()
	{
		$(this).on('change',function()
		{
//			$('input[type=checkbox][data-id='+$(this).attr('data-id')+']').attr('checked','');
		})
	});
});
</script>




    
    <pre>
    next steps:
    - joing columns, ask:
    	- order of join
        - joining character
        - post- and prefixing characters
	- ask for langauges! (ANY, not just project sp.)
    </pre>

{include file="../shared/admin-footer.tpl"}

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

	<div style="border-bottom:1px dotted #666;padding-bottom:10px;margin-bottom:20px;">
        <input type="hidden" name="action" value="" id="action" />
        <input type="hidden" name="value" value="" id="value" />
        <span onclick="$('#raw').toggle();">enter data:</span> <span id="raw"> (TAB separated, like copy/pasted excel cells)
        <textarea name="raw" style="width:100%;height:200px;font-size:0.8em;overflow:scroll">{$raw}</textarea>
        <br />
        <input type="submit" value="parse" />
        <label><input type="checkbox" value="1" name="ignorefirst" {if $ignorefirst} checked="checked"{/if}/>first line has titles</label>
	    </span>
    </div>


    {if $lines}
	<div style="border-bottom:1px dotted #666;padding-bottom:10px;margin-bottom:20px;">
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
                        <a href="#" onclick="$('#action').val('delcol');$('#value').val({$c});$('#theForm').submit();return false;">
                            remove column
                        </a>
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
            <a class="small-link" href="#" onclick="$('#action').val('delcolreset');$('#theForm').submit();return false;">
                reset removed columns
            </a>
			<br />
            <input type="submit" value="process" />
            <span style="margin-left:10px">
            threshold match percentage:
            <input type="text" value="{$threshold}" name="threshold" style="width:25px;text-align:right" />%
            </span>

        </span>
    </div>
	{/if}

</form>
</div>

{include file="../shared/admin-messages.tpl"}

{if $matches}
<div class="page-generic-div">

	<div style="border-bottom:1px dotted #666;padding-bottom:10px;margin-bottom:20px;">

        <p>
            below are the lines from your bulk import (<span style="color:#039">in blue</span>) plus, for each line, possible matches
            with references that are already in the database, ranked by matching percentage.<br/>
            please review the matches. if you judge an existing reference to be a match, check the radiobutton preceding it. if there
            is no match, check the radiobutton labeled "none of the above". if you wish the reference to be created as a new entry in
            the database, check the checkbox labeled "create as new?".<br />
            when done, press "save" to save new entries. you will also be offered a download of your original data with an extra column
            containing the ID's of the matching, resp. saved database entries.<br />
            <b>when saving new entries, make sure you have assigned <i>all</i> possible appropriate fields to the corresponding
            columns</b>
        </p>

        {foreach from=$lines item=line key=k}{if !$ignorefirst || ($ignorefirst && $k>0)}
        <p>
            {$k}. <span style="color:#039">{$line[$field_author]}, <i>{$line[$field_label]}</i> ({$line[$field_date]})</span>
			<!-- a href="#" onclick="$('#matches{$k}').toggle();return false;">{$line[$field_author]},
            <i>{$line[$field_label]}</i> ({$line[$field_date]})</a -->
    
		    {assign var=m value=$matches[$k]}

		    <p id="matches{$k}" style="_display:none;border-bottom:1px dotted #666;padding-bottom:5px;margin-top:0">
                {if $m.labels|@count>0}
                {foreach from=$m.labels item=match}
                <label>
                    <input type="radio"  data-id="{$k}" name="match[{$k}]" value="{$match.id}" />
                    {$match.match.label|@round}%: 
                    {if $match.authors|@count==0}{if $match.authors_literal}{$match.authors_literal}{else}(no authors){/if}{else}{foreach from=$match.authors item=auth key=a}{if $a>0}, {/if}{$auth.name}{/foreach}{/if}, 
                    <i>{$match.names.label}</i> ({$match.date})
                </label>
    
                    <a href="edit.php?id={$match.id}" target="_referentie">&rarr;</a><br />
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

</div>

{include file="../shared/admin-messages.tpl"}

<div class="page-generic-div">

</div>

{/if}



















<script>
$(document).ready(function()
{
	$('input[type=radio]').each(function()
	{
		$(this).on('change',function()
		{
			var a=$(this).attr('data-id');
			if ($(this).val().length>0)
			{
				$('input[type=checkbox][data-id='+a+']').prop('checked',false);
				$('input[type=checkbox][data-id='+a+']').prop('disabled',true);
			}
			else
			{
				$('input[type=checkbox][data-id='+a+']').prop('disabled',false);
			}
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

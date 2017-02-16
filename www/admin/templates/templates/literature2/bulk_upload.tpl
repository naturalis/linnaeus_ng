{include file="../shared/admin-header.tpl"}

{assign var=suppressEmptyColumns value=true}

{function colselect}
<select name="fields[{$data}]">
<option value="">ignore</option>
{foreach from=$cols item=col key=c}
{if $col|@strlen==0}
<option value="" disabled="disabled"></option>
{else}
<option value="{$c}" {if $fields[{$data}]==$c}selected="selected"{/if}>{$col}</option>
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
            <span onclick="$('.raw').toggle();">
            	raw referece data (TAB separated, like copy/pasted excel cells; be sure to use <a href="publication_types.php">legal publication types</a> - use the system labels, not the translations):
            </span>
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
            choose 'ignore' to skip a column, or click 'remove column' to ignore and hide it from view.
            optionally, use "reference #" to indicate the column in your data with an ID that you need
            to match with.
            </p>

            <table>
            {foreach from=$lines item=line key=k}{if $k<5}
                {if $k==0}
				{if $firstline}
                <tr class="admin-row">
                    {foreach from=$firstline item=cell key=c}
                    {if $delcols[$c]!==true}{if $suppressEmptyColumns && $emptycols[$c]!=true}
                    <td style="font-weight:bold"> {$cell}</td>
                    {/if}{/if}
                    {/foreach}
                </tr>
				{/if}
                <tr class="admin-row">
                    {foreach from=$line item=cell key=c}
                    {if $delcols[$c]!==true}{if $suppressEmptyColumns && $emptycols[$c]!=true}
                    <td>{colselect data=$c}</td>
                    {/if}{/if}
                    {/foreach}
                </tr>
                <tr class="admin-row">
                    {foreach from=$line item=cell key=c}{if $delcols[$c]!==true}
					{if $suppressEmptyColumns && $emptycols[$c]!=true}
                    <td {if $emptycols[$c]==true} title="empty column"{/if}>
                    <a href="#" onclick="$('#action').val('delcol');$('#value').val({$c});$('#theForm').submit();return false;">remove column</a>
                    {if $emptycols[$c]==true}<span title="empty column"> * </span>{/if}
                    </td>
					{/if}
                    {/if}
                    {/foreach}
                </tr>
                {/if}
				{if !$ignorefirst || ($ignorefirst && $k>0)}
                <tr>
                    {foreach from=$line item=cell key=c}
					{if $suppressEmptyColumns && $emptycols[$c]!=true}
                    {if $delcols[$c]!==true}
                    <td>{$cell}</td>
                    {/if}
                    {/if}
                    {/foreach}
                </tr>
                {/if}
            {/if}{/foreach}
            </table>
            <a class="small-link" href="#" onclick="$('#action').val('delcolreset');$('#theForm').submit();return false;">reset removed columns</a><br />
            <input type="submit" value="process" />
            <span style="margin-left:10px">
                threshold match percentage:
                <input type="text" value="{$threshold}" name="threshold" style="width:25px;text-align:right" />%
            </span>
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

		{assign var=i value=1}
		{assign var=linecount value=$lines|@count-($ignorefirst)}

        {foreach from=$lines item=line key=k}{if !$ignorefirst || ($ignorefirst && $k>0)}
        <div class="match" data-id="{$k}" style="{if $i>1}display:none;{/if}border-bottom:1px dotted #666;padding-bottom:5px;margin-top:0">

        <script>
		storeElement( {$k} );
		</script>

	        {$i}/{$linecount}
        	<table class="matches">
            	<tr>
                	<td style="width:60px"></td>
                    <td>{$line[$field_author]}</td>
                    <td><i>{$line[$field_label]}</i></td>
                    <td>{$line[$field_date]}</td>
                    <td></td>
				</tr>

		    {assign var=m value=$matches[$k]}
		    {assign var=found_full_match value=false}

            {if $m.labels|@count>0}
            {foreach from=$m.labels item=match}
            	<tr>
                	<td>
		                <label>
	                        {if $match.match.label|@round==100 && $match.match.label|@round!= $match.match.label}&asymp;{/if}
       	        		    {$match.match.label|@round}%
        		            <input
                            	type="radio"
                                data-id="{$k}"
                                name="match_ref[{$k}]"
                                value="{$match.id}"
                                {if $match.match.label==100} checked="checked"{assign var=found_full_match value=true}{/if}
							/>
						</label>
					</td>
                    <td>
                        {if $match.authors|@count==0}{if $match.authors_literal}{$match.authors_literal}{else}(no authors){/if}{else}{foreach from=$match.authors item=auth key=a}{if $a>0}, {/if}{$auth.name}{/foreach}{/if}
					</td>
                    <td><i>{$match.names.label}</i></td>
                    <td>{$match.date}</td>
                    <td><a href="edit.php?id={$match.id}" target="_referentie">&rarr;</a></td>
				</tr>
			{/foreach}
                <tr>
                    <td>
                        <input type="radio" id="none-{$k}" name="match_ref[{$k}]" data-id="{$k}" value=""{if !$found_full_match} checked="checked"{/if} />
                    </td>
                    <td colspan="4" style="text-align:left">
                    <label for="none-{$k}">
                        none of the above;
                    </label>
                    <label>
                        create as new? <input type="checkbox" data-id="{$k}" name="new_ref[{$k}]" onchange="$('#pub-type-warning-{$k}').toggle($(this).prop('checked'));" />
                    </label>
                    </td>
                </tr>
			{else}
            	<tr>
                	<td></td>
                    <td colspan="4" style="text-align:left">
                        <label>no matches; create as new? <input type="checkbox" name="new_ref[{$k}]" onchange="$('#pub-type-warning-{$k}').toggle($(this).prop('checked'));" /></label>
					</td>
				</tr>
            {/if}


			{if $matching_publication_types[$k]==""}
            	<tr style="display:none" id="pub-type-warning-{$k}">
                	<td></td>
                    <td colspan="4" style="text-align:left">
                    	<span style="color:red">unknown publication type "{$line[$field_publication_type]}" will not be saved ('publication_type_id' will be set to null).</span>
					</td>
				</tr>
			{/if}

			</table>

            {if $i>1}
            <a href="#" class="prev" title="click or use left arrow for previous" onclick="
            	$(this).parent().prev().fadeToggle();
            	$(this).parent().toggle();
                return false;
			">previous</a>
            {/if}
            {if $i>1 && $i<$linecount}
            |
            {/if}
            {if $i++<$linecount}
            <a href="#" class="next" title="click or use right arrow for previous" onclick="
            	$(this).parent().next().fadeToggle();
            	$(this).parent().toggle();
                return false;
			">next</a><br />
            {/if}
		</div>
		{/if}

        {/foreach}

        <p>
	        <input type="button" value="process matches" onclick="processMatches();" />
        </p>

	</div>

</div>

{/if}


</form>

<script>
$(document).ready(function()
{
	$('input[type=radio]').each(function()
	{
		$(this).on('change',function()
		{
			if ($(this).prop('checked'))
			{
				var a=$(this).attr('data-id');
				var state=($(this).val().length>0);
				$('input[type=checkbox][data-id="'+a+'"]').prop('disabled',state);
				if (state)
				{
					$('input[type=checkbox][data-id="'+a+'"]').prop('checked',false);
				}
			}

		})
		$(this).trigger('change');
	});

	$('.match').on('show', function() { markElementAsSeen($(this).attr('data-id')); } );
	markElementAsSeen( '1' );

	$(document).keydown(function(e)
	{
		switch(e.which) {
			case 37: // left
				$('.prev:visible').trigger('click');
				break;
			case 39: // right
				$('.next:visible').trigger('click');
				break;
			default: return;
		}
		e.preventDefault();
	});

});
</script>

{include file="../shared/admin-footer.tpl"}

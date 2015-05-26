{include file="../shared/admin-header.tpl"}
<script>
var columnexamples=[];
var values=[];
{foreach from=$duplicate_columns item=v key=k}
	{foreach from=$v.columns item=f}
	values.push('{$v.example[$f]}');
	{/foreach}
	columnexamples.push( { column:'{$k}', values: values } );
{/foreach}

function makeJoinExample(col)
{
	for(var i=0;i<columnexamples.length;i++)
	{
		if (columnexamples[i].column==col)
		{
			var val=columnexamples[i].values;
		}
	}
	$('#example-'+col).html(
		$('#lpad-'+col).val()+
		values.join($('#infix-'+col).val())+
		$('#rpad-'+col).val()
	);
}

</script>

<form method="post" id="theForm">
<input type="hidden" name="rnd" value="{$rnd}" />

<div id="page-main" class="literature-match">

    {assign var=i value=1}
    {assign var=newcount value=$new_ref|@count}

    <p>
		{if $newcount==0}
		no new references to be stored.<br />
        click 'next' to download matched ID's.
		{else}
		new references, to be stored:<br />
        (do not worry about multiple "create as new"'s for the same name; they will resolve to one and the same.)
        {/if}
    </p>

	<div style="padding-bottom:10px;margin-bottom:20px;">
 


        {foreach from=$new_ref item=line key=k}{if !$ignorefirst || ($ignorefirst && $k>0)}
        <div class="match" data-id="{$k}" style="{if $i>1}display:none;{/if}border-bottom:1px dotted #666;padding-bottom:5px;margin-top:0">

		<script>
        storeElement( {$k} );
        </script>

        	{assign var=line value=$lines[{$k}]}

			{$i}/{$newcount}	
        	<table class="processed">
            	<tr>
                    <td colspan="2">
						<span id="title-{$k}">{$line[$field_author]}, <i>{$line[$field_label]}</i> ({$line[$field_date]})</span>
					</td>
                    <td style="width:10px;padding-left:6px">
						<input 
                        	type="checkbox" 
                            name="kill[]"
                            value="{$k}" 
                            title="i've changed my mind, don't save!" 
                            onchange="
                            if ($(this).is(':checked'))
                            {
	                            $('#title-{$k}').css('text-decoration','line-through');
							}
                            else
                            {
	                            $('#title-{$k}').css('text-decoration','none');
                            }"
						 />
					</td>
				</tr>

            	<tr>
                	<td style="width:100px">
                    	authors:
                    </td>
                    <td colspan="2">
                        <table class="inner-matches">
                            {foreach from=$matching_authors[$k][$field_author] item=author key=kk}
                            <tr>
                                <td>{$author.name}</td>
                                <td>
								{assign var=found_full_match value=false}
                                {foreach from=$author.suggestions item=suggestion key=kkk}
                                	<label>
                                        <input 
                                        	type="radio" 
                                            name="author[{$k}][{$field_author}][{$kk}]" 
                                            value="{$suggestion.id}" 
                                            data-value="{$suggestion.match.name}"
                                            data-id="{$k}.{$kk}"
			                                {if $suggestion.match.name==100} checked="checked"{assign var=found_full_match value=true}{/if}
                                       	 />
                                        {$suggestion.names.name} ({$suggestion.match.name|@round:2}%)
									</label>
                                    <a href="../actors/edit.php?id={$suggestion.id}" target="_actor">&rarr;</a>
                                    <br />
                                {/foreach}
                                <label>
                                    {if $author.suggestions|@count==0}
                                    &nbsp;no match; create as new?
                                    {else}
                                    <input
                                    	type="radio" 
                                        name="author[{$k}][{$field_author}][{$kk}]" 
                                        value=""
                                        data-id="{$k}.{$kk}"
                                        {if !$found_full_match} checked="checked"{/if} 
                                    />
                                    none of the above;</label><label>create as new?
                                    {/if}
                                     <input 
                                     	type="checkbox" 
                                        name="new_author[{$k}][{$field_author}][{$kk}]"
                                        data-id="{$k}.{$kk}"
                                        {if !$found_full_match} checked="checked"{/if} 
                                     />
                                </label>
                                </td>
                            </tr>
                            {/foreach}
                        </table>
					</td>
				</tr>

            	<tr>
                	<td style="width:100px">
                    	language:
                    </td>
                    <td colspan="2">
                    	<select name="language[{$k}]">
                        {foreach from=$languages item=v key=k}{if $v.id!=$smarty.const.LANGUAGE_ID_SCIENTIFIC}
						{if $v.sort_criterium==0 && $languages[$k-1].sort_criterium!=0}<option value="" disabled="disabled"></option>{/if}
                        <option value="{$v.id}"{if $v.id==$default_language} selected="selected"{/if}>{$v.label}</option>
                        {/if}{/foreach}
                        </select>
					</td>
				</tr>
                
				{if $line[$field_publishedin]}
                <tr>
                    <td style="width:100px">
                        published in:
                    </td>
                    <td colspan="2">
                        <table class="inner-matches">
                            <tr>
                                <td>{$line[$field_publishedin]}</td>
                                <td>
                                {assign var=found_full_match value=false}
                                {foreach from=$matching_publishedin[$k][$field_publishedin] item=publication key=kk}
                                    <label>
                                        <input 
                                        	type="radio" 
                                        	name="publishedin[{$k}][{$field_publishedin}][{$kk}]" 
                                            value="{$publication.id}"
                                             data-id="{$k}.{$kk}pub"
                                             {if $publication.match.label==100} checked="checked"{assign var=found_full_match value=true}{/if}
                                            />
                                        {$publication.names.label}{if $publication.date}, {$publication.date}{/if} ({$publication.match.label|@round:2}%)
                                    </label>
                                    <a href="edit.php?id={$publication.id}" target="_referentie">&rarr;</a>
                                    <br />
                                {/foreach}
                                <label>
                                    
                                    {if $matching_publishedin[$k][$field_publishedin]|@count==0}
                                    &nbsp;no match; create as new?
                                    {else}
                                    <input 
                                    	type="radio" 
                                        name="publishedin[{$k}][{$field_publishedin}][{$kk}]" 
                                        value=""
										data-id="{$k}.{$kk}pub"
                                        {if !$found_full_match} checked="checked"{/if} 
									/>
                                    none of the above; create as new?
                                    {/if}
                                    <input
                                    	type="checkbox" 
                                        name="new_publishedin[{$k}][{$field_publishedin}][{$kk}]"
										data-id="{$k}.{$kk}pub"
                                        {if !$found_full_match} checked="checked"{/if} 
									/>
                                </label>
                                <select name="new_publishedin_language[{$k}][{$field_publishedin}][{$kk}]">
                                    <option value="">-</option>
                                    {foreach from=$languages item=v key=k}{if $v.id!=$smarty.const.LANGUAGE_ID_SCIENTIFIC}
                                    {if $v.sort_criterium==0 && $languages[$k-1].sort_criterium!=0}<option value="" disabled="disabled"></option>{/if}
                                    <option value="{$v.id}"{if $v.id==$default_language} selected="selected"{/if}>{$v.label}</option>
                                    {/if}{/foreach}
                                </select>
                                </td>
                            </tr>
                        </table>
                    </td>
				</tr>
                {/if}

				{if $line[$field_periodical]}
                <tr>
                    <td style="width:100px">
                        periodical:
                    </td>
                    <td colspan="2">
                        <table class="inner-matches">
                            <tr>
                                <td>{$line[$field_periodical]}</td>
                                <td>
                                {foreach from=$matching_periodical[$k][$field_periodical] item=publication key=kk}
                                    <label>
                                        <input
                                        	type="radio" 
                                            name="periodical[{$k}][{$field_periodical}][{$kk}]" 
                                            value="{$publication.id}"
                                            data-id="{$k}.{$kk}per"
                                            {if $publication.match.label==100} checked="checked"{assign var=found_full_match value=true}{/if}
                                         />
                                        {$publication.names.label}{if $publication.date}, {$publication.date}{/if} ({$publication.match.label|@round:2}%)
                                    </label>
                                    <a href="edit.php?id={$publication.id}" target="_referentie">&rarr;</a>
                                    <br />
                                {/foreach}
                                <label>
                                    {if $matching_periodical[$k][$field_periodical]|@count==0}
                                    &nbsp;no match; create as new?
                                    {else}
                                    <input
                                    	type="radio" 
                                        name="periodical[{$k}][{$field_periodical}][{$kk}]" 
                                        value=""
										data-id="{$k}.{$kk}per"
                                        {if !$found_full_match} checked="checked"{/if} 
									/>
                                    none of the above; create as new?
                                    {/if}
                                    <input
                                    	type="checkbox" 
                                        name="new_periodical[{$k}][{$field_periodical}][{$kk}]"
										data-id="{$k}.{$kk}per"
                                        {if !$found_full_match} checked="checked"{/if} 
									/>
                                    <select name="new_periodical_language[{$k}][{$field_publishedin}][{$kk}]">
                                        <option value="">-</option>
                                        {foreach from=$languages item=v key=k}{if $v.id!=$smarty.const.LANGUAGE_ID_SCIENTIFIC}
                                        {if $v.sort_criterium==0 && $languages[$k-1].sort_criterium!=0}<option value="" disabled="disabled"></option>{/if}
                                        <option value="{$v.id}"{if $v.id==$default_language} selected="selected"{/if}>{$v.label}</option>
                                        {/if}{/foreach}
                                    </select>

                                </label>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
                {/if}

			</table>
            
            {if $i>1}
            <a class="prev" href="#" onclick="
            	$(this).parent().prev().fadeToggle();
            	$(this).parent().toggle();
                return false;
			">prev</a>
            {/if}
            {if $i>1 && $i<$newcount}
            |
            {/if}
            {if $i++<$newcount}
            <a class="next" href="#" onclick="
            	$(this).parent().next().fadeToggle();
            	$(this).parent().toggle();
                return false;
			">next</a>
            {/if}

		</div>
        {/if}{/foreach}

        {if $duplicate_columns|@count>0}
        <br />

        <div>
        You have selected mutiple columns for a single field. Please indicate how to join them into a single value.
        {foreach from=$duplicate_columns item=v key=k}
        <p>
            Join columns {foreach from=$v.columns item=f key=c}{if $c>0}, {/if}{$f}{/foreach} for "{$k}":
           
            left padding:	<input class="column-joinage" name="lpad[{$k}]" id="lpad-{$k}" type="text" style="width:10px;" maxlength="2" onkeyup="makeJoinExample('{$k}')" />
            infix:			<input class="column-joinage" name="infix[{$k}]" value=", " id="infix-{$k}" type="text" style="width:10px;" maxlength="2" onkeyup="makeJoinExample('{$k}')" />
            right padding:	<input class="column-joinage" name="rpad[{$k}]" id="rpad-{$k}" type="text" style="width:10px;" maxlength="2" onkeyup="makeJoinExample('{$k}')" />
            example: <span id="example-{$k}"></span>
		</p>
        {/foreach}
		</div>
		{/if}

        <p>
	        <input type="button" value=" {if $newcount==0}next{else}save{/if}" onclick="processMatches();" />
        </p>
        <p>
	        <a href="bulk_upload.php">back</a>
        </p>

	</div>
	

</div>


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

	$(document).keydown(function(e) {
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


	$('.match').on('show', function() { markElementAsSeen($(this).attr('data-id')); } );
	markElementAsSeen( '1' );
	$('.column-joinage').trigger('keyup');
});
</script>

{include file="../shared/admin-footer.tpl"}

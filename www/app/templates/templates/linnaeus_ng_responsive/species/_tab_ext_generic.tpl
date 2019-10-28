	<div style="margin-bottom:10px">

        {if $passport_content.content}
        <div>
            {$passport_content.content}
        </div>
        {/if}
        
        <div>
		<h2 id="name-header">{$ext_name}</h2>

		<table>
        {foreach from=$content->result->data item=v}
        	{foreach from=$v->values item=l key=k}
            {capture "value"}{$l->value_start}{if $l->value_end} - {$l->value_end}{/if}{/capture}
			<tr>
				<td class="legend-cell">{if $k==0}{$v->trait->name}{/if}</td>
                <td>{if $v->values|@count>1}<li>{/if}{$smarty.capture.value}</li></td>
			</tr>
            {/foreach}
			<tr><td class="last-row" colspan="2"></td></tr>
        {/foreach}
		</table>

		{if $content->result->references}
        <br />
        <h4 class="source">{t}Publicatie{if $content->result->references|@count>1}s{/if}{/t}</h4>
		<ul class="exotica">
        
        {foreach from=$content->result->references item=v}
	        {if $content->result->references|@count>1}<li>{/if}
                <a href="../literature2/reference.php?id={$v->id}">
                {capture authors}
                {foreach from=$v->authors item=author key=ak}{if $ak>0}, {/if}{$author->name|@trim}{/foreach}
                {if $ak|@is_null}{$v->author}{/if}
                {/capture}
				{$smarty.capture.authors|@trim}{if $v->date}{if $smarty.capture.authors|@trim|@strlen>0}, {/if}{$v->date}{/if}.
                </a>
                {if $v->label|@trim|@strlen>0}{$v->label|@trim}{if !($v->label|@trim|@substr:-1)|@in_array:array('?','!','.')}. {/if}{/if}
                {if $v->periodical_id}{$v->periodical_ref->label} {elseif $v->periodical}{$v->periodical} {/if}
                {if $v->publishedin_id}{$v->publishedin_ref->label} {elseif $v->publishedin}{$v->publishedin} {/if}
                {if $v->volume}{$v->volume}{/if}{if $v->volume && $v->pages}: {/if}{if $v->pages}{$v->pages}. {/if}
                {if $v->publisher}{$v->publisher}.{/if}      

	        {if $content->result->references|@count>1}</li>{/if}
        {/foreach}
		{/if}
        </ul>
        </div>

	</div>
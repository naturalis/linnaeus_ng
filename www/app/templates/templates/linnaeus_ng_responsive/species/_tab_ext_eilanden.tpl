<style>
.traits.help {
	vertical-align:super;
	font-size:0.8em;
	font-weight:bold;
	margin-left:2px;
}
table.traits {
	width:510px;	
	border-collapse:collapse;
}
table.traits td {
	padding:1px 0 1px 0;
}
table.traits td li {
	list-style-position: inside;
}
.legend-cell {
	width:200px;
}
.last-row {
	border-bottom:1px solid #eee;
	padding-bottom:0px;
}
ul.traits {
	list-style-type: disc;
	list-style-position: inherit;
}
ul.traits li {
	margin-left:12px;
}
</style>

	
		<div style="margin-bottom:10px">

        {if $passport_content.content}
        <div>
            {$passport_content.content}
        </div>
        {/if}
        
        <div>
		<h2 id="name-header">{t}Voorkomen{/t}</h2>

		<table class="traits">
        {foreach from=$external_content->content_json_decoded->result->data item=v}
        	{foreach from=$v->values item=l key=k}
            {capture "value"}{$l->value_start}{if $l->value_end} - {$l->value_end}{/if}{/capture}
			<tr>
				<td style="width:165px;">{if $k==0}{$v->trait->name}{/if}</td>
                <td>{if $v->values|@count>1}<li>{/if}{$smarty.capture.value}</li></td>
			</tr>
            {/foreach}
            <tr><td class="last-row" colspan="2"></td></tr>
        {/foreach}
		</table>

		{if $external_content->content_json_decoded->result->references}
        <br />
        <h4 class="source">{t}Publicatie{if $external_content->content_json_decoded->result->references|@count>1}s{/if}{/t}</h4>
		<ul class="traits">
        {foreach from=$external_content->content_json_decoded->result->references item=v}
            {if $external_content->content_json_decoded->result->references|@count>1}<li>{/if}
            <a href="../literature2/reference.php?id={$v->id}">
                {capture authors}
                    {foreach from=$v->authors item=author key=ak}{if $ak>0}, {/if}{$author->name|@trim}{/foreach}
                    {if $ak|@is_null}{$v->author}{/if}
                {/capture}
                {$smarty.capture.authors|@trim}{if $v->date}{if $smarty.capture.authors|@trim|@strlen>0}, {/if}{$v->date}{/if}.
                {if $v->label|@trim|@strlen>0}{$v->label|@trim}{if !($v->label|@trim|@substr:-1)|@in_array:array('?','!','.')}. {/if}{/if}
                {if $v->periodical_id}{$v->periodical_ref->label} {elseif $v->periodical}{$v->periodical} {/if}
                {if $v->publishedin_id}In: {$v->publishedin_ref->label} {elseif $v->publishedin}{$v->publishedin} {/if}
                {if $v->volume}{$v->volume}: {/if}
                {if $v->pages}{$v->pages}. {/if}
                {if $v->publisher}{$v->publisher}.{/if}
            </a>
            {if $external_content->content_json_decoded->result->references|@count>1}</li>{/if}
        {/foreach}
        {/if}
        </ul>
        </div>

	</div>
	<div style="margin-bottom:10px">

        {if $passport_content.content}
        <div>
            {$passport_content.content}
        </div>
        {/if}
        
        <div>
		<h2 id="name-header">
        	{if $external_content->content_json_decoded->result->group->name}
    	        {$external_content->content_json_decoded->result->group->name}
            {else}
				{$external_content->content_json_decoded->result->group->sysname}
            {/if}
		</h2>
        
		{if $external_content->content_json_decoded->result->group->description}
        <p>
	        {$external_content->content_json_decoded->result->group->description}
        </p>
        {/if}

		<table>
        {foreach $external_content->content_json_decoded->result->data v k}
			<tr>
	            <td>
                	{if $v->trait->name}{$v->trait->name}{else}{$v->trait->sysname}{/if}:
                	{* if $v->trait->description}<br /><span class="description">{$v->trait->description}</span>{/if *}
				</td>
	            <td>
				{if $v->values|@count>1}<ul>{/if}
                {foreach $v->values vv kk}
	                {if $v->values|@count>1}<li>{/if}
					{$vv->value_start}{if $vv->value_end} - {$vv->value_end}{/if}
	                {if $v->values|@count>1}</li>{/if}
                {/foreach}
				{if $v->values|@count>1}</ul>{/if}
				</td>
			</tr>
			<tr><td class="last-row" colspan="2"></td></tr>
        {/foreach}
		</table>

		{if $external_content->content_json_decoded->result->references}
        <br />
        <h4 class="source">{t}Publicatie{if $external_content->content_json_decoded->result->references|@count>1}s{/if}{/t}</h4>
		<ul class="exotica">
        
        {foreach from=$external_content->content_json_decoded->result->references item=v}
	        {if $external_content->content_json_decoded->result->references|@count>1}<li>{/if}
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
                {if $v->volume}{$v->volume}{/if}{if $v->pages}: {$v->pages}. {/if}
                {if $v->publisher}{$v->publisher}.{/if}      

	        {if $content->result->references|@count>1}</li>{/if}
        {/foreach}
		{/if}
        </ul>
        </div>

	</div>
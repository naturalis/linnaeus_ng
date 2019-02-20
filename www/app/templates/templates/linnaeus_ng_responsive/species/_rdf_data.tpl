		{assign var=hasAuthor value=false}
		{capture name=authors}
		{foreach from=$rdf item=v}{if $v.predicate=='hasAuthor'}{if $hasAuthor}, {/if}{$v.data.name}{assign var=hasAuthor value=true}{/if}{/foreach}		
		{/capture}

		{if $hasAuthor}			
		<h2>{t}Bron{/t}</h2>
		<h4 class="source">{t}Auteur(s){/t}</h4>
		<p>{$smarty.capture.authors}</p>
		{/if}
		{assign var=hasReferences value=0}
		{capture name=references}
		{foreach from=$rdf item=v}
			{if $v.object_type=='reference' && $v.data.label!=''}
			{assign var=hasReferences value=$hasReferences+1}
			<li>
                <a href="../literature2/reference.php?id={$v.data.id}">
                {capture authors}
				{if $v.data.author}
                {$v.data.author}
                {else}
                {foreach from=$v.data.authors item=author key=ak}{if $ak>0}, {/if}{$author.name}{/foreach}
                {if $ak|@is_null}{$v.data.author}{/if}
                {/if}
                {/capture}
                {capture authorstring}
				{$smarty.capture.authors|@trim}{if $v.data.date}{if $smarty.capture.authors|@trim|@strlen>0}, {/if}{$v.data.date}{/if}
                {/capture}
                {if $smarty.capture.authorstring|@trim|@strlen>0}{$smarty.capture.authorstring|@trim}.{/if}
                {if $v.data.label|@trim|@strlen>0}{$v.data.label|@trim}{if !($v.data.label|@trim|@substr:-1)|@in_array:array('?','!','.')}{if $v.data.publication_type!='Website'}.{/if} {/if}{/if}
                {if $v.data.periodical_id}{$v.data.periodical_ref.label} {elseif $v.data.periodical}{$v.data.periodical} {/if}
                {if $v.data.publishedin_id}{$v.data.publishedin_ref.label} {elseif $v.data.publishedin}{$v.data.publishedin} {/if}
                {if $v.data.volume}{$v.data.volume}{/if}{if $v.data.pages}: {$v.data.pages}. {/if}
                {if $v.data.publisher}{$v.data.publisher}.{/if}      
                </a></li>
            {*
                {elseif $v.predicate=='hasReference' && $v.data.citation!=''}
                {assign var=hasReferences value=hasReferences+1}
                <li><a href="../literature2/reference.php?id={$v.data.id}">{$v.data.citation}</a></li>
            *}
			{/if}
		{/foreach}
		{/capture}
			
		{if $hasReferences>0}			
			<h2 class="source">{t}Publicatie{if $hasReferences>1}s{/if}{/t}</h2>
			<ul class="reference">
			{$smarty.capture.references}
			</ul>
		{/if}
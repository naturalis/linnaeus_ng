		{assign var=hasAuthor value=false}
		{capture name=authors}
		{foreach from=$rdf item=v}{if $v.predicate=='hasAuthor'}{if $hasAuthor}, {/if}{$v.data.name}{assign var=hasAuthor value=true}{/if}{/foreach}		
		{/capture}

		{if $hasAuthor}			
		<h2>Bron</h2>
		<p>
			<h4 class="source">Auteur(s)</h4>
			{$smarty.capture.authors}
		</p>
		{/if}
		{assign var=hasReferences value=false}
		{capture name=references}
		{foreach from=$rdf item=v}
			{if $v.predicate=='hasReference' && $v.data.citation!=''}
			{assign var=hasReferences value=true}
			<li><a href="../literature2/reference.php?id={$v.data.id}">{$v.data.citation}</a></li>
			{elseif $v.object_type=='reference' && $v.data.label!=''}
			{assign var=hasReferences value=true}
			<li>
                <a href="../literature2/reference.php?id={$v.data.id}">
                {foreach from=$v.data.authors item=author key=ak}{if $ak>0}, {/if}{$author.name}{/foreach}{if $v.data.date}, {$v.data.date}{/if}.
                {if $v.data.label}{$v.data.label}. {/if}
                {if $v.data.periodical_id}{$v.data.periodical_ref.label} {elseif $v.data.periodical}{$v.data.periodical} {/if}
                {if $v.data.publishedin_id}{$v.data.publishedin_ref.label} {elseif $v.data.publishedin}{$v.data.publishedin} {/if}
                {if $v.data.volume}{$v.data.volume}{/if}{if $v.data.pages}: {$v.data.pages}. {/if}
                {if $v.data.publisher}{$v.data.publisher}.{/if}      
                </a></li>
			{/if}
		{/foreach}
		{/capture}
			
		{if $hasReferences}			
		<p>
			<h4 class="source">Publicatie</h4>
			<ul class="reference">
			{$smarty.capture.references}
			</ul>
		</p>
		{/if}
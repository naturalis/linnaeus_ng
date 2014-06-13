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
			<li>{if $v.data.actor.name}{$v.data.actor.name} {$v.data.date}{else}{$v.data.source}{/if}. <a href="../literature2/reference.php?id={$v.data.id}">{$v.data.label}</a></li>
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
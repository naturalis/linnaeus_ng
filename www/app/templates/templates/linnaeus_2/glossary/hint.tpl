<b>{$term.term}</b><br />
{$term.definition|substr:0:600}{if $term.definition|count_characters:true>600}...{/if}
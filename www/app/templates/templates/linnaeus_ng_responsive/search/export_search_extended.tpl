{assign var=a value=$csvExportSettings['field-sep']}
{assign var=b value=$csvExportSettings['line-end']}
{assign var=c value=$csvExportSettings['field-enclose']}
{$c}{t}Resultaten{/t} 1-{$results.data|@count} {t}van{/t} {$results.count}{if $searchHR || $searchTraitsHR} {t}{t}voor{/t}{/t} '{if $searchHR}{$searchHR}{/if}{if $searchHR && $searchTraitsHR} {/if}{if $searchTraitsHR}{$searchTraitsHR}{/if}'{/if}{$c}{$b}{$c}{t}wetenschappelijke naam{/t}{$c}{$a}{$c}{t}populaire naam{/t}{$c}{$a}{$c}{t}status voorkomen{/t}{$c}{$a}{$c}{t}link{/t}{$c}{$a}{$b}
{foreach from=$results.data item=v}{$c}{$v.taxon_download}{$c}{$a}{$c}{$v.common_name}{$c}{$a}{$c}{$v.presence_information_index_label} {$v.presence_information_title}{$c}{$a}{$c}{$url_taxon_detail}{$v.id}{$c}{$a}{$b}{/foreach}
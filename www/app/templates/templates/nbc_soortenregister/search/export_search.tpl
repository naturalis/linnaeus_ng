{assign var=a value=$csvExportSettings['field-sep']}
{assign var=b value=$csvExportSettings['line-end']}
{assign var=c value=$csvExportSettings['field-enclose']}
{$c}{t}Resultaten{/t} 1-{$results.data|@count} {t}van{/t} {$results.count} {t}voor{/t} '{$search.search|@escape:quotes}'{$c}{$b}{$c}{t}wetenschappelijke naam{/t}{$c}{$a}{$c}nederlandse naam{$c}{$a}{$c}{t}status voorkomen{/t}{$c}{$a}{$c}{t}link naar soortenregister{/t}{$c}{$b}{foreach $results.data v}
{$c}{$v.taxon}{$c}{$a}{$c}{$v.common_name}{$c}{$a}{$c}{$v.presence_information_index_label} {$v.presence_information_title}{$c}{$a}{$c}{$url_taxon_detail}{$v.nsr_id}{$c}{$b}{/foreach}

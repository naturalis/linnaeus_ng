{assign var=a value=$csvExportSettings['field-sep']}
{assign var=b value=$csvExportSettings['line-end']}
{assign var=c value=$csvExportSettings['field-enclose']}
{$c}{t}Resultaten{/t} 1-{$results.data|@count} {t}van{/t} {$results.count}{if $searchHR} {t}voor{/t} '{$searchHR|@escape:quotes}'{/if}{$c}{$b}{$c}{t}wetenschappelijke naam{/t}{$c}{$a}{$c}nederlandse naam{$c}{$a}{$c}fotograaf{$c}{$a}{$c}{t}link naar soortenregister{/t}{$c}{$a}{$c}link naar afbeelding{$c}{$b}{foreach from=$results.data item=v}
{$c}{$v.taxon}{$c}{$a}{$c}{$v.common_name}{$c}{$a}{$c}{$v.photographer}{$c}{$a}{$c}{$url_taxon_detail}{$v.nsr_id}{$c}{$a}{$c}{$taxon_base_url_images_main}{$v.image}{$c}{$b}{/foreach}

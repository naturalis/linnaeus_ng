{assign var=a value=$csvExportSettings['field-sep']}
{assign var=b value=$csvExportSettings['line-end']}
{assign var=c value=$csvExportSettings['field-enclose']}
{$c}Resultaten 1-{$results.data|@count} van {$results.count}{if $searchHR} voor '{$searchHR|@escape:quotes}'{/if}{$c}{$b}{$c}wetenschappelijke naam{$c}{$a}{$c}nederlandse naam{$c}{$a}{$c}fotograaf{$c}{$a}{$c}link naar soortenregister{$c}{$a}{$c}link naar afbeelding{$c}{$b}{foreach from=$results.data item=v}
{$c}{$v.taxon}{$c}{$a}{$c}{$v.common_name}{$c}{$a}{$c}{$v.photographer}{$c}{$a}{$c}{$url_taxon_detail}{$v.nsr_id}{$c}{$a}{$c}{$taxon_base_url_images_main}{$v.image}{$c}{$b}{/foreach}

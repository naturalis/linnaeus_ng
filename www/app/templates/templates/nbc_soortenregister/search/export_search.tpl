{assign var=a value=$csvExportSettings['field-sep']}
{assign var=b value=$csvExportSettings['line-end']}
{assign var=c value=$csvExportSettings['field-enclose']}
{$c}Resultaten 1-{$results.data|@count} van {$results.count} voor '{$search.search|@escape:quotes}'{$c}{$b}{$c}wetenschappelijke naam{$c}{$a}{$c}nederlandse naam{$c}{$a}{$c}status voorkomen{$c}{$a}{$c}link naar soortenregister{$c}{$b}{foreach from=$results.data item=v}
{$c}{$v.taxon}{$c}{$a}{$c}{$v.common_name}{$c}{$a}{$c}{$v.presence_information_index_label} {$v.presence_information_title}{$c}{$a}{$c}{$url_taxon_detail}{$v.nsr_id}{$c}{$b}{/foreach}

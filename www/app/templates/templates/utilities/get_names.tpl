<pre>
{foreach from=$taxa item=v}
taxon	{$v.id}	{$v.taxon}
{/foreach}
{foreach from=$common item=v}
commonname	{$v.id}	{$v.commonname}	{$v.taxon_id}	{$taxa[$v.taxon_id].taxon}	{$lang[$v.language_id].language}
{/foreach}
{foreach from=$synonyms item=v}
synonym	{$v.id}	{$v.synonym}	{$v.taxon_id}	{$taxa[$v.taxon_id].taxon}	
{/foreach}




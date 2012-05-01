<pre>
{foreach from=$taxa item=v}
{if $ranks[$v.rank_id].rank}
accepted	{$v.id}	{$v.taxon}	{$ranks[$v.rank_id].rank}
{/if}
{/foreach}
{foreach from=$common item=v}
{assign var=d value=$taxa[$v.taxon_id]}
{if $ranks[$d.rank_id].rank}
common	{$v.id}	{$v.commonname}	{$v.taxon_id}	{$lang[$v.language_id].language}	{$taxa[$v.taxon_id].taxon}	{$ranks[$d.rank_id].rank}
{/if}
{/foreach}
{foreach from=$synonyms item=v}
{assign var=d value=$taxa[$v.taxon_id]}
{if $ranks[$d.rank_id].rank}
synonym	{$v.id}	{$v.synonym}	{$v.remark}	{$v.taxon_id}	{$taxa[$v.taxon_id].taxon}	{$ranks[$d.rank_id].rank}
{/if}
{/foreach}




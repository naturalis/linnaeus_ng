ID (do not change)	TAXON	NODE 1 LATITUDE	NODE 1 LONGITUDE	NODE 2 LATITUDE	NODE 2 LONGITUDE	NODE 3 LATITUDE	NODE 3 LONGITUDE	etc.
{foreach from=$taxa key=k item=v}
{if $v.lower_taxon==1}{$v.id}	{$v.taxon}
{/if}
{/foreach}
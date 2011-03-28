"The following types of geographic data have"
"been defined in your project:"

DATA TYPE	DATA TYPE ID
{foreach from=$geodataTypes key=k item=v}
{$v.title}:	{$v.id}
{/foreach}

"Copy the appropriate data type ID's in the"
"third column (column C) of your data set below."
"MAKE SURE YOU COPY OR TYPE THE ACTUAL VALUE!"
"DO *NOT* LINK TO THE CELLS ABOVE!"

"(You can safely remove this and the lines above,"
"but retaining them  will not interfere with the"
"correct loading of data.)"


ID (do not change)	TAXON (do not change)	DATA TYPE ID (see above)	NODE 1 LATITUDE	NODE 1 LONGITUDE	NODE 2 LATITUDE	NODE 2 LONGITUDE	NODE 3 LATITUDE	NODE 3 LONGITUDE	etc.
{foreach from=$taxa key=k item=v}
{if $v.lower_taxon==1}{$v.id}	{$v.taxon}
{/if}
{/foreach}
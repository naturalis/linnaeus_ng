"{t}The following types of geographic data have{/t}"
"{t}been defined in your project:{/t}"

{t}DATA TYPE{/t}	{t}DATA TYPE ID{/t}
{foreach from=$geodataTypes key=k item=v}
{$v.title}:	{$v.id}
{/foreach}

"{t}Copy the appropriate data type ID's in the{/t}"
"{t}third column (column C) of your data set below.{/t}"
"{t}MAKE SURE YOU COPY OR TYPE THE ACTUAL VALUE!{/t}"
"{t}DO *NOT* LINK TO THE CELLS ABOVE!{/t}"

"{t}(You can safely remove this and the lines above,{/t}"
"{t}but retaining them  will not interfere with the{/t}"
"{t}correct loading of data.){/t}"


ID {t}(do not change){/t}	{t}TAXON{/t} {t}(do not change){/t}	{t}DATA TYPE ID (see above){/t}	{t}NODE 1 LATITUDE{/t}	{t}NODE 1 LONGITUDE{/t}	{t}NODE 2 LATITUDE{/t}	N{t}ODE 2 LONGITUDE{/t}	{t}NODE 3 LATITUDE{/t}	{t}NODE 3 LONGITUDE{/t}	{t}etc.{/t}
{foreach from=$taxa key=k item=v}
{if $v.lower_taxon==1}{$v.id}	{$v.taxon}
{/if}
{/foreach}
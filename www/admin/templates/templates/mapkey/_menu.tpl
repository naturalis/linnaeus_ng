<input type="button" value="{t}< previous{/t}" {if $navList && $navList[$navCurrentId].prev.id}onclick="allNavigate({$navList[$navCurrentId].prev.id});" title="{$navList[$navCurrentId].prev.title}"{else}disabled="disabled"{/if} class="allLookupButton" />
<input type="button" value="{t}next >{/t}" {if $navList && $navList[$navCurrentId].next.id}onclick="allNavigate({$navList[$navCurrentId].next.id});" title="{$navList[$navCurrentId].next.title}"{else}disabled="disabled"{/if} class="allLookupButton" />
&nbsp;
{t}Type to find:{/t} <input type="text" id="allLookupBox" autocomplete="off" />
&nbsp;
<a href="choose_species.php" class="allLookupLink">{t}Species list{/t}</a>
<a href="data_types.php" class="allLookupLink">{t}Data types{/t}</a>
{if $ln2maps|@count>0}
<a href="ln2_species_show.php?id={$taxon.id}" class="allLookupLink">{t}Linnaeus 2 maps{/t}</a>
{/if}
&nbsp;
<a href="../utilities/search_index.php">Extensive search</a>


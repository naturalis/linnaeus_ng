{if $adjacentTaxa}
<input type="button" value="{t}< previous{/t}" {if $adjacentTaxa.prev.id}onclick="allNavigate({$adjacentTaxa.prev.id});" title="{$adjacentTaxa.prev.label}"{else}disabled="disabled"{/if} class="allLookupButton" />
<input type="button" value="{t}next >{/t}" {if $adjacentTaxa.next.id}onclick="allNavigate({$adjacentTaxa.next.id});" title="{$adjacentTaxa.next.label}"{else}disabled="disabled"{/if} class="allLookupButton" />
{/if}
&nbsp;
{t}Find taxa:{/t} <input type="text" id="allLookupBox" autocomplete="off" />
&nbsp;
<!-- a href="branches.php" class="allLookupLink">Taxon list</a-->
<a href="tree.php" class="allLookupLink">Taxon tree</a>
&nbsp;
<a href="tree.php" class="allLookupLink">Taxon list</a>
&nbsp;
<a href="manage.php" class="allLookupLink">{t}Management{/t}</a>
&nbsp;
<a href="../search/index.php">Extensive search</a>
&nbsp;
<a href="../../../app/views/{$controllerBaseName}/" style="color:#999;margin-left:10px" target="_project">view project</a>
<span id="message-container" style="float:right"></span>
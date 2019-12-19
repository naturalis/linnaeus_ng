<input type="button" value="{t}< previous{/t}" {if $navList && $navList[$navCurrentId].prev.id}onclick="allNavigate({$navList[$navCurrentId].prev.id});" title="{$navList[$navCurrentId].prev.title}"{else}disabled="disabled"{/if} class="allLookupButton" />
<input type="button" value="{t}next >{/t}" {if $navList && $navList[$navCurrentId].next.id}onclick="allNavigate({$navList[$navCurrentId].next.id});" title="{$navList[$navCurrentId].next.title}"{else}disabled="disabled"{/if} class="allLookupButton" />
&nbsp;
{t}Type to find:{/t} <input type="text" id="allLookupBox" autocomplete="off" />
&nbsp;
<a href="contents.php" class="allLookupLink">{t}Contents{/t}</a>
&nbsp;
<a href="edit.php?action=new" class="allLookupLink">{t}Create new{/t}</a>
&nbsp;
<a href="../search/index.php">Extensive search</a>
&nbsp;
<a href="../../../app/views/{$controllerBaseName}/" style="color:#999;margin-left:10px" target="_project">view project</a>
<p>
{if $alpha|@count>0}
<div id="alphabet">
{t}Click to browse:{/t}
{section name=i loop=$alpha}
{if $alpha[i]==$letter}
<span class="alphabet-active-letter">{$alpha[i]}</span>
{else}
<span class="alphabet-letter"><a href="?letter={$alpha[i]}&action=browse">{$alpha[i]}</a></span>
{/if}
{/section}
<form id="alphaForm" method="post" action="">
<input type="hidden" name="letter" id="letter" value="{$letter}"  />
</form>
</div>
{/if}
</p>

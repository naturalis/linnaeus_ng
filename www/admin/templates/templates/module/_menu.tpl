<input type="button" value="{t}< previous{/t}" {if $navList && $navList[$navCurrentId].prev.id}onclick="allNavigate({$navList[$navCurrentId].prev.id});" title="{$navList[$navCurrentId].prev.title}"{else}disabled="disabled"{/if} class="allLookupButton" />
<input type="button" value="{t}next >{/t}" {if $navList && $navList[$navCurrentId].next.id}onclick="allNavigate({$navList[$navCurrentId].next.id});" title="{$navList[$navCurrentId].next.title}"{else}disabled="disabled"{/if} class="allLookupButton" />
&nbsp;
{t}Type to find:{/t} <input type="text" id="allLookupBox" autocomplete="off" />
&nbsp;
<a href="contents.php" class="allLookupLink">{t}Contents{/t}</a>
&nbsp;
<a href="create_page.php" class="allLookupLink">{t}Create new{/t}</a>
&nbsp;
<a href="manage.php" class="allLookupLink">{t}Management{/t}</a>
&nbsp;
<a href="../search/index.php">Extensive search</a>
&nbsp;
<a href="../../../app/views/{$controllerBaseName}/" style="color:#999;margin-left:10px" target="_project">view project</a>
<span id="message-container" style="float:right"></span>
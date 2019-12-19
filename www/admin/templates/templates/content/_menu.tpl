<input type="button" value="{t}Content{/t}"  onclick="window.open('../utilities/admin_index.php','_self');" />
<input type="button" value="{t}Welcome{/t}" {if $subject=='Welcome'}disabled="disabled" style="color:red"{else}onclick="window.open('welcome.php','_self')"{/if} />
<input type="button" value="{t}Contributors{/t}" {if $subject=='Contributors'}disabled="disabled" style="color:red"{else}onclick="window.open('contributors.php','_self')"{/if} />
{*<input type="button" value="{t}About ETI{/t}" {if $subject=='About ETI'}disabled="disabled" style="color:red"{else}onclick="window.open('about_eti.php','_self')"{/if} />*}
&nbsp;&nbsp;
{t}Type to find:{/t} <input type="text" id="allLookupBox" autocomplete="off" />
&nbsp;&nbsp;
<a href="../search/index.php">Extensive search</a>
&nbsp;
<a href="../../../app/views/{$controllerBaseName}/" style="color:#999;margin-left:10px" target="_project">view project</a>
<span id="message-container" style="float:right"></span>
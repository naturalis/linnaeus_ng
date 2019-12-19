<input type="button" value="{t}Content{/t}"  disabled="disabled" style="color:red" />
<input type="button" value="{t}Welcome{/t}" onclick="window.open('views/content/welcome.php','_self')" />
<input type="button" value="{t}Contributors{/t}" onclick="window.open('views/content/contributors.php','_self')" />
{*<input type="button" value="{t}About ETI{/t}" onclick="window.open('views/content/about_eti.php','_self')" />*}
&nbsp;&nbsp;
{t}Type to find:{/t} <input type="text" id="allLookupBox" autocomplete="off" />
&nbsp;&nbsp;
<a href="views/utilities/index.php">Extensive search</a>
&nbsp;
<a href="../../../app/views/{$controllerBaseName}/" style="color:#999;margin-left:10px" target="_project">view project</a>
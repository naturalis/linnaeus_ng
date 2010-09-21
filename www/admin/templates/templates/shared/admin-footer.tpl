</div ends="page-container">

<div id="footer-container">
</div ends="footer-container">

{if $debugMode}
{literal}
<style>
#debug {
	white-space:pre;
	font-size:10px;
	margin-top:15px;
	color:#666;
	font-family:Arial, Helvetica, sans-serif;
}
</style>
<hr style="border-top:1px dotted #ddd;" />
<span onclick="var a=document.getElementById('debug'); if(a.style.visibility=='visible') {a.style.visibility='hidden';} else {a.style.visibility='visible';} " style="cursor:pointer">&nbsp;&Delta;&nbsp;</span>
{/literal}
<div id="debug" style="visibility:hidden">
{php}
var_dump($_SESSION);
{/php}
</div>
{/if}
</div ends="body-container">
<span id="dummy-element"></span>
</body>
</html>

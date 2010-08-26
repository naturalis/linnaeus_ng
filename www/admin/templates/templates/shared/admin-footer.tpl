</div ends="admin-page-container">
<div id="admin-footer-container">
{if !$excludecludeBottonMenu}
	<div id="admin-footer-menu">
		<a href="index.php">User management index</a>
		<a href="/admin/admin-index.php">Main index</a>
		<a href="choose_project.php">Switch projects</a>
		<a href="logout.php">Log out</a>
	</div>
{/if}
</div ends="admin-footer-container">
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
</div ends="admin-body-container"></body>
</html>

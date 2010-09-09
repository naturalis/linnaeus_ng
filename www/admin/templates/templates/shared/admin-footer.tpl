</div ends="page-container">

<div id="footer-container">
{if !$excludecludeBottonMenu}
	<div id="footer-menu">
{if $session.user._number_of_projects > 1}
		<a href="{$rootWebUrl}admin/views/users/choose_project.php">Switch projects</a>
{/if}
		<a href="{$rootWebUrl}admin/views/users/logout.php">Log out (logged in as {if $session.user.last_name!=''}{$session.user.first_name} {$session.user.last_name})</a>
		<br />
{/if}
	</div>
{/if}
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
</div ends="body-container"></body>
</html>

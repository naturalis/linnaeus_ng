<?php /* Smarty version 2.6.26, created on 2010-08-26 13:29:38
         compiled from ../shared/admin-footer.tpl */ ?>
</div ends="admin-page-container">
<div id="admin-footer-container">
<?php if (! $this->_tpl_vars['excludecludeBottonMenu']): ?>
	<div id="admin-footer-menu">
		<a href="index.php">User management index</a>
		<a href="/admin/admin-index.php">Main index</a>
		<a href="choose_project.php">Switch projects</a>
		<a href="logout.php">Log out</a>
	</div>
<?php endif; ?>
</div ends="admin-footer-container">
<?php if ($this->_tpl_vars['debugMode']): ?>
<?php echo '
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
<span onclick="var a=document.getElementById(\'debug\'); if(a.style.visibility==\'visible\') {a.style.visibility=\'hidden\';} else {a.style.visibility=\'visible\';} " style="cursor:pointer">&nbsp;&Delta;&nbsp;</span>
'; ?>

<div id="debug" style="visibility:hidden">
<?php 
var_dump($_SESSION);
 ?>
</div>
<?php endif; ?>
</div ends="admin-body-container"></body>
</html>
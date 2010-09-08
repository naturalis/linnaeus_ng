<?php /* Smarty version 2.6.26, created on 2010-09-08 13:25:56
         compiled from ../shared/admin-footer.tpl */ ?>
</div ends="page-container">

<div id="footer-container">
<?php if (! $this->_tpl_vars['excludecludeBottonMenu']): ?>
	<div id="footer-menu">
		<a href="<?php echo $this->_tpl_vars['rootWebUrl']; ?>
admin/admin-index.php">Main index</a>
<?php if ($this->_tpl_vars['session']['user']['_number_of_projects'] > 1): ?>
		<a href="<?php echo $this->_tpl_vars['rootWebUrl']; ?>
admin/views/users/choose_project.php">Switch projects</a>
<?php endif; ?>
		<a href="<?php echo $this->_tpl_vars['rootWebUrl']; ?>
admin/views/users/logout.php">Log out (logged in as <?php if ($this->_tpl_vars['session']['user']['last_name'] != ''): ?><?php echo $this->_tpl_vars['session']['user']['first_name']; ?>
 <?php echo $this->_tpl_vars['session']['user']['last_name']; ?>
)</a>
		<br />
<?php endif; ?>
	</div>
<?php endif; ?>
</div ends="footer-container">

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
</div ends="body-container"></body>
</html>
<?php /* Smarty version 2.6.26, created on 2010-08-26 12:04:05
         compiled from view.tpl */ ?>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "../shared/admin-header.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>

<div id="admin-main">
<table>
	<tr>
		<td>first_name:</td><td><?php echo $this->_tpl_vars['data']['first_name']; ?>
</td>
	</tr>
	<tr>
		<td>last_name:</td><td><?php echo $this->_tpl_vars['data']['last_name']; ?>
</td>
	</tr>
	<tr>
		<td>username:</td><td><?php echo $this->_tpl_vars['data']['username']; ?>
</td>
	</tr>
	<tr>
		<td>gender:</td>
		<td><?php echo $this->_tpl_vars['data']['gender']; ?>
</td>
	</tr>
	<tr>
		<td>email address:</td>
		<td><?php echo $this->_tpl_vars['data']['email_address']; ?>
</td>
	</tr>
	<tr>
		<td>role in current project:</td>
		<td><?php echo $this->_tpl_vars['userRole']['role']['role']; ?>
</td>
	</tr>
	<tr>
		<td colspan="2">&nbsp;</td>
	</tr>
	<tr>
		<td colspan="2">
			<input type="button" value="Back" onclick="window.open('user_overview.php','_self');" />
			<input type="button" value="Edit" onclick="window.open('edit.php?id=<?php echo $this->_tpl_vars['data']['id']; ?>
','_self');" />
		</td>
	</tr>
</table>
</div>

<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "../shared/admin-footer.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
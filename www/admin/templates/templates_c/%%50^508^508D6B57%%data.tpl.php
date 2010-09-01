<?php /* Smarty version 2.6.26, created on 2010-09-01 12:04:31
         compiled from data.tpl */ ?>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "../shared/admin-header.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>

<div id="admin-main">
<?php echo $this->_tpl_vars['data']['id']; ?>
<?php echo $this->_tpl_vars['data']['version']; ?>

<table>
	<tr>
		<td>
			Internal project name:
		</td>
		<td>
			<?php echo $this->_tpl_vars['data']['sys_name']; ?>

		</td>
	</tr>
	<tr>
		<td>
			Internal project description:
		</td>
		<td>
			<?php echo $this->_tpl_vars['data']['sys_description']; ?>

		</td>
	</tr>
	<tr>
		<td>
			Project title:
		</td>
		<td>
			<input type="text" name="title" value="<?php echo $this->_tpl_vars['data']['title']; ?>
" />
		</td>
	</tr>
	<!-- tr>
		<td>
			Project logo:
		</td>
		<td>
		<form enctype="multipart/form-data" action="" method="POST">
		<input type="hidden" name="MAX_FILE_SIZE" value="10000000" />
			Choose a file to upload: <input name="uploadedfile" type="file" /><br />
			<input type="submit" value="Upload File" />
		</form>

			<input type="text" name="title" value="<?php echo $this->_tpl_vars['data']['logo_path']; ?>
" />
		</td>
	</tr -->
</table>





logo<br />
languages<br />
<br />
welcome text<br />
contrib text<br />
about ETI (fix)<br /><br />

</div>

<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "../shared/admin-footer.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
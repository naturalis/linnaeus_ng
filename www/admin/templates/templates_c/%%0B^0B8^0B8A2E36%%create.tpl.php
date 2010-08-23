<?php /* Smarty version 2.6.26, created on 2010-08-23 15:20:12
         compiled from create.tpl */ ?>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "../shared/admin-header.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>

<div id="admin-titles">
<span id="admin-title">Administration menu</span><br />
<span id="admin-subtitle">User management: create user</span>
</div>

<div id="admin-main">
<form method="post" action="" name="theForm" id="theForm">
<input name="id" value="-1" type="hidden" />
<?php if ($this->_tpl_vars['check'] == true): ?>
<input name="checked" id="checked" value="1" type="hidden" />
<?php endif; ?>
<table>
	<tr>
		<td>username</td><td>
		<?php if ($this->_tpl_vars['check'] == true): ?>
			<?php echo $this->_tpl_vars['data']['username']; ?>

		<?php else: ?>
			<input type="text" name="username" value="<?php echo $this->_tpl_vars['data']['username']; ?>
" maxlength="16" />
		<?php endif; ?>
		</td>
	</tr>
	<tr>
		<td>password</td><td><input type="text" name="password" value="<?php echo $this->_tpl_vars['data']['password']; ?>
" maxlength="16" /></td>
	</tr>
	<tr>
		<td>password (repeat)</td><td><input type="text" name="password_2" value="<?php echo $this->_tpl_vars['data']['password_2']; ?>
" maxlength="16" /></td>
	</tr>
	<tr>
		<td>first_name</td><td><input type="text" name="first_name" value="<?php echo $this->_tpl_vars['data']['first_name']; ?>
" maxlength="16" /></td>
	</tr>
	<tr>
		<td>last_name</td><td><input type="text" name="last_name" value="<?php echo $this->_tpl_vars['data']['last_name']; ?>
" maxlength="16" /></td>
	</tr>
	<tr>
		<td>gender</td>
		<td>
			<label for="gender-f"><input type="radio" id="gender-f" name="gender" value="f" <?php if ($this->_tpl_vars['data']['gender'] != 'm'): ?>checked<?php endif; ?>/>f</label>
			<label for="gender-m"><input type="radio" id="gender-m" name="gender" value="m" <?php if ($this->_tpl_vars['data']['gender'] == 'm'): ?>checked<?php endif; ?> />m</label>
		</td>
	</tr>
	<tr>
		<td>email_address</td><td><input type="text" name="email_address" value="<?php echo $this->_tpl_vars['data']['email_address']; ?>
" maxlength="64" /></td>
	</tr>
	<tr>
		<td colspan="2">
		<?php if ($this->_tpl_vars['check'] == true): ?>
			<input type="button" value="back" onclick="document.getElementById('checked').value='0';document.getElementById('theForm').submit()" />
		<?php endif; ?>
			<input type="submit" value="save" />
		</td>
	</tr>
</table>

</form>

</div>

<div id="admin-messages">
<?php if (! empty ( $this->_tpl_vars['errors'] )): ?>
<?php unset($this->_sections['error']);
$this->_sections['error']['name'] = 'error';
$this->_sections['error']['loop'] = is_array($_loop=$this->_tpl_vars['errors']) ? count($_loop) : max(0, (int)$_loop); unset($_loop);
$this->_sections['error']['show'] = true;
$this->_sections['error']['max'] = $this->_sections['error']['loop'];
$this->_sections['error']['step'] = 1;
$this->_sections['error']['start'] = $this->_sections['error']['step'] > 0 ? 0 : $this->_sections['error']['loop']-1;
if ($this->_sections['error']['show']) {
    $this->_sections['error']['total'] = $this->_sections['error']['loop'];
    if ($this->_sections['error']['total'] == 0)
        $this->_sections['error']['show'] = false;
} else
    $this->_sections['error']['total'] = 0;
if ($this->_sections['error']['show']):

            for ($this->_sections['error']['index'] = $this->_sections['error']['start'], $this->_sections['error']['iteration'] = 1;
                 $this->_sections['error']['iteration'] <= $this->_sections['error']['total'];
                 $this->_sections['error']['index'] += $this->_sections['error']['step'], $this->_sections['error']['iteration']++):
$this->_sections['error']['rownum'] = $this->_sections['error']['iteration'];
$this->_sections['error']['index_prev'] = $this->_sections['error']['index'] - $this->_sections['error']['step'];
$this->_sections['error']['index_next'] = $this->_sections['error']['index'] + $this->_sections['error']['step'];
$this->_sections['error']['first']      = ($this->_sections['error']['iteration'] == 1);
$this->_sections['error']['last']       = ($this->_sections['error']['iteration'] == $this->_sections['error']['total']);
?>
<span class="admin-message-error"><?php echo $this->_tpl_vars['errors'][$this->_sections['error']['index']]; ?>
</span><br />
<?php endfor; endif; ?>
<?php endif; ?>
</div>

<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "../shared/admin-bottom.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "../shared/admin-footer.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
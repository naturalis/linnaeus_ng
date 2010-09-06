<?php /* Smarty version 2.6.26, created on 2010-09-06 18:38:37
         compiled from create.tpl */ ?>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "../shared/admin-header.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>

<div id="page-main">
<?php if ($this->_tpl_vars['check'] == true): ?>
Please verify the data below. Click 'Save' to save the user data; or 'Back' to return to the previous screen.
<?php endif; ?>

<form method="post" action="" name="theForm" id="theForm">
	<input name="id" value="-1" type="hidden" />
	<input name="checked" id="checked" value="<?php echo $this->_tpl_vars['check']; ?>
" type="hidden" />
<?php if ($this->_tpl_vars['existing_user']): ?><input name="existing_user_id" id="existing_user_id" value="<?php echo $this->_tpl_vars['existing_user']['id']; ?>
" type="hidden" /><?php endif; ?>

<table>
	<tr>
		<td>username:</td>
		<td>
		<?php if ($this->_tpl_vars['check'] == true): ?><?php echo $this->_tpl_vars['data']['username']; ?>
<?php else: ?>
			<input 
				type="text" 
				name="username" 
				id="username" 
				value="<?php echo $this->_tpl_vars['data']['username']; ?>
" 
				maxlength="16" 
				onblur="userRemoteValueCheck(this.id,[this.value],['e','f'])" 
			/>
			<span class="asterisk-required-field">*</span>
			<span id="username-message" class=""></span>	<?php endif; ?>
		</td>
	</tr>
	<tr>
		<td>password:</td>
		<td>
		<?php if ($this->_tpl_vars['check'] == true): ?><?php echo $this->_tpl_vars['data']['password']; ?>
<?php else: ?>
			<input
				type="password"
				name="password"
				id="password"
				value="<?php echo $this->_tpl_vars['data']['password']; ?>
"
				maxlength="16"
				onblur="userRemoteValueCheck(this.id,[this.value],['f'])"
			/>
			<span class="asterisk-required-field">*</span>
			<span id="password-message" class=""></span>	</td>	
	</tr>
	<tr>
		<td>password (repeat)</td>
		<td>
			<input
				type="password" 
				name="password_2" 
				id="password_2" 
				value="<?php echo $this->_tpl_vars['data']['password_2']; ?>
" 
				maxlength="16" 
				onblur="userRemoteValueCheck(this.id,[this.value,document.getElementById('password').value],['f','q'])"
			/>
			<span class="asterisk-required-field">*</span>
			<span id="password_2-message" class=""></span>	<?php endif; ?>
		</td>
	</tr>
	<tr>
		<td>first_name:</td>
		<td>
		<?php if ($this->_tpl_vars['check'] == true): ?><?php echo $this->_tpl_vars['data']['first_name']; ?>
<?php else: ?>
			<input
				type="text"
				name="first_name"
				id="first_name"
				value="<?php echo $this->_tpl_vars['data']['first_name']; ?>
" 
				maxlength="32" 
				onblur="userRemoteValueCheck(this.id,[this.value],['f'])"
			/>
			<span class="asterisk-required-field">*</span>
			<span id="first_name-message" class=""></span>	<?php endif; ?>
		</td>
	</tr>
	<tr>
		<td>last_name:</td>
		<td>
		<?php if ($this->_tpl_vars['check'] == true): ?><?php echo $this->_tpl_vars['data']['last_name']; ?>
<?php else: ?>
			<input
				type="text"
				name="last_name"
				id="last_name"
				value="<?php echo $this->_tpl_vars['data']['last_name']; ?>
"
				maxlength="32"
				onblur="userRemoteValueCheck(this.id,[this.value],['f'])"
			/>
			<span class="asterisk-required-field">*</span>
			<span id="last_name-message" class=""></span>	<?php endif; ?>
		</td>
	</tr>
	<tr>
		<td>gender:</td>
		<td>
		<?php if ($this->_tpl_vars['check'] == true): ?><?php echo $this->_tpl_vars['data']['gender']; ?>
<?php else: ?>
			<label for="gender-f">
				<input 
					type="radio" 
					id="gender-f" 
					name="gender" 
					value="f" 
					<?php if ($this->_tpl_vars['data']['gender'] != 'm'): ?>checked="checked"<?php endif; ?>
				/>f
			</label>
			<label for="gender-m">
				<input
					type="radio" 
					id="gender-m" 
					name="gender" 
					value="m"
					<?php if ($this->_tpl_vars['data']['gender'] == 'm'): ?>checked="checked"<?php endif; ?>
				 />m
			</label>
			<span class="asterisk-required-field">*</span>
		<?php endif; ?>
		</td>
	</tr>
	<tr>
		<td>email_address:</td>
		<td>
		<?php if ($this->_tpl_vars['check'] == true): ?><?php echo $this->_tpl_vars['data']['email_address']; ?>
<?php else: ?>
			<input
				type="text" 
				name="email_address" 
				id="email_address" 
				value="<?php echo $this->_tpl_vars['data']['email_address']; ?>
" 
				maxlength="64" 
				onblur="userRemoteValueCheck(this.id,[this.value],['f','e'])"
			/>
			<span class="asterisk-required-field">*</span>
			<span id="email_address-message" class=""></span>
		<?php endif; ?>
		</td>
	</tr>
	<tr>
		<td>role in current project:</td>
		<td>
		<?php if ($this->_tpl_vars['check'] == true): ?><?php unset($this->_sections['i']);
$this->_sections['i']['name'] = 'i';
$this->_sections['i']['loop'] = is_array($_loop=$this->_tpl_vars['roles']) ? count($_loop) : max(0, (int)$_loop); unset($_loop);
$this->_sections['i']['show'] = true;
$this->_sections['i']['max'] = $this->_sections['i']['loop'];
$this->_sections['i']['step'] = 1;
$this->_sections['i']['start'] = $this->_sections['i']['step'] > 0 ? 0 : $this->_sections['i']['loop']-1;
if ($this->_sections['i']['show']) {
    $this->_sections['i']['total'] = $this->_sections['i']['loop'];
    if ($this->_sections['i']['total'] == 0)
        $this->_sections['i']['show'] = false;
} else
    $this->_sections['i']['total'] = 0;
if ($this->_sections['i']['show']):

            for ($this->_sections['i']['index'] = $this->_sections['i']['start'], $this->_sections['i']['iteration'] = 1;
                 $this->_sections['i']['iteration'] <= $this->_sections['i']['total'];
                 $this->_sections['i']['index'] += $this->_sections['i']['step'], $this->_sections['i']['iteration']++):
$this->_sections['i']['rownum'] = $this->_sections['i']['iteration'];
$this->_sections['i']['index_prev'] = $this->_sections['i']['index'] - $this->_sections['i']['step'];
$this->_sections['i']['index_next'] = $this->_sections['i']['index'] + $this->_sections['i']['step'];
$this->_sections['i']['first']      = ($this->_sections['i']['iteration'] == 1);
$this->_sections['i']['last']       = ($this->_sections['i']['iteration'] == $this->_sections['i']['total']);
?><?php if ($this->_tpl_vars['roles'][$this->_sections['i']['index']]['id'] == $this->_tpl_vars['data']['role_id']): ?><?php echo $this->_tpl_vars['roles'][$this->_sections['i']['index']]['role']; ?>
<?php endif; ?><?php endfor; endif; ?><?php else: ?>
			<select name="role_id">
			<?php unset($this->_sections['i']);
$this->_sections['i']['name'] = 'i';
$this->_sections['i']['loop'] = is_array($_loop=$this->_tpl_vars['roles']) ? count($_loop) : max(0, (int)$_loop); unset($_loop);
$this->_sections['i']['show'] = true;
$this->_sections['i']['max'] = $this->_sections['i']['loop'];
$this->_sections['i']['step'] = 1;
$this->_sections['i']['start'] = $this->_sections['i']['step'] > 0 ? 0 : $this->_sections['i']['loop']-1;
if ($this->_sections['i']['show']) {
    $this->_sections['i']['total'] = $this->_sections['i']['loop'];
    if ($this->_sections['i']['total'] == 0)
        $this->_sections['i']['show'] = false;
} else
    $this->_sections['i']['total'] = 0;
if ($this->_sections['i']['show']):

            for ($this->_sections['i']['index'] = $this->_sections['i']['start'], $this->_sections['i']['iteration'] = 1;
                 $this->_sections['i']['iteration'] <= $this->_sections['i']['total'];
                 $this->_sections['i']['index'] += $this->_sections['i']['step'], $this->_sections['i']['iteration']++):
$this->_sections['i']['rownum'] = $this->_sections['i']['iteration'];
$this->_sections['i']['index_prev'] = $this->_sections['i']['index'] - $this->_sections['i']['step'];
$this->_sections['i']['index_next'] = $this->_sections['i']['index'] + $this->_sections['i']['step'];
$this->_sections['i']['first']      = ($this->_sections['i']['iteration'] == 1);
$this->_sections['i']['last']       = ($this->_sections['i']['iteration'] == $this->_sections['i']['total']);
?>
				<option
					title="<?php echo $this->_tpl_vars['roles'][$this->_sections['i']['index']]['role']; ?>
: <?php echo $this->_tpl_vars['roles'][$this->_sections['i']['index']]['description']; ?>
" 
					value="<?php echo $this->_tpl_vars['roles'][$this->_sections['i']['index']]['id']; ?>
"
					<?php if ($this->_tpl_vars['roles'][$this->_sections['i']['index']]['id'] == $this->_tpl_vars['data']['role_id']): ?> selected class="option-selected" <?php endif; ?>
				><?php echo $this->_tpl_vars['roles'][$this->_sections['i']['index']]['role']; ?>
</option>
			<?php endfor; endif; ?>
			</select>
			<span class="asterisk-required-field">*</span>
		<?php endif; ?>
		</td>
	</tr>
	<tr>
		<td colspan="2">
		<?php if ($this->_tpl_vars['check'] == true): ?>
			<input
				type="button" 
				value="Back" 
				onclick="document.getElementById('checked').value='-1';document.getElementById('theForm').submit()" />
		<?php endif; ?>
			<input type="submit" value="Save" />
		</td>
	</tr>
</table>

</form>

</div>

<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "../shared/admin-messages.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>

<?php echo '
<script type="text/JavaScript">

$(document).ready(function(){

	$(\'#username\').focus();

});

</script>
'; ?>


<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "../shared/admin-footer.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
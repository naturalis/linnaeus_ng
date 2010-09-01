<?php /* Smarty version 2.6.26, created on 2010-09-01 12:04:23
         compiled from edit.tpl */ ?>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "../shared/admin-header.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>

<div id="admin-main">
<form method="post" action="" name="theForm" id="theForm">
	<input name="id" value="<?php echo $this->_tpl_vars['data']['id']; ?>
" type="hidden" />
	<input name="checked" id="checked" value="1" type="hidden" />
	<input name="delete" id="delete" value="0" type="hidden" />
	<input name="userProjectRole" value="<?php echo $this->_tpl_vars['userRole']['id']; ?>
" type="hidden" />
<script type="text/javascript">
	userid = '<?php echo $this->_tpl_vars['data']['id']; ?>
';
</script>
<table>
	<tr>
		<td>username</td>
		<td>
			<input
				type="text" 
				name="username" 
				id="username" 
				value="<?php echo $this->_tpl_vars['data']['username']; ?>
" 
				maxlength="16" 
				onblur="remoteValueCheck(this.id,[this.value],['e','f'],userid)" 
			/>
			<span class="admin-required-field-asterisk">*</span>
			<span id="username-message" class=""></span>
		</td>
	</tr>
	<tr>
		<td>password</td>
		<td>
			<input 
				type="password" 
				name="password" 
				id="password" 
				value="" 
				maxlength="16" 
				onblur="<?php echo 'if (this.value) { remoteValueCheck(this.id,[this.value],[\'f\'],userid); }'; ?>
"
			/>
			<span class="admin-required-field-asterisk">*</span>
			<span id="password-message" class="">(leave blank to leave unchanged)</span>
		</td>
	</tr>
	<tr>
		<td>password (repeat)</td>
		<td>
			<input 
				type="password" 
				name="password_2" 
				id="password_2" 
				value="" 
				maxlength="16" 
				onblur="<?php echo 'if (this.value || $(\'#password.val().)) { remoteValueCheck(this.id,[this.value,document.getElementById(\'password\').value],[\'f\',\'q\'],userid); }'; ?>
"
			/>
			<span class="admin-required-field-asterisk">*</span>
			<span id="password_2-message" class="">(leave blank to leave unchanged)</span>
		</td>
	</tr>
	<tr>
		<td>first_name</td>
		<td>
			<input 
				type="text" 
				name="first_name" 
				id="first_name" 
				value="<?php echo $this->_tpl_vars['data']['first_name']; ?>
" 
				maxlength="32"
				onblur="remoteValueCheck(this.id,[this.value],['f'],userid)"
			/>
			<span class="admin-required-field-asterisk">*</span>
			<span id="first_name-message" class=""></span>
		</td>
	</tr>
	<tr>
		<td>last_name</td>
		<td>
			<input 
				type="text" 
				name="last_name" 
				id="last_name" 
				value="<?php echo $this->_tpl_vars['data']['last_name']; ?>
" 
				maxlength="32"
				onblur="remoteValueCheck(this.id,[this.value],['f'],userid)"
			/>
			<span class="admin-required-field-asterisk">*</span>
			<span id="last_name-message" class=""></span>
		</td>
	</tr>
	<tr>
		<td>gender</td>
		<td>
			<label for="gender-f">
				<input 
					type="radio" 
					id="gender-f" 
					name="gender" 
					value="f" <?php if ($this->_tpl_vars['data']['gender'] != 'm'): ?>checked="checked"<?php endif; ?>
				/>f
			</label>
			<label for="gender-m">
				<input
					type="radio" 
					id="gender-m" 
					name="gender" 
					value="m" <?php if ($this->_tpl_vars['data']['gender'] == 'm'): ?>checked="checked"<?php endif; ?> 
				/>m
			</label>
			<span class="admin-required-field-asterisk">*</span>
		</td>
	</tr>
	<tr>
		<td>email_address</td>
		<td>
			<input 
				type="text" 
				name="email_address" 
				id="email_address" 
				value="<?php echo $this->_tpl_vars['data']['email_address']; ?>
" 
				maxlength="64"
				onblur="remoteValueCheck(this.id,[this.value],['f','e'],userid)"
			/>
			<span class="admin-required-field-asterisk">*</span>
			<span id="email_address-message" class=""></span>
		</td>
	</tr>

	<tr>
		<td>role in current project:</td>
		<td>
		<?php if ($this->_tpl_vars['isLeadExpert']): ?>Lead expert<?php else: ?>
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
<?php if ($this->_tpl_vars['roles'][$this->_sections['i']['index']]['id'] == $this->_tpl_vars['userRole']['role']['id']): ?> (current)<?php endif; ?>" 
					value="<?php echo $this->_tpl_vars['roles'][$this->_sections['i']['index']]['id']; ?>
"
					<?php if ($this->_tpl_vars['roles'][$this->_sections['i']['index']]['id'] == $this->_tpl_vars['userRole']['role']['id']): ?> selected class="option-selected" <?php endif; ?>
				><?php echo $this->_tpl_vars['roles'][$this->_sections['i']['index']]['role']; ?>
</option>
			<?php endfor; endif; ?>
			</select>
		<?php endif; ?>
</td>
	</tr>
	<tr>
		<td>active</td>
		<td>
			<label for="active-y">
				<input
					type="radio" 
					id="active-y" 
					name="active" 
					value="1"
					<?php if ($this->_tpl_vars['isLeadExpert']): ?> disabled="disabled"<?php endif; ?> 
					<?php if ($this->_tpl_vars['data']['active'] == '1'): ?>checked="checked"<?php endif; ?>/>y
			</label>
			<label for="active-n">
				<input
					type="radio" 
					id="active-n" 
					name="active" 
					value="0" 
					<?php if ($this->_tpl_vars['isLeadExpert']): ?> disabled="disabled"<?php endif; ?> 
					<?php if ($this->_tpl_vars['data']['active'] != '1'): ?>checked="checked"<?php endif; ?> />n
			</label>
		</td>
	</tr>
	<tr>
		<td colspan="2">&nbsp;</td>
	</tr>
	<tr>
		<td colspan="2">
			<input type="submit" value="Save" />
			<?php echo '
			<input type="button" value="Delete" onclick="if (confirm(\'Are you sure?\')) { var e = document.getElementById(\'delete\'); e.value = \'1\'; e = document.getElementById(\'theForm\'); e.submit(); } " />
			'; ?>

			<input type="button" value="Back" onclick="window.open('user_overview.php','_self');" />
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
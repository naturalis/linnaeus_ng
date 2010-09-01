<?php /* Smarty version 2.6.26, created on 2010-09-01 12:02:36
         compiled from user_overview.tpl */ ?>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "../shared/admin-header.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>

<div id="admin-main">
<table>
<tr>
	<th onclick="tableColumnSort('id');">id</th>
	<th onclick="tableColumnSort('first_name');">first name</th>
	<th onclick="tableColumnSort('last_name');">last name</th>
	<th onclick="tableColumnSort('gender');">gender</th>
	<th onclick="tableColumnSort('email_address');">e-mail</th>
	<th onclick="tableColumnSort('role');">role</th>
	<th></th>
	<th></th>
</tr>
<?php unset($this->_sections['i']);
$this->_sections['i']['name'] = 'i';
$this->_sections['i']['loop'] = is_array($_loop=$this->_tpl_vars['users']) ? count($_loop) : max(0, (int)$_loop); unset($_loop);
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
<tr>
	<td><?php echo $this->_tpl_vars['users'][$this->_sections['i']['index']]['id']; ?>
</td>
	<td><?php echo $this->_tpl_vars['users'][$this->_sections['i']['index']]['first_name']; ?>
</td>
	<td><?php echo $this->_tpl_vars['users'][$this->_sections['i']['index']]['last_name']; ?>
</td>
	<td><?php echo $this->_tpl_vars['users'][$this->_sections['i']['index']]['gender']; ?>
</td>
	<td><?php echo $this->_tpl_vars['users'][$this->_sections['i']['index']]['email_address']; ?>
</td>
	<td><?php echo $this->_tpl_vars['users'][$this->_sections['i']['index']]['role']; ?>
</td>
	<td>[<a href="view.php?id=<?php echo $this->_tpl_vars['users'][$this->_sections['i']['index']]['id']; ?>
">view</a>]</td>
	<td>[<a href="edit.php?id=<?php echo $this->_tpl_vars['users'][$this->_sections['i']['index']]['id']; ?>
">edit</a>]</td>
</tr>
<?php endfor; endif; ?>
</table>
</div>

<form method="post" action="" name="postForm" id="postForm">
<input type="hidden" name="key" id="key" value="<?php echo $this->_tpl_vars['sortBy']['key']; ?>
" />
<input type="hidden" name="dir" value="<?php echo $this->_tpl_vars['sortBy']['dir']; ?>
"  />
</form>

<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "../shared/admin-footer.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
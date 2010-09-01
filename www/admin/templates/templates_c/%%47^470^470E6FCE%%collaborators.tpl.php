<?php /* Smarty version 2.6.26, created on 2010-09-01 13:03:00
         compiled from collaborators.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'count', 'collaborators.tpl', 22, false),)), $this); ?>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "../shared/admin-header.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>

<div id="admin-main">
<div class="admin-text-block">
Select the standard modules you wish to use in your project:<br />
<table>
<?php unset($this->_sections['i']);
$this->_sections['i']['name'] = 'i';
$this->_sections['i']['loop'] = is_array($_loop=$this->_tpl_vars['modules']) ? count($_loop) : max(0, (int)$_loop); unset($_loop);
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
	<?php if ($this->_tpl_vars['modules'][$this->_sections['i']['index']]['active'] == 'y'): ?>
		<td title="in use in your project" class="admin-td-module-inuse">&nbsp;</td>
		<td style="width:100px">
			<span class="admin-td-module-title-inuse" id="cell-<?php echo $this->_tpl_vars['modules'][$this->_sections['i']['index']]['id']; ?>
d">
	<?php else: ?>
		<td title="in use in your project, but inactive" class="admin-td-module-inactive" >&nbsp;</td>
		<td style="width:100px">
			<span class="admin-td-module-title-deactivated" id="cell-<?php echo $this->_tpl_vars['modules'][$this->_sections['i']['index']]['id']; ?>
d">
	<?php endif; ?>
				<span class="admin-td-module-title"><?php echo $this->_tpl_vars['modules'][$this->_sections['i']['index']]['module']; ?>
</span>
			</span>
		</td>
		<td>
			<span onclick="toggleModuleUsers(<?php echo $this->_tpl_vars['modules'][$this->_sections['i']['index']]['id']; ?>
);" style="cursor:pointer"><span id="cell-<?php echo $this->_tpl_vars['modules'][$this->_sections['i']['index']]['id']; ?>
n"><?php echo count($this->_tpl_vars['modules'][$this->_sections['i']['index']]['collaborators']); ?>
</span> collaborators</span>
		</td>
	</tr>
	<tr id="users-<?php echo $this->_tpl_vars['modules'][$this->_sections['i']['index']]['id']; ?>
" class="admin-modusers-hidden">
		<td colspan="3">
			<table>
			<?php unset($this->_sections['j']);
$this->_sections['j']['name'] = 'j';
$this->_sections['j']['loop'] = is_array($_loop=$this->_tpl_vars['users']) ? count($_loop) : max(0, (int)$_loop); unset($_loop);
$this->_sections['j']['show'] = true;
$this->_sections['j']['max'] = $this->_sections['j']['loop'];
$this->_sections['j']['step'] = 1;
$this->_sections['j']['start'] = $this->_sections['j']['step'] > 0 ? 0 : $this->_sections['j']['loop']-1;
if ($this->_sections['j']['show']) {
    $this->_sections['j']['total'] = $this->_sections['j']['loop'];
    if ($this->_sections['j']['total'] == 0)
        $this->_sections['j']['show'] = false;
} else
    $this->_sections['j']['total'] = 0;
if ($this->_sections['j']['show']):

            for ($this->_sections['j']['index'] = $this->_sections['j']['start'], $this->_sections['j']['iteration'] = 1;
                 $this->_sections['j']['iteration'] <= $this->_sections['j']['total'];
                 $this->_sections['j']['index'] += $this->_sections['j']['step'], $this->_sections['j']['iteration']++):
$this->_sections['j']['rownum'] = $this->_sections['j']['iteration'];
$this->_sections['j']['index_prev'] = $this->_sections['j']['index'] - $this->_sections['j']['step'];
$this->_sections['j']['index_next'] = $this->_sections['j']['index'] + $this->_sections['j']['step'];
$this->_sections['j']['first']      = ($this->_sections['j']['iteration'] == 1);
$this->_sections['j']['last']       = ($this->_sections['j']['iteration'] == $this->_sections['j']['total']);
?>
				<?php $this->assign('x', $this->_tpl_vars['users'][$this->_sections['j']['index']]['id']); ?>
				<tr>
					<td style="width:15px;">
					</td>
				<?php if ($this->_tpl_vars['modules'][$this->_sections['i']['index']]['collaborators'][$this->_tpl_vars['x']]['user_id'] == $this->_tpl_vars['users'][$this->_sections['j']['index']]['id']): ?>
					<td 
						id="cell-<?php echo $this->_tpl_vars['modules'][$this->_sections['i']['index']]['id']; ?>
-<?php echo $this->_tpl_vars['users'][$this->_sections['j']['index']]['id']; ?>
a"
						class="admin-td-module-title-inuse">
							<?php echo $this->_tpl_vars['users'][$this->_sections['j']['index']]['first_name']; ?>
 <?php echo $this->_tpl_vars['users'][$this->_sections['j']['index']]['last_name']; ?>

						</td>
					<td><?php echo $this->_tpl_vars['users'][$this->_sections['j']['index']]['role']; ?>
</td>
					<td 
						title="remove collaborator" 
						class="admin-td-moduser-remove"
						id="cell-<?php echo $this->_tpl_vars['modules'][$this->_sections['i']['index']]['id']; ?>
-<?php echo $this->_tpl_vars['users'][$this->_sections['j']['index']]['id']; ?>
b"
						onclick="moduleUserAction(this,<?php echo $this->_tpl_vars['modules'][$this->_sections['i']['index']]['id']; ?>
,<?php echo $this->_tpl_vars['users'][$this->_sections['j']['index']]['id']; ?>
,'remove')">
					</td>
				<?php else: ?>
					<td
						id="cell-<?php echo $this->_tpl_vars['modules'][$this->_sections['i']['index']]['id']; ?>
-<?php echo $this->_tpl_vars['users'][$this->_sections['j']['index']]['id']; ?>
a"
						class="">
						<?php echo $this->_tpl_vars['users'][$this->_sections['j']['index']]['first_name']; ?>
 <?php echo $this->_tpl_vars['users'][$this->_sections['j']['index']]['last_name']; ?>

					</td>
					<td><?php echo $this->_tpl_vars['users'][$this->_sections['j']['index']]['role']; ?>
</td>
					<td
						title="add collaborator" 
						class="admin-td-moduser-inactive"
						id="cell-<?php echo $this->_tpl_vars['modules'][$this->_sections['i']['index']]['id']; ?>
-<?php echo $this->_tpl_vars['users'][$this->_sections['j']['index']]['id']; ?>
b"
						onclick="moduleUserAction(this,<?php echo $this->_tpl_vars['modules'][$this->_sections['i']['index']]['id']; ?>
,<?php echo $this->_tpl_vars['users'][$this->_sections['j']['index']]['id']; ?>
,'add')">
					</td>
				<?php endif; ?>
				</tr>
			<?php endfor; endif; ?>			
			</table>
		</td>
	</tr>
<?php endfor; endif; ?>
</table>
</div>

<br />

<div class="admin-text-block">
Besides these standard modules, you can add up to five extra content modules to your project:<br />
<table>
<?php $this->assign('n', 1); ?>
<?php unset($this->_sections['i']);
$this->_sections['i']['name'] = 'i';
$this->_sections['i']['loop'] = is_array($_loop=$this->_tpl_vars['free_modules']) ? count($_loop) : max(0, (int)$_loop); unset($_loop);
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
	<tr id="row-f<?php echo $this->_tpl_vars['free_modules'][$this->_sections['i']['index']]['id']; ?>
">
	<?php if ($this->_tpl_vars['free_modules'][$this->_sections['i']['index']]['active'] == 'y'): ?>
		<td title="in use in your project" class="admin-td-module-inuse" id="cell-f<?php echo $this->_tpl_vars['free_modules'][$this->_sections['i']['index']]['id']; ?>
a">&nbsp;</td>
		<td>
			<span class="admin-td-module-title-inuse" id="cell-f<?php echo $this->_tpl_vars['free_modules'][$this->_sections['i']['index']]['id']; ?>
d">
	<?php else: ?>
		<td title="in use in your project, but inactive" class="admin-td-module-inactive" >&nbsp;</td>
		<td>
			<span class="admin-td-module-title-deactivated" id="cell-f<?php echo $this->_tpl_vars['free_modules'][$this->_sections['i']['index']]['id']; ?>
d">
	<?php endif; ?>
				<span class="admin-td-module-title"><?php echo $this->_tpl_vars['free_modules'][$this->_sections['i']['index']]['module']; ?>
</span>
			</span>
		</td>
</tr>
<?php endfor; endif; ?>
</table>

</div>

</div>

<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "../shared/admin-messages.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "../shared/admin-footer.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
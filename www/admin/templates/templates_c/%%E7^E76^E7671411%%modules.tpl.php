<?php /* Smarty version 2.6.26, created on 2010-09-06 15:41:19
         compiled from modules.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'count', 'modules.tpl', 187, false),)), $this); ?>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "../shared/admin-header.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>

<div id="page-main">

<div class="text-block">
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
	<?php if (! $this->_tpl_vars['modules'][$this->_sections['i']['index']]['module_project_id']): ?>
		<td
			class="cell-module-unused" 
			id="cell-<?php echo $this->_tpl_vars['modules'][$this->_sections['i']['index']]['id']; ?>
a"
			title="not in use in your project" 
		>&nbsp;
			
		</td>
		<td
			class="cell-module-activate" 
			title="activate" 
			onclick="moduleChangeModuleStatus(this)"
			id="cell-<?php echo $this->_tpl_vars['modules'][$this->_sections['i']['index']]['id']; ?>
b"
		>&nbsp;
			
		</td>
		<td
			class="cell-module-invisible" 
			title="" 
			onclick="moduleChangeModuleStatus(this)"
			id="cell-<?php echo $this->_tpl_vars['modules'][$this->_sections['i']['index']]['id']; ?>
c"
		>&nbsp;
			
		</td>	
		<td>
			<span
				class="cell-module-title-unused" 
				id="cell-<?php echo $this->_tpl_vars['modules'][$this->_sections['i']['index']]['id']; ?>
d">
			<span class="cell-module-title"><?php echo $this->_tpl_vars['modules'][$this->_sections['i']['index']]['module']; ?>
</span> - <?php echo $this->_tpl_vars['modules'][$this->_sections['i']['index']]['description']; ?>
</span>
			<span id="cell-<?php echo $this->_tpl_vars['modules'][$this->_sections['i']['index']]['id']; ?>
e" style="visibility:hidden"><?php echo $this->_tpl_vars['modules'][$this->_sections['i']['index']]['module']; ?>
</span>
		</td>
	<?php else: ?>
	<?php if ($this->_tpl_vars['modules'][$this->_sections['i']['index']]['active'] == 'y'): ?>
		<td
			title="in use in your project"
			class="cell-module-in-use" 
			id="cell-<?php echo $this->_tpl_vars['modules'][$this->_sections['i']['index']]['id']; ?>
a"
		>&nbsp;
			
		</td>
		<td 
			class="cell-module-deactivate" 
			title="deactivate (no data will be deleted)" 
			onclick="moduleChangeModuleStatus(this)"
			id="cell-<?php echo $this->_tpl_vars['modules'][$this->_sections['i']['index']]['id']; ?>
b"
		>&nbsp;
			
		</td>
		<td
			class="cell-module-invisible" 
			title="" 
			onclick="moduleChangeModuleStatus(this)"
			id="cell-<?php echo $this->_tpl_vars['modules'][$this->_sections['i']['index']]['id']; ?>
c"
		>&nbsp;
		</td>
		<td>
			<span 
				class="cell-module-title-in-use" 
				id="cell-<?php echo $this->_tpl_vars['modules'][$this->_sections['i']['index']]['id']; ?>
d">
			<span class="cell-module-title"><?php echo $this->_tpl_vars['modules'][$this->_sections['i']['index']]['module']; ?>
</span> - <?php echo $this->_tpl_vars['modules'][$this->_sections['i']['index']]['description']; ?>
</span>
			<span id="cell-<?php echo $this->_tpl_vars['modules'][$this->_sections['i']['index']]['id']; ?>
e" style="visibility:hidden"><?php echo $this->_tpl_vars['modules'][$this->_sections['i']['index']]['module']; ?>
</span>
		</td>
	<?php else: ?>
		<td
			title="in use in your project, but inactive" 
			class="cell-module-inactive"
			id="cell-<?php echo $this->_tpl_vars['modules'][$this->_sections['i']['index']]['id']; ?>
a"
		>&nbsp;
			
		</td>
		<td 
			class="cell-module-reactivate" 
			title="re-activate" 
			onclick="moduleChangeModuleStatus(this)"
			id="cell-<?php echo $this->_tpl_vars['modules'][$this->_sections['i']['index']]['id']; ?>
b"
		>&nbsp;
			
		</td>
		<td
			class="cell-module-delete" 
			title="delete module and data" 
			onclick="moduleChangeModuleStatus(this)"
			id="cell-<?php echo $this->_tpl_vars['modules'][$this->_sections['i']['index']]['id']; ?>
c"
		>&nbsp;
			
		</td>
		<td>
			<span
				class="cell-module-title-inactive" 
				id="cell-<?php echo $this->_tpl_vars['modules'][$this->_sections['i']['index']]['id']; ?>
d">
			<span class="cell-module-title"><?php echo $this->_tpl_vars['modules'][$this->_sections['i']['index']]['module']; ?>
</span> - <?php echo $this->_tpl_vars['modules'][$this->_sections['i']['index']]['description']; ?>

			<span id="cell-<?php echo $this->_tpl_vars['modules'][$this->_sections['i']['index']]['id']; ?>
e" style="visibility:hidden"><?php echo $this->_tpl_vars['modules'][$this->_sections['i']['index']]['module']; ?>
</span>
			</span>
		</td>
	<?php endif; ?>

<?php endif; ?>
</tr>
<?php endfor; endif; ?>
</table>
</div>

<br />

<div class="text-block">
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
		<td
			title="in use in your project"
			class="cell-module-in-use" 
			id="cell-f<?php echo $this->_tpl_vars['free_modules'][$this->_sections['i']['index']]['id']; ?>
a"
		>&nbsp;
			
		</td>
		<td 
			class="cell-module-deactivate" 
			title="deactivate (no data will be deleted)" 
			onclick="moduleChangeModuleStatus(this)"
			id="cell-f<?php echo $this->_tpl_vars['free_modules'][$this->_sections['i']['index']]['id']; ?>
b"
		>&nbsp;
			
		</td>
		<td
			class="cell-module-invisible" 
			title="" 
			onclick="moduleChangeModuleStatus(this,['row-f<?php echo $this->_tpl_vars['free_modules'][$this->_sections['i']['index']]['id']; ?>
'])"
			id="cell-f<?php echo $this->_tpl_vars['free_modules'][$this->_sections['i']['index']]['id']; ?>
c"
		>&nbsp;
		</td>
		<td>
			<span 
				class="cell-module-title-in-use" 
				id="cell-f<?php echo $this->_tpl_vars['free_modules'][$this->_sections['i']['index']]['id']; ?>
d">
			<span class="cell-module-title"><?php echo $this->_tpl_vars['free_modules'][$this->_sections['i']['index']]['module']; ?>
</span></span>
			<span id="cell-f<?php echo $this->_tpl_vars['free_modules'][$this->_sections['i']['index']]['id']; ?>
e" style="visibility:hidden"><?php echo $this->_tpl_vars['free_modules'][$this->_sections['i']['index']]['module']; ?>
</span>
		</td>
	<?php else: ?>
		<td
			title="in use in your project, but inactive" 
			class="cell-module-inactive"
			id="cell-f<?php echo $this->_tpl_vars['free_modules'][$this->_sections['i']['index']]['id']; ?>
a"
		>&nbsp;
			
		</td>
		<td 
			class="cell-module-reactivate" 
			title="re-activate" 
			onclick="moduleChangeModuleStatus(this)"
			id="cell-f<?php echo $this->_tpl_vars['free_modules'][$this->_sections['i']['index']]['id']; ?>
b"
		>&nbsp;
			
		</td>
		<td
			class="cell-module-delete" 
			title="delete module and data" 
			onclick="moduleChangeModuleStatus(this,['row-f<?php echo $this->_tpl_vars['free_modules'][$this->_sections['i']['index']]['id']; ?>
'])"
			id="cell-f<?php echo $this->_tpl_vars['free_modules'][$this->_sections['i']['index']]['id']; ?>
c"
		>&nbsp;
			
		</td>
		<td>
			<span
				class="cell-module-title-inactive" 
				id="cell-f<?php echo $this->_tpl_vars['free_modules'][$this->_sections['i']['index']]['id']; ?>
d">
			<span class="cell-module-title"><?php echo $this->_tpl_vars['free_modules'][$this->_sections['i']['index']]['module']; ?>
</span>
			<span id="cell-f<?php echo $this->_tpl_vars['free_modules'][$this->_sections['i']['index']]['id']; ?>
e" style="visibility:hidden"><?php echo $this->_tpl_vars['free_modules'][$this->_sections['i']['index']]['module']; ?>
</span>
			</span>
		</td>
	<?php endif; ?>
</tr>
<?php endfor; endif; ?>
</table>

<table id="new-input" class="<?php if (count($this->_tpl_vars['free_modules']) >= 5): ?>module-new-input-hidden<?php endif; ?>">
<tr>
	<td colspan="4">&nbsp;</td>
</tr>
<tr >
	<td colspan="4">
		<form action="" method="post">
		<input type="hidden" name="rnd" value="<?php echo $this->_tpl_vars['rnd']; ?>
">
		Enter new module's name: <input type="text" name="module_new" id="module_new" value="" maxlength="32" />
		<input type="submit" value="add module" onclick="addFreeModule();" />
		</form>	
	</td>
</tr>
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
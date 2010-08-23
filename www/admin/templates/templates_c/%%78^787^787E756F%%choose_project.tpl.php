<?php /* Smarty version 2.6.26, created on 2010-08-23 13:06:50
         compiled from choose_project.tpl */ ?>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "../shared/admin-header.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>

<div id="admin-titles">
<span id="admin-title">Administration menu</span><br />
<span id="admin-subtitle">Choose you project</span>
</div>

<div id="admin-main">
<ul class="admin-list">
<?php unset($this->_sections['project']);
$this->_sections['project']['name'] = 'project';
$this->_sections['project']['loop'] = is_array($_loop=$this->_tpl_vars['projects']) ? count($_loop) : max(0, (int)$_loop); unset($_loop);
$this->_sections['project']['show'] = true;
$this->_sections['project']['max'] = $this->_sections['project']['loop'];
$this->_sections['project']['step'] = 1;
$this->_sections['project']['start'] = $this->_sections['project']['step'] > 0 ? 0 : $this->_sections['project']['loop']-1;
if ($this->_sections['project']['show']) {
    $this->_sections['project']['total'] = $this->_sections['project']['loop'];
    if ($this->_sections['project']['total'] == 0)
        $this->_sections['project']['show'] = false;
} else
    $this->_sections['project']['total'] = 0;
if ($this->_sections['project']['show']):

            for ($this->_sections['project']['index'] = $this->_sections['project']['start'], $this->_sections['project']['iteration'] = 1;
                 $this->_sections['project']['iteration'] <= $this->_sections['project']['total'];
                 $this->_sections['project']['index'] += $this->_sections['project']['step'], $this->_sections['project']['iteration']++):
$this->_sections['project']['rownum'] = $this->_sections['project']['iteration'];
$this->_sections['project']['index_prev'] = $this->_sections['project']['index'] - $this->_sections['project']['step'];
$this->_sections['project']['index_next'] = $this->_sections['project']['index'] + $this->_sections['project']['step'];
$this->_sections['project']['first']      = ($this->_sections['project']['iteration'] == 1);
$this->_sections['project']['last']       = ($this->_sections['project']['iteration'] == $this->_sections['project']['total']);
?>
<li><a href="?project_id=<?php echo $this->_tpl_vars['projects'][$this->_sections['project']['index']]['id']; ?>
"><?php echo $this->_tpl_vars['projects'][$this->_sections['project']['index']]['name']; ?>
</a></li>
<?php endfor; endif; ?>

</ul>
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
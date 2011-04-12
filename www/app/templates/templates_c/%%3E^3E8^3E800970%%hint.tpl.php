<?php /* Smarty version 2.6.26, created on 2011-04-12 15:52:54
         compiled from hint.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'substr', 'hint.tpl', 2, false),array('modifier', 'strlen', 'hint.tpl', 2, false),)), $this); ?>
<b><?php echo $this->_tpl_vars['term']['term']; ?>
</b><br />
<?php echo ((is_array($_tmp=$this->_tpl_vars['term']['definition'])) ? $this->_run_mod_handler('substr', true, $_tmp, 0, 300) : substr($_tmp, 0, 300)); ?>
<?php if (((is_array($_tmp=$this->_tpl_vars['term']['definition'])) ? $this->_run_mod_handler('strlen', true, $_tmp) : strlen($_tmp)) > 300): ?>...<?php endif; ?>
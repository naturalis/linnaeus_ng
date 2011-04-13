<?php /* Smarty version 2.6.26, created on 2011-04-12 17:20:20
         compiled from ../shared/_header-container.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'escape', '../shared/_header-container.tpl', 4, false),)), $this); ?>
<div id="header-container">
<?php if ($this->_tpl_vars['session']['project']['logo']): ?>
	<div id="image">
	<a href="<?php echo $this->_tpl_vars['session']['project']['urls']['project_start']; ?>
"><img src="<?php echo $this->_tpl_vars['session']['project']['urls']['project_media']; ?>
<?php echo ((is_array($_tmp=$this->_tpl_vars['session']['project']['logo'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'url') : smarty_modifier_escape($_tmp, 'url')); ?>
" id="project-logo" /></a>
	</div>
<?php endif; ?>
	<div id="title">
	<?php if (! $this->_tpl_vars['session']['project']['logo']): ?><a href="<?php echo $this->_tpl_vars['session']['project']['urls']['project_start']; ?>
"><?php echo $this->_tpl_vars['session']['project']['title']; ?>
</a><?php else: ?><?php echo $this->_tpl_vars['session']['project']['title']; ?>
<?php endif; ?>
	</div>
</div>
<?php /* Smarty version 2.6.26, created on 2011-03-10 10:53:16
         compiled from ../shared/_body-start.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'escape', '../shared/_body-start.tpl', 5, false),)), $this); ?>
<body id="body"><form method="post" action="" id="theForm" onsubmit="return checkForm();"><div id="body-container">
<div id="header-container">
	<div id="image">
<?php if ($this->_tpl_vars['session']['project']['logo']): ?>
	<a href="<?php echo $this->_tpl_vars['session']['project']['urls']['project_start']; ?>
"><img src="<?php echo $this->_tpl_vars['session']['project']['urls']['project_media']; ?>
<?php echo ((is_array($_tmp=$this->_tpl_vars['session']['project']['logo'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'url') : smarty_modifier_escape($_tmp, 'url')); ?>
" id="project-logo" /></a>
<?php endif; ?>
	</div>
	<div id="title">
	<?php echo $this->_tpl_vars['session']['project']['title']; ?>

	</div>
</div>
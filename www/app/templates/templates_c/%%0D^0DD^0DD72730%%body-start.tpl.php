<?php /* Smarty version 2.6.26, created on 2011-02-23 13:37:00
         compiled from ../shared/body-start.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'escape', '../shared/body-start.tpl', 4, false),)), $this); ?>
<body id="body"><div id="body-container">
<div id="header-container">
	<div id="image">
	<a href="<?php echo $this->_tpl_vars['session']['project']['urls']['project_start']; ?>
"><img src="<?php echo $this->_tpl_vars['session']['project']['urls']['project_media']; ?>
<?php echo ((is_array($_tmp=$this->_tpl_vars['session']['project']['logo'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'url') : smarty_modifier_escape($_tmp, 'url')); ?>
" id="project-logo" /></a>
	</div>
	<div id="title">
	<?php echo $this->_tpl_vars['session']['project']['title']; ?>

	</div>
</div>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "../shared/main-menu.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<div id="page-container">
<?php if ($this->_tpl_vars['headerTitles']): ?>
	<div id="header-titles">
		<span id="title"><?php echo $this->_tpl_vars['headerTitles']['title']; ?>
</span><br />
		<span id="subtitle"><?php echo $this->_tpl_vars['headerTitles']['subtitle']; ?>
</span>
	</div>
<?php endif; ?>
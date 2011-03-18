<?php /* Smarty version 2.6.26, created on 2011-03-17 15:14:49
         compiled from topic.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'escape', 'topic.tpl', 7, false),array('modifier', 'substr', 'topic.tpl', 17, false),array('block', 't', 'topic.tpl', 15, false),)), $this); ?>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "../shared/header.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "../shared/messages.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>

<div id="page-main">
	<div id="content">
		<?php if ($this->_tpl_vars['page']['image']['thumb_name']): ?>
		<img id="image-thumb" onclick="showMedia('<?php echo $this->_tpl_vars['session']['project']['urls']['project_media']; ?>
<?php echo ((is_array($_tmp=$this->_tpl_vars['page']['image']['file_name'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'url') : smarty_modifier_escape($_tmp, 'url')); ?>
','<?php echo $this->_tpl_vars['page']['topic']; ?>
')" src="<?php echo $this->_tpl_vars['session']['project']['urls']['project_thumbs']; ?>
<?php echo ((is_array($_tmp=$this->_tpl_vars['page']['image']['thumb_name'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'url') : smarty_modifier_escape($_tmp, 'url')); ?>
" />
		<?php elseif ($this->_tpl_vars['page']['image']['file_name']): ?>
		<img id="image-full" onclick="showMedia('<?php echo $this->_tpl_vars['session']['project']['urls']['project_media']; ?>
<?php echo ((is_array($_tmp=$this->_tpl_vars['page']['image']['file_name'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'url') : smarty_modifier_escape($_tmp, 'url')); ?>
','<?php echo $this->_tpl_vars['page']['topic']; ?>
')" src="<?php echo $this->_tpl_vars['session']['project']['urls']['project_media']; ?>
<?php echo ((is_array($_tmp=$this->_tpl_vars['page']['image']['file_name'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'url') : smarty_modifier_escape($_tmp, 'url')); ?>
" />
		<?php endif; ?>
		<?php echo $this->_tpl_vars['page']['content']; ?>

	</div>
	<div id="navigation">
	<?php if ($this->_tpl_vars['adjacentPages']['prev']): ?>
	<span onclick="goModuleTopic(<?php echo $this->_tpl_vars['adjacentPages']['prev']['page_id']; ?>
)" id="prev"><?php $this->_tag_stack[] = array('t', array()); $_block_repeat=true;$this->_plugins['block']['t'][0][0]->smartyTranslate($this->_tag_stack[count($this->_tag_stack)-1][1], null, $this, $_block_repeat);while ($_block_repeat) { ob_start(); ?>< previous<?php $_block_content = ob_get_contents(); ob_end_clean(); $_block_repeat=false;echo $this->_plugins['block']['t'][0][0]->smartyTranslate($this->_tag_stack[count($this->_tag_stack)-1][1], $_block_content, $this, $_block_repeat); }  array_pop($this->_tag_stack); ?></span>
	<?php endif; ?>
	<span id="back" onclick="goAlpha('<?php echo substr($this->_tpl_vars['page']['topic'], 0, 1); ?>
','index.php')"><?php $this->_tag_stack[] = array('t', array()); $_block_repeat=true;$this->_plugins['block']['t'][0][0]->smartyTranslate($this->_tag_stack[count($this->_tag_stack)-1][1], null, $this, $_block_repeat);while ($_block_repeat) { ob_start(); ?>back to index<?php $_block_content = ob_get_contents(); ob_end_clean(); $_block_repeat=false;echo $this->_plugins['block']['t'][0][0]->smartyTranslate($this->_tag_stack[count($this->_tag_stack)-1][1], $_block_content, $this, $_block_repeat); }  array_pop($this->_tag_stack); ?></span>
	<?php if ($this->_tpl_vars['adjacentPages']['next']): ?>
	<span onclick="goModuleTopic(<?php echo $this->_tpl_vars['adjacentPages']['next']['page_id']; ?>
)" id="next"><?php $this->_tag_stack[] = array('t', array()); $_block_repeat=true;$this->_plugins['block']['t'][0][0]->smartyTranslate($this->_tag_stack[count($this->_tag_stack)-1][1], null, $this, $_block_repeat);while ($_block_repeat) { ob_start(); ?>next ><?php $_block_content = ob_get_contents(); ob_end_clean(); $_block_repeat=false;echo $this->_plugins['block']['t'][0][0]->smartyTranslate($this->_tag_stack[count($this->_tag_stack)-1][1], $_block_content, $this, $_block_repeat); }  array_pop($this->_tag_stack); ?></span>
	<?php endif; ?>
	</div>
</div>

<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "../shared/footer.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
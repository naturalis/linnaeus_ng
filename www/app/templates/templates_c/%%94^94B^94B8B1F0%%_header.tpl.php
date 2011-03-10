<?php /* Smarty version 2.6.26, created on 2011-03-10 10:53:16
         compiled from _header.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('block', 't', '_header.tpl', 2, false),)), $this); ?>
<div id="matrix-header">
	<div id="current"><?php $this->_tag_stack[] = array('t', array('_s1' => $this->_tpl_vars['matrix']['name'])); $_block_repeat=true;$this->_plugins['block']['t'][0][0]->smartyTranslate($this->_tag_stack[count($this->_tag_stack)-1][1], null, $this, $_block_repeat);while ($_block_repeat) { ob_start(); ?>Using matrix "%s".<?php $_block_content = ob_get_contents(); ob_end_clean(); $_block_repeat=false;echo $this->_plugins['block']['t'][0][0]->smartyTranslate($this->_tag_stack[count($this->_tag_stack)-1][1], $_block_content, $this, $_block_repeat); }  array_pop($this->_tag_stack); ?></div>
	<?php if ($this->_tpl_vars['matrixCount'] > 1): ?><div id="switch"><?php $this->_tag_stack[] = array('t', array('_s1' => '<a href="matrices.php">','_s2' => '</a>')); $_block_repeat=true;$this->_plugins['block']['t'][0][0]->smartyTranslate($this->_tag_stack[count($this->_tag_stack)-1][1], null, $this, $_block_repeat);while ($_block_repeat) { ob_start(); ?>(Click %shere%s to change to another matrix)<?php $_block_content = ob_get_contents(); ob_end_clean(); $_block_repeat=false;echo $this->_plugins['block']['t'][0][0]->smartyTranslate($this->_tag_stack[count($this->_tag_stack)-1][1], $_block_content, $this, $_block_repeat); }  array_pop($this->_tag_stack); ?></div><?php endif; ?>
</div>


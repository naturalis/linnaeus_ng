<?php /* Smarty version 2.6.26, created on 2011-02-23 13:37:00
         compiled from ../shared/main-menu.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('block', 't', '../shared/main-menu.tpl', 6, false),)), $this); ?>
<div id="menu-container">
	<div id="main-menu">
<?php $_from = $this->_tpl_vars['menu']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['k'] => $this->_tpl_vars['v']):
?>
<?php if ($this->_tpl_vars['v']['type'] == 'regular' && $this->_tpl_vars['v']['module'] != 'Introduction'): ?>
<?php if ($this->_tpl_vars['v']['controller'] == $this->_tpl_vars['controllerBaseName']): ?>
<span class="menu-item-active"><?php $this->_tag_stack[] = array('t', array()); $_block_repeat=true;$this->_plugins['block']['t'][0][0]->smartyTranslate($this->_tag_stack[count($this->_tag_stack)-1][1], null, $this, $_block_repeat);while ($_block_repeat) { ob_start(); ?><?php echo $this->_tpl_vars['v']['module']; ?>
<?php $_block_content = ob_get_contents(); ob_end_clean(); $_block_repeat=false;echo $this->_plugins['block']['t'][0][0]->smartyTranslate($this->_tag_stack[count($this->_tag_stack)-1][1], $_block_content, $this, $_block_repeat); }  array_pop($this->_tag_stack); ?></span>
<?php else: ?>
<a class="menu-item" href="../<?php echo $this->_tpl_vars['v']['controller']; ?>
/"><?php $this->_tag_stack[] = array('t', array()); $_block_repeat=true;$this->_plugins['block']['t'][0][0]->smartyTranslate($this->_tag_stack[count($this->_tag_stack)-1][1], null, $this, $_block_repeat);while ($_block_repeat) { ob_start(); ?><?php echo $this->_tpl_vars['v']['module']; ?>
<?php $_block_content = ob_get_contents(); ob_end_clean(); $_block_repeat=false;echo $this->_plugins['block']['t'][0][0]->smartyTranslate($this->_tag_stack[count($this->_tag_stack)-1][1], $_block_content, $this, $_block_repeat); }  array_pop($this->_tag_stack); ?></a>
<?php endif; ?>
<?php elseif ($this->_tpl_vars['v']['module'] != 'Introduction'): ?>
<a class="menu-item" href="../linnaeus/mod.php?i=<?php echo $this->_tpl_vars['v']['id']; ?>
"><?php $this->_tag_stack[] = array('t', array()); $_block_repeat=true;$this->_plugins['block']['t'][0][0]->smartyTranslate($this->_tag_stack[count($this->_tag_stack)-1][1], null, $this, $_block_repeat);while ($_block_repeat) { ob_start(); ?><?php echo $this->_tpl_vars['v']['module']; ?>
<?php $_block_content = ob_get_contents(); ob_end_clean(); $_block_repeat=false;echo $this->_plugins['block']['t'][0][0]->smartyTranslate($this->_tag_stack[count($this->_tag_stack)-1][1], $_block_content, $this, $_block_repeat); }  array_pop($this->_tag_stack); ?></a>
<?php endif; ?>
<?php endforeach; endif; unset($_from); ?>
	</div>
	<div id="language-change">
	<form action="" method="post" id="languageForm">
	<select name="uiLanguage" onChange="$('#languageForm').submit();">
<?php $_from = $this->_tpl_vars['languages']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['k'] => $this->_tpl_vars['v']):
?>
		<option value="<?php echo $this->_tpl_vars['v']['language_id']; ?>
"<?php if ($this->_tpl_vars['v']['language_id'] == $this->_tpl_vars['currentLanguageId']): ?> selected="selected"<?php endif; ?>><?php echo $this->_tpl_vars['v']['language']; ?>
 <?php if ($this->_tpl_vars['v']['def_language'] == 1): ?>*<?php endif; ?></option>
<?php endforeach; endif; unset($_from); ?>
	</select>
	</form>
	</div>
</div>
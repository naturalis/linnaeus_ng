<?php /* Smarty version 2.6.26, created on 2011-04-13 15:05:40
         compiled from C:/Users/maarten/htdocs/linnaeus+ng/linnaeus_ng/www/app/templates/templates/shared/0064/_main-menu.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('block', 't', 'C:/Users/maarten/htdocs/linnaeus ng/linnaeus_ng/www/app/templates/templates/shared/0064/_main-menu.tpl', 8, false),)), $this); ?>
<div id="menu-container">
	<div id="main-menu">
<?php $this->assign('first', true); ?>
<?php $_from = $this->_tpl_vars['menu']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['k'] => $this->_tpl_vars['v']):
?>
<?php if ($this->_tpl_vars['v']['type'] == 'regular' && $this->_tpl_vars['v']['module'] != 'Introduction'): ?>
<?php if ($this->_tpl_vars['v']['controller'] == $this->_tpl_vars['controllerBaseName']): ?>
<div class="menu-item-container-active">
<span class="menu-active-indicator"><a class="menu-item-active" href="../<?php echo $this->_tpl_vars['v']['controller']; ?>
/"><?php $this->_tag_stack[] = array('t', array()); $_block_repeat=true;$this->_plugins['block']['t'][0][0]->smartyTranslate($this->_tag_stack[count($this->_tag_stack)-1][1], null, $this, $_block_repeat);while ($_block_repeat) { ob_start(); ?><?php echo $this->_tpl_vars['v']['module']; ?>
<?php $_block_content = ob_get_contents(); ob_end_clean(); $_block_repeat=false;echo $this->_plugins['block']['t'][0][0]->smartyTranslate($this->_tag_stack[count($this->_tag_stack)-1][1], $_block_content, $this, $_block_repeat); }  array_pop($this->_tag_stack); ?></a></span><br />
</div>
<?php $this->assign('first', false); ?>
<?php else: ?>
<div class="menu-item-container">
<a class="menu-item" href="../<?php echo $this->_tpl_vars['v']['controller']; ?>
/"><?php $this->_tag_stack[] = array('t', array()); $_block_repeat=true;$this->_plugins['block']['t'][0][0]->smartyTranslate($this->_tag_stack[count($this->_tag_stack)-1][1], null, $this, $_block_repeat);while ($_block_repeat) { ob_start(); ?><?php echo $this->_tpl_vars['v']['module']; ?>
<?php $_block_content = ob_get_contents(); ob_end_clean(); $_block_repeat=false;echo $this->_plugins['block']['t'][0][0]->smartyTranslate($this->_tag_stack[count($this->_tag_stack)-1][1], $_block_content, $this, $_block_repeat); }  array_pop($this->_tag_stack); ?></a><br />
</div>
<?php $this->assign('first', false); ?>
<?php endif; ?>
<?php elseif ($this->_tpl_vars['v']['module'] != 'Introduction'): ?>
<?php if (! $this->_tpl_vars['first']): ?><span class="menu-separator">|</span><?php endif; ?>
<?php if ($this->_tpl_vars['v']['id'] == $this->_tpl_vars['module']['id']): ?>
<div class="menu-item-container-active">
<span class="menu-active-indicator"><span class="menu-item-active" onclick="goMenuModule(<?php echo $this->_tpl_vars['v']['id']; ?>
);"><?php $this->_tag_stack[] = array('t', array()); $_block_repeat=true;$this->_plugins['block']['t'][0][0]->smartyTranslate($this->_tag_stack[count($this->_tag_stack)-1][1], null, $this, $_block_repeat);while ($_block_repeat) { ob_start(); ?><?php echo $this->_tpl_vars['v']['module']; ?>
<?php $_block_content = ob_get_contents(); ob_end_clean(); $_block_repeat=false;echo $this->_plugins['block']['t'][0][0]->smartyTranslate($this->_tag_stack[count($this->_tag_stack)-1][1], $_block_content, $this, $_block_repeat); }  array_pop($this->_tag_stack); ?></span></span><br />
</div>
<?php $this->assign('first', false); ?>
<?php else: ?>
<div class="menu-item-container">
<span class="menu-item" onclick="goMenuModule(<?php echo $this->_tpl_vars['v']['id']; ?>
);"><?php $this->_tag_stack[] = array('t', array()); $_block_repeat=true;$this->_plugins['block']['t'][0][0]->smartyTranslate($this->_tag_stack[count($this->_tag_stack)-1][1], null, $this, $_block_repeat);while ($_block_repeat) { ob_start(); ?><?php echo $this->_tpl_vars['v']['module']; ?>
<?php $_block_content = ob_get_contents(); ob_end_clean(); $_block_repeat=false;echo $this->_plugins['block']['t'][0][0]->smartyTranslate($this->_tag_stack[count($this->_tag_stack)-1][1], $_block_content, $this, $_block_repeat); }  array_pop($this->_tag_stack); ?></span><br />
</div>
<?php $this->assign('first', false); ?>
<?php endif; ?>
<?php endif; ?>
<?php endforeach; endif; unset($_from); ?>
	</div>
	<div id="language-change">
		<input
			type="text"
			name="search"
			id="search"
			class="search-input-shaded"
			value="<?php if ($this->_tpl_vars['search']): ?><?php echo $this->_tpl_vars['search']; ?>
<?php else: ?><?php $this->_tag_stack[] = array('t', array()); $_block_repeat=true;$this->_plugins['block']['t'][0][0]->smartyTranslate($this->_tag_stack[count($this->_tag_stack)-1][1], null, $this, $_block_repeat);while ($_block_repeat) { ob_start(); ?>enter search term<?php $_block_content = ob_get_contents(); ob_end_clean(); $_block_repeat=false;echo $this->_plugins['block']['t'][0][0]->smartyTranslate($this->_tag_stack[count($this->_tag_stack)-1][1], $_block_content, $this, $_block_repeat); }  array_pop($this->_tag_stack); ?><?php endif; ?>"
			onkeydown="setSearchKeyed(true);"
			onblur="setSearchKeyed(false);"
			onfocus="onSearchBoxSelect()" />
			<img src="../../media/system/search.gif" onclick="doSearch();" />
		<select id="languageSelect" onchange="doLanguageChange()">
	<?php $_from = $this->_tpl_vars['languages']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['k'] => $this->_tpl_vars['v']):
?>
			<option value="<?php echo $this->_tpl_vars['v']['language_id']; ?>
"<?php if ($this->_tpl_vars['v']['language_id'] == $this->_tpl_vars['currentLanguageId']): ?> selected="selected"<?php endif; ?>><?php echo $this->_tpl_vars['v']['language']; ?>
 <?php if ($this->_tpl_vars['v']['def_language'] == 1): ?>*<?php endif; ?></option>
	<?php endforeach; endif; unset($_from); ?>
		</select>
	</div>
</div>
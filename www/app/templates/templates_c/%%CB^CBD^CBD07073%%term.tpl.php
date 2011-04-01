<?php /* Smarty version 2.6.26, created on 2011-04-01 12:10:26
         compiled from term.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('block', 't', 'term.tpl', 21, false),array('modifier', 'escape', 'term.tpl', 36, false),array('modifier', 'substr', 'term.tpl', 53, false),)), $this); ?>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "../shared/header.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>

<?php if ($this->_tpl_vars['alpha']): ?>
<div id="alphabet">
	<?php $_from = $this->_tpl_vars['alpha']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['k'] => $this->_tpl_vars['v']):
?>
	<?php if ($this->_tpl_vars['letter'] == $this->_tpl_vars['v']): ?>
	<span class="letter-active"><?php echo $this->_tpl_vars['v']; ?>
</span>
	<?php else: ?>
	<span class="letter" onclick="goAlpha('<?php echo $this->_tpl_vars['v']; ?>
','index.php')"><?php echo $this->_tpl_vars['v']; ?>
</span>
	<?php endif; ?>
	<?php endforeach; endif; unset($_from); ?>
</div>
<?php endif; ?>

<div id="page-main">
	<div id="term"><?php echo $this->_tpl_vars['term']['term']; ?>
</div>
	<div id="defintion"><?php echo $this->_tpl_vars['term']['definition']; ?>
</div>
	<?php if ($this->_tpl_vars['term']['synonyms']): ?>
	
	<div id="synonyms">
		<div id="synonyms-title"><?php $this->_tag_stack[] = array('t', array()); $_block_repeat=true;$this->_plugins['block']['t'][0][0]->smartyTranslate($this->_tag_stack[count($this->_tag_stack)-1][1], null, $this, $_block_repeat);while ($_block_repeat) { ob_start(); ?>Synonyms<?php $_block_content = ob_get_contents(); ob_end_clean(); $_block_repeat=false;echo $this->_plugins['block']['t'][0][0]->smartyTranslate($this->_tag_stack[count($this->_tag_stack)-1][1], $_block_content, $this, $_block_repeat); }  array_pop($this->_tag_stack); ?></div>
		<?php $_from = $this->_tpl_vars['term']['synonyms']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['k'] => $this->_tpl_vars['v']):
?>
			<div class="synonym"><?php echo $this->_tpl_vars['v']['synonym']; ?>
 (<?php echo $this->_tpl_vars['v']['language']; ?>
)</div>
		<?php endforeach; endif; unset($_from); ?>
	</div>
	<?php endif; ?>
	
	<?php if ($this->_tpl_vars['term']['media']): ?>
	<div id="media">
		<div id="media-title"><?php $this->_tag_stack[] = array('t', array()); $_block_repeat=true;$this->_plugins['block']['t'][0][0]->smartyTranslate($this->_tag_stack[count($this->_tag_stack)-1][1], null, $this, $_block_repeat);while ($_block_repeat) { ob_start(); ?>Images<?php $_block_content = ob_get_contents(); ob_end_clean(); $_block_repeat=false;echo $this->_plugins['block']['t'][0][0]->smartyTranslate($this->_tag_stack[count($this->_tag_stack)-1][1], $_block_content, $this, $_block_repeat); }  array_pop($this->_tag_stack); ?></div>
	<?php $_from = $this->_tpl_vars['term']['media']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['k'] => $this->_tpl_vars['v']):
?>
		<div class="image-cell">
		<?php if ($this->_tpl_vars['v']['thumb_name']): ?>
			<img
				class="image-thumb"
				onclick="showMedia('<?php echo $this->_tpl_vars['session']['project']['urls']['project_media']; ?>
<?php echo ((is_array($_tmp=$this->_tpl_vars['v']['file_name'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'url') : smarty_modifier_escape($_tmp, 'url')); ?>
','<?php echo $this->_tpl_vars['v']['original_name']; ?>
');" 
				src="<?php echo $this->_tpl_vars['session']['project']['urls']['project_thumbs']; ?>
<?php echo ((is_array($_tmp=$this->_tpl_vars['v']['thumb_name'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'url') : smarty_modifier_escape($_tmp, 'url')); ?>
" />
		<?php else: ?>
			<img
				class="image-full"
				onclick="showMedia('<?php echo $this->_tpl_vars['session']['project']['urls']['project_media']; ?>
<?php echo ((is_array($_tmp=$this->_tpl_vars['v']['file_name'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'url') : smarty_modifier_escape($_tmp, 'url')); ?>
','<?php echo $this->_tpl_vars['v']['original_name']; ?>
');" 
				src="<?php echo $this->_tpl_vars['session']['project']['urls']['project_media']; ?>
<?php echo ((is_array($_tmp=$this->_tpl_vars['v']['file_name'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'url') : smarty_modifier_escape($_tmp, 'url')); ?>
" />
		<?php endif; ?>
		</div>
	<?php endforeach; endif; unset($_from); ?>
	</div>
	<?php endif; ?>

	<div id="navigation">
		<?php if ($this->_tpl_vars['adjacentTerms']['prev']): ?>
		<span onclick="goGlossaryTerm(<?php echo $this->_tpl_vars['adjacentTerms']['prev']['id']; ?>
)" id="prev"><?php $this->_tag_stack[] = array('t', array()); $_block_repeat=true;$this->_plugins['block']['t'][0][0]->smartyTranslate($this->_tag_stack[count($this->_tag_stack)-1][1], null, $this, $_block_repeat);while ($_block_repeat) { ob_start(); ?>< previous<?php $_block_content = ob_get_contents(); ob_end_clean(); $_block_repeat=false;echo $this->_plugins['block']['t'][0][0]->smartyTranslate($this->_tag_stack[count($this->_tag_stack)-1][1], $_block_content, $this, $_block_repeat); }  array_pop($this->_tag_stack); ?></span>
		<?php endif; ?>
		<span id="back" onclick="goAlpha('<?php echo substr($this->_tpl_vars['term']['term'], 0, 1); ?>
','index.php')"><?php $this->_tag_stack[] = array('t', array()); $_block_repeat=true;$this->_plugins['block']['t'][0][0]->smartyTranslate($this->_tag_stack[count($this->_tag_stack)-1][1], null, $this, $_block_repeat);while ($_block_repeat) { ob_start(); ?>back to index<?php $_block_content = ob_get_contents(); ob_end_clean(); $_block_repeat=false;echo $this->_plugins['block']['t'][0][0]->smartyTranslate($this->_tag_stack[count($this->_tag_stack)-1][1], $_block_content, $this, $_block_repeat); }  array_pop($this->_tag_stack); ?></span>
		<?php if ($this->_tpl_vars['adjacentTerms']['next']): ?>
		<span onclick="goGlossaryTerm(<?php echo $this->_tpl_vars['adjacentTerms']['next']['id']; ?>
)" id="next"><?php $this->_tag_stack[] = array('t', array()); $_block_repeat=true;$this->_plugins['block']['t'][0][0]->smartyTranslate($this->_tag_stack[count($this->_tag_stack)-1][1], null, $this, $_block_repeat);while ($_block_repeat) { ob_start(); ?>next ><?php $_block_content = ob_get_contents(); ob_end_clean(); $_block_repeat=false;echo $this->_plugins['block']['t'][0][0]->smartyTranslate($this->_tag_stack[count($this->_tag_stack)-1][1], $_block_content, $this, $_block_repeat); }  array_pop($this->_tag_stack); ?></span>
		<?php endif; ?>
	</div>
</div>

<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "../shared/footer.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
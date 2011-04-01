<?php /* Smarty version 2.6.26, created on 2011-04-01 12:05:23
         compiled from reference.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('block', 't', 'reference.tpl', 27, false),array('modifier', 'substr', 'reference.tpl', 50, false),)), $this); ?>
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
	<div id="reference">
		<div id="author">
			<span id="name">
				<?php echo $this->_tpl_vars['ref']['author_full']; ?>

			</span>
			<span id="year"><?php echo $this->_tpl_vars['ref']['year']; ?>
</span>
		</div>
		<div id="text"><?php echo $this->_tpl_vars['ref']['text']; ?>
</div>

	<?php if ($this->_tpl_vars['ref']['taxa']): ?>
		<div id="taxa">
			<div class="title"><?php $this->_tag_stack[] = array('t', array()); $_block_repeat=true;$this->_plugins['block']['t'][0][0]->smartyTranslate($this->_tag_stack[count($this->_tag_stack)-1][1], null, $this, $_block_repeat);while ($_block_repeat) { ob_start(); ?>Referenced in the following taxa:<?php $_block_content = ob_get_contents(); ob_end_clean(); $_block_repeat=false;echo $this->_plugins['block']['t'][0][0]->smartyTranslate($this->_tag_stack[count($this->_tag_stack)-1][1], $_block_content, $this, $_block_repeat); }  array_pop($this->_tag_stack); ?></div>
		<?php $_from = $this->_tpl_vars['ref']['taxa']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['k'] => $this->_tpl_vars['v']):
?>
			<div>
				<span class="a" onclick="goTaxon(<?php echo $this->_tpl_vars['v']['taxon']['id']; ?>
)"><?php echo $this->_tpl_vars['v']['taxon']['taxon']; ?>
</span>
				<?php if ($this->_tpl_vars['v']['taxon']['is_hybrid'] == 1): ?><span class="hybrid-marker" title="<?php $this->_tag_stack[] = array('t', array()); $_block_repeat=true;$this->_plugins['block']['t'][0][0]->smartyTranslate($this->_tag_stack[count($this->_tag_stack)-1][1], null, $this, $_block_repeat);while ($_block_repeat) { ob_start(); ?>hybrid<?php $_block_content = ob_get_contents(); ob_end_clean(); $_block_repeat=false;echo $this->_plugins['block']['t'][0][0]->smartyTranslate($this->_tag_stack[count($this->_tag_stack)-1][1], $_block_content, $this, $_block_repeat); }  array_pop($this->_tag_stack); ?>"><?php echo $this->_tpl_vars['session']['project']['hybrid_marker']; ?>
</span><?php endif; ?>
			</div>
		<?php endforeach; endif; unset($_from); ?>
		</div>
		
	<?php endif; ?>

	<?php if ($this->_tpl_vars['ref']['synonyms']): ?>
		<div id="synonyms">
			<div class="title"><?php $this->_tag_stack[] = array('t', array()); $_block_repeat=true;$this->_plugins['block']['t'][0][0]->smartyTranslate($this->_tag_stack[count($this->_tag_stack)-1][1], null, $this, $_block_repeat);while ($_block_repeat) { ob_start(); ?>Referenced in the following synonyms:<?php $_block_content = ob_get_contents(); ob_end_clean(); $_block_repeat=false;echo $this->_plugins['block']['t'][0][0]->smartyTranslate($this->_tag_stack[count($this->_tag_stack)-1][1], $_block_content, $this, $_block_repeat); }  array_pop($this->_tag_stack); ?></div>
		<?php $_from = $this->_tpl_vars['ref']['synonyms']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['k'] => $this->_tpl_vars['v']):
?>
			<div><span class="a" onclick="goTaxon(<?php echo $this->_tpl_vars['v']['taxon_id']; ?>
,'names')"><?php echo $this->_tpl_vars['v']['synonym']; ?>
</span></div>
		<?php endforeach; endif; unset($_from); ?>
		</div>
	<?php endif; ?>

	</div>

	<div id="navigation">
		<span id="back" onclick="goAlpha('<?php echo substr($this->_tpl_vars['ref']['author_first'], 0, 1); ?>
','index.php')"><?php $this->_tag_stack[] = array('t', array()); $_block_repeat=true;$this->_plugins['block']['t'][0][0]->smartyTranslate($this->_tag_stack[count($this->_tag_stack)-1][1], null, $this, $_block_repeat);while ($_block_repeat) { ob_start(); ?>back to index<?php $_block_content = ob_get_contents(); ob_end_clean(); $_block_repeat=false;echo $this->_plugins['block']['t'][0][0]->smartyTranslate($this->_tag_stack[count($this->_tag_stack)-1][1], $_block_content, $this, $_block_repeat); }  array_pop($this->_tag_stack); ?></span>
	</div>
</div>

<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "../shared/footer.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
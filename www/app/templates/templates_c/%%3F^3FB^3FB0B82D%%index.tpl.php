<?php /* Smarty version 2.6.26, created on 2011-03-29 11:21:50
         compiled from ../species/index.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('block', 't', '../species/index.tpl', 10, false),)), $this); ?>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "../shared/header.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>

<div id="page-main">
	<div id="index">
		<table>
		<?php $_from = $this->_tpl_vars['taxa']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }$this->_foreach['taxonloop'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['taxonloop']['total'] > 0):
    foreach ($_from as $this->_tpl_vars['k'] => $this->_tpl_vars['v']):
        $this->_foreach['taxonloop']['iteration']++;
?>
		<tr class="highlight">
			<td class="a" onclick="goTaxon(<?php echo $this->_tpl_vars['v']['id']; ?>
)">
				<?php echo $this->_tpl_vars['v']['taxon']; ?>

				<?php if ($this->_tpl_vars['v']['is_hybrid'] == 1): ?><span class="hybrid-marker" title="<?php $this->_tag_stack[] = array('t', array()); $_block_repeat=true;$this->_plugins['block']['t'][0][0]->smartyTranslate($this->_tag_stack[count($this->_tag_stack)-1][1], null, $this, $_block_repeat);while ($_block_repeat) { ob_start(); ?>hybrid<?php $_block_content = ob_get_contents(); ob_end_clean(); $_block_repeat=false;echo $this->_plugins['block']['t'][0][0]->smartyTranslate($this->_tag_stack[count($this->_tag_stack)-1][1], $_block_content, $this, $_block_repeat); }  array_pop($this->_tag_stack); ?>"><?php echo $this->_tpl_vars['session']['project']['hybrid_marker']; ?>
</span><?php endif; ?>
			</td>
			<td>(<?php echo $this->_tpl_vars['v']['rank']; ?>
)</td>
		</tr>
		<?php endforeach; endif; unset($_from); ?>
		</table>
	</div>
<?php if ($this->_tpl_vars['prevStart'] != -1 || $this->_tpl_vars['nextStart'] != -1): ?>
	<div id="navigation">
		<?php if ($this->_tpl_vars['prevStart'] != -1): ?>
		<span class="a" onclick="goNavigate(<?php echo $this->_tpl_vars['prevStart']; ?>
);">< <?php $this->_tag_stack[] = array('t', array()); $_block_repeat=true;$this->_plugins['block']['t'][0][0]->smartyTranslate($this->_tag_stack[count($this->_tag_stack)-1][1], null, $this, $_block_repeat);while ($_block_repeat) { ob_start(); ?>previous<?php $_block_content = ob_get_contents(); ob_end_clean(); $_block_repeat=false;echo $this->_plugins['block']['t'][0][0]->smartyTranslate($this->_tag_stack[count($this->_tag_stack)-1][1], $_block_content, $this, $_block_repeat); }  array_pop($this->_tag_stack); ?></span>
		<?php endif; ?>
		<?php if ($this->_tpl_vars['nextStart'] != -1): ?>
		<span class="a" onclick="goNavigate(<?php echo $this->_tpl_vars['nextStart']; ?>
);"><?php $this->_tag_stack[] = array('t', array()); $_block_repeat=true;$this->_plugins['block']['t'][0][0]->smartyTranslate($this->_tag_stack[count($this->_tag_stack)-1][1], null, $this, $_block_repeat);while ($_block_repeat) { ob_start(); ?>next<?php $_block_content = ob_get_contents(); ob_end_clean(); $_block_repeat=false;echo $this->_plugins['block']['t'][0][0]->smartyTranslate($this->_tag_stack[count($this->_tag_stack)-1][1], $_block_content, $this, $_block_repeat); }  array_pop($this->_tag_stack); ?> ></span>
		<?php endif; ?>
	</div>
<?php endif; ?>
</div>

<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "../shared/footer.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
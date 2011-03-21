<?php /* Smarty version 2.6.26, created on 2011-03-21 17:53:43
         compiled from index.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('block', 't', 'index.tpl', 20, false),array('modifier', 'count', 'index.tpl', 30, false),)), $this); ?>
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
')"><?php echo $this->_tpl_vars['v']; ?>
</span>
	<?php endif; ?>
	<?php endforeach; endif; unset($_from); ?>
</div>
<?php endif; ?>

<div id="page-main">
<?php if ($this->_tpl_vars['gloss']): ?>
<table id="index">
<thead> 
	<tr>
		<th id="th-term"><?php $this->_tag_stack[] = array('t', array()); $_block_repeat=true;$this->_plugins['block']['t'][0][0]->smartyTranslate($this->_tag_stack[count($this->_tag_stack)-1][1], null, $this, $_block_repeat);while ($_block_repeat) { ob_start(); ?>term<?php $_block_content = ob_get_contents(); ob_end_clean(); $_block_repeat=false;echo $this->_plugins['block']['t'][0][0]->smartyTranslate($this->_tag_stack[count($this->_tag_stack)-1][1], $_block_content, $this, $_block_repeat); }  array_pop($this->_tag_stack); ?></th>
		<th id="th-definition"><?php $this->_tag_stack[] = array('t', array()); $_block_repeat=true;$this->_plugins['block']['t'][0][0]->smartyTranslate($this->_tag_stack[count($this->_tag_stack)-1][1], null, $this, $_block_repeat);while ($_block_repeat) { ob_start(); ?>synonyms<?php $_block_content = ob_get_contents(); ob_end_clean(); $_block_repeat=false;echo $this->_plugins['block']['t'][0][0]->smartyTranslate($this->_tag_stack[count($this->_tag_stack)-1][1], $_block_content, $this, $_block_repeat); }  array_pop($this->_tag_stack); ?></th>
	</tr>
</thead>
<tbody>
<?php $_from = $this->_tpl_vars['gloss']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['k'] => $this->_tpl_vars['v']):
?>
	<tr class="highlight">
		<td class="a" onclick="goGlossaryTerm(<?php echo $this->_tpl_vars['v']['id']; ?>
)"><?php echo $this->_tpl_vars['v']['term']; ?>
</td>
		<td>
		<?php $_from = $this->_tpl_vars['v']['synonyms']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['k2'] => $this->_tpl_vars['v2']):
?>
			<?php echo $this->_tpl_vars['v2']['synonym']; ?>
<?php if (count($this->_tpl_vars['v']['synonyms']) > 1 && $this->_tpl_vars['k2'] != count($this->_tpl_vars['v']['synonyms']) -1): ?>, <?php endif; ?>
		<?php endforeach; endif; unset($_from); ?>
		</td>
	</tr>
<?php endforeach; endif; unset($_from); ?>
</tbody>
</table>
<?php else: ?>
<?php $this->_tag_stack[] = array('t', array()); $_block_repeat=true;$this->_plugins['block']['t'][0][0]->smartyTranslate($this->_tag_stack[count($this->_tag_stack)-1][1], null, $this, $_block_repeat);while ($_block_repeat) { ob_start(); ?>No glossary terms have been defined in this language.<?php $_block_content = ob_get_contents(); ob_end_clean(); $_block_repeat=false;echo $this->_plugins['block']['t'][0][0]->smartyTranslate($this->_tag_stack[count($this->_tag_stack)-1][1], $_block_content, $this, $_block_repeat); }  array_pop($this->_tag_stack); ?>
<?php endif; ?>
</div>

<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "../shared/footer.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
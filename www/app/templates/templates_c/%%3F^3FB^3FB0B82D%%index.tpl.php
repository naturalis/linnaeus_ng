<?php /* Smarty version 2.6.26, created on 2011-03-05 17:49:52
         compiled from ../species/index.tpl */ ?>
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
)"><?php echo $this->_tpl_vars['v']['taxon']; ?>
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
);">< previous</span>
		<?php endif; ?>
		<?php if ($this->_tpl_vars['nextStart'] != -1): ?>
		<span class="a" onclick="goNavigate(<?php echo $this->_tpl_vars['nextStart']; ?>
);">next ></span>
		<?php endif; ?>
	</div>
<?php endif; ?>
</div>

<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "../shared/footer.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
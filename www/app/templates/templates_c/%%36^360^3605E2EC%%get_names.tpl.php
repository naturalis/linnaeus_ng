<?php /* Smarty version 2.6.26, created on 2011-04-13 12:57:46
         compiled from get_names.tpl */ ?>
<pre>
<?php $_from = $this->_tpl_vars['taxa']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['v']):
?>
accepted	<?php echo $this->_tpl_vars['v']['id']; ?>
	<?php echo $this->_tpl_vars['v']['taxon']; ?>
	<?php echo $this->_tpl_vars['ranks'][$this->_tpl_vars['v']['rank_id']]['rank']; ?>

<?php endforeach; endif; unset($_from); ?>
<?php $_from = $this->_tpl_vars['common']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['v']):
?>
<?php $this->assign('d', $this->_tpl_vars['taxa'][$this->_tpl_vars['v']['taxon_id']]); ?>
common	<?php echo $this->_tpl_vars['v']['id']; ?>
	<?php echo $this->_tpl_vars['v']['commonname']; ?>
	<?php echo $this->_tpl_vars['v']['taxon_id']; ?>
	<?php echo $this->_tpl_vars['taxa'][$this->_tpl_vars['v']['taxon_id']]['taxon']; ?>
	<?php echo $this->_tpl_vars['lang'][$this->_tpl_vars['v']['language_id']]['language']; ?>
	<?php echo $this->_tpl_vars['ranks'][$this->_tpl_vars['d']['rank_id']]['rank']; ?>

<?php endforeach; endif; unset($_from); ?>
<?php $_from = $this->_tpl_vars['synonyms']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['v']):
?>
<?php $this->assign('d', $this->_tpl_vars['taxa'][$this->_tpl_vars['v']['taxon_id']]); ?>
synonym	<?php echo $this->_tpl_vars['v']['id']; ?>
	<?php echo $this->_tpl_vars['v']['synonym']; ?>
	<?php echo $this->_tpl_vars['v']['remark']; ?>
	<?php echo $this->_tpl_vars['v']['taxon_id']; ?>
	<?php echo $this->_tpl_vars['taxa'][$this->_tpl_vars['v']['taxon_id']]['taxon']; ?>
	<?php echo $this->_tpl_vars['ranks'][$this->_tpl_vars['d']['rank_id']]['rank']; ?>

<?php endforeach; endif; unset($_from); ?>



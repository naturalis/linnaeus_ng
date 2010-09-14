<?php /* Smarty version 2.6.26, created on 2010-09-13 18:33:16
         compiled from page.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'count', 'page.tpl', 43, false),)), $this); ?>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "../shared/admin-header.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>

<div id="page-main">


<span id="message-container" style="float:right;"></span>
<br />

<div id="taxon-pages-table-div">
<table id="taxon-pages-table">
	<tr>
		<td style="width:150px"></td>
<?php unset($this->_sections['i']);
$this->_sections['i']['name'] = 'i';
$this->_sections['i']['loop'] = is_array($_loop=$this->_tpl_vars['languages']) ? count($_loop) : max(0, (int)$_loop); unset($_loop);
$this->_sections['i']['show'] = true;
$this->_sections['i']['max'] = $this->_sections['i']['loop'];
$this->_sections['i']['step'] = 1;
$this->_sections['i']['start'] = $this->_sections['i']['step'] > 0 ? 0 : $this->_sections['i']['loop']-1;
if ($this->_sections['i']['show']) {
    $this->_sections['i']['total'] = $this->_sections['i']['loop'];
    if ($this->_sections['i']['total'] == 0)
        $this->_sections['i']['show'] = false;
} else
    $this->_sections['i']['total'] = 0;
if ($this->_sections['i']['show']):

            for ($this->_sections['i']['index'] = $this->_sections['i']['start'], $this->_sections['i']['iteration'] = 1;
                 $this->_sections['i']['iteration'] <= $this->_sections['i']['total'];
                 $this->_sections['i']['index'] += $this->_sections['i']['step'], $this->_sections['i']['iteration']++):
$this->_sections['i']['rownum'] = $this->_sections['i']['iteration'];
$this->_sections['i']['index_prev'] = $this->_sections['i']['index'] - $this->_sections['i']['step'];
$this->_sections['i']['index_next'] = $this->_sections['i']['index'] + $this->_sections['i']['step'];
$this->_sections['i']['first']      = ($this->_sections['i']['iteration'] == 1);
$this->_sections['i']['last']       = ($this->_sections['i']['iteration'] == $this->_sections['i']['total']);
?>
		<td>
			<?php if ($this->_tpl_vars['languages'][$this->_sections['i']['index']]['active'] == 'n'): ?>(<?php endif; ?><?php echo $this->_tpl_vars['languages'][$this->_sections['i']['index']]['language']; ?>
<?php if ($this->_tpl_vars['languages'][$this->_sections['i']['index']]['def_language'] == '1'): ?> *<?php endif; ?><?php if ($this->_tpl_vars['languages'][$this->_sections['i']['index']]['active'] == 'n'): ?>)<?php endif; ?>
		</td>
<?php endfor; endif; ?>
	</tr>
<?php unset($this->_sections['i']);
$this->_sections['i']['name'] = 'i';
$this->_sections['i']['loop'] = is_array($_loop=$this->_tpl_vars['pages']) ? count($_loop) : max(0, (int)$_loop); unset($_loop);
$this->_sections['i']['show'] = true;
$this->_sections['i']['max'] = $this->_sections['i']['loop'];
$this->_sections['i']['step'] = 1;
$this->_sections['i']['start'] = $this->_sections['i']['step'] > 0 ? 0 : $this->_sections['i']['loop']-1;
if ($this->_sections['i']['show']) {
    $this->_sections['i']['total'] = $this->_sections['i']['loop'];
    if ($this->_sections['i']['total'] == 0)
        $this->_sections['i']['show'] = false;
} else
    $this->_sections['i']['total'] = 0;
if ($this->_sections['i']['show']):

            for ($this->_sections['i']['index'] = $this->_sections['i']['start'], $this->_sections['i']['iteration'] = 1;
                 $this->_sections['i']['iteration'] <= $this->_sections['i']['total'];
                 $this->_sections['i']['index'] += $this->_sections['i']['step'], $this->_sections['i']['iteration']++):
$this->_sections['i']['rownum'] = $this->_sections['i']['iteration'];
$this->_sections['i']['index_prev'] = $this->_sections['i']['index'] - $this->_sections['i']['step'];
$this->_sections['i']['index_next'] = $this->_sections['i']['index'] + $this->_sections['i']['step'];
$this->_sections['i']['first']      = ($this->_sections['i']['iteration'] == 1);
$this->_sections['i']['last']       = ($this->_sections['i']['iteration'] == $this->_sections['i']['total']);
?>
	<tr>
		<td>
			<?php echo $this->_tpl_vars['pages'][$this->_sections['i']['index']]['page']; ?>

		</td>
	<?php unset($this->_sections['j']);
$this->_sections['j']['name'] = 'j';
$this->_sections['j']['loop'] = is_array($_loop=$this->_tpl_vars['languages']) ? count($_loop) : max(0, (int)$_loop); unset($_loop);
$this->_sections['j']['show'] = true;
$this->_sections['j']['max'] = $this->_sections['j']['loop'];
$this->_sections['j']['step'] = 1;
$this->_sections['j']['start'] = $this->_sections['j']['step'] > 0 ? 0 : $this->_sections['j']['loop']-1;
if ($this->_sections['j']['show']) {
    $this->_sections['j']['total'] = $this->_sections['j']['loop'];
    if ($this->_sections['j']['total'] == 0)
        $this->_sections['j']['show'] = false;
} else
    $this->_sections['j']['total'] = 0;
if ($this->_sections['j']['show']):

            for ($this->_sections['j']['index'] = $this->_sections['j']['start'], $this->_sections['j']['iteration'] = 1;
                 $this->_sections['j']['iteration'] <= $this->_sections['j']['total'];
                 $this->_sections['j']['index'] += $this->_sections['j']['step'], $this->_sections['j']['iteration']++):
$this->_sections['j']['rownum'] = $this->_sections['j']['iteration'];
$this->_sections['j']['index_prev'] = $this->_sections['j']['index'] - $this->_sections['j']['step'];
$this->_sections['j']['index_next'] = $this->_sections['j']['index'] + $this->_sections['j']['step'];
$this->_sections['j']['first']      = ($this->_sections['j']['iteration'] == 1);
$this->_sections['j']['last']       = ($this->_sections['j']['iteration'] == $this->_sections['j']['total']);
?>
	<?php $this->assign('n', $this->_tpl_vars['languages'][$this->_sections['j']['index']]['language_id']); ?>
		<td>
			<input 
				type="text" 
				maxlength="32" 
				id="name-<?php echo $this->_tpl_vars['pages'][$this->_sections['i']['index']]['id']; ?>
-<?php echo $this->_tpl_vars['languages'][$this->_sections['j']['index']]['language_id']; ?>
" 
				onfocus="taxonSetActivePageTitle([<?php echo $this->_tpl_vars['pages'][$this->_sections['i']['index']]['id']; ?>
,<?php echo $this->_tpl_vars['languages'][$this->_sections['j']['index']]['language_id']; ?>
])" 
				onblur="taxonPageTitleSave([<?php echo $this->_tpl_vars['pages'][$this->_sections['i']['index']]['id']; ?>
,<?php echo $this->_tpl_vars['languages'][$this->_sections['j']['index']]['language_id']; ?>
])" 
				value="<?php echo $this->_tpl_vars['pages'][$this->_sections['i']['index']]['page_titles'][$this->_tpl_vars['n']]; ?>
" 
			/>
		</td>
	<?php endfor; endif; ?>
		<td class="cell-page-delete" onclick="taxonPageDelete(<?php echo $this->_tpl_vars['pages'][$this->_sections['i']['index']]['id']; ?>
,'<?php echo $this->_tpl_vars['pages'][$this->_sections['i']['index']]['page']; ?>
');"></td>
<?php endfor; endif; ?>
	</tr>
</table>

<br />
<?php if (count($this->_tpl_vars['languages']) == 0): ?>
You have to define at least one language in your project before you can add any pages.<br />
<a href="../projects/data.php">Define languages.</a>
<?php else: ?>
<form method="post" action="" id="theForm">
<?php if (count($this->_tpl_vars['pages']) < 10): ?>
Add a new page:
<input type="text" maxlength="32" id="new_page" name="new_page" value="" />
<input type="hidden" name="rnd" value="<?php echo $this->_tpl_vars['rnd']; ?>
" />
<input type="submit" value="save" />
<?php endif; ?>
</form>
<?php endif; ?>

</div>

</div>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "../shared/admin-messages.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>

<?php echo '
<script type="text/JavaScript">
$(window).unload( function () { taxonPageTitleSave(taxonActivePageTitle); } );
</script>
'; ?>



<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "../shared/admin-footer.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<?php /* Smarty version 2.6.26, created on 2010-09-13 18:35:13
         compiled from edit.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'addslashes', 'edit.tpl', 47, false),)), $this); ?>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "../shared/admin-header.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>

<div id="page-main">

<form name="theForm" id="theForm">
	<input type="hidden" name="taxon_id" id="taxon_id" value="<?php echo $this->_tpl_vars['taxon']['id']; ?>
" />  

<div id="taxon-navigation-table-div">
<table id="taxon-navigation-table">
	<tr>
		<td id="taxon-navigation-cell">
			<span style="float:right">
				<span id="message-container" style="margin-right:10px">&nbsp;</span>
				<input type="button" value="save" onclick="taxonSaveData()" style="margin-right:5px" />
				<input type="button" value="undo" onclick="allSetMessage('coming soon')" style="margin-right:5px" />
				<input type="button" value="delete" onclick="taxonDeleteData(taxonActiveLanguage)" style="margin-right:5px" />
				<input type="button" value="taxon list" onclick="taxonClose()" style="" />
			</span>
		</td>
	</tr>
</table>
</div>

<div id="taxon-pages-table-div"></div>

<div id="taxon-language-table-div"></div>

Page title:<input type="text" maxlength="64" name="taxon" id="taxon-name-input" value="<?php echo $this->_tpl_vars['content']['title']; ?>
" />
<textarea name="content" style="width:880px;height:600px;" id="taxon-content"><?php echo $this->_tpl_vars['content']['content']; ?>
</textarea>
</form>



<?php echo '
<script type="text/JavaScript">
$(document).ready(function(){
'; ?>

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
	taxonAddLanguage([<?php echo $this->_tpl_vars['languages'][$this->_sections['i']['index']]['language_id']; ?>
,'<?php echo $this->_tpl_vars['languages'][$this->_sections['i']['index']]['language']; ?>
',<?php if ($this->_tpl_vars['languages'][$this->_sections['i']['index']]['def_language'] == '1'): ?>1<?php else: ?>0<?php endif; ?>]);
<?php endfor; endif; ?>
	taxonActiveLanguage = <?php echo $this->_tpl_vars['activeLanguage']; ?>
;
	taxonUpdateLanguageBlock();


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
	var pagenames = new Array();
	pagenames[-1] = '<?php echo ((is_array($_tmp=$this->_tpl_vars['pages'][$this->_sections['i']['index']]['page'])) ? $this->_run_mod_handler('addslashes', true, $_tmp) : addslashes($_tmp)); ?>
';
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
?><?php $this->assign('n', $this->_tpl_vars['languages'][$this->_sections['j']['index']]['language_id']); ?>pagenames[<?php echo $this->_tpl_vars['n']; ?>
] = '<?php echo ((is_array($_tmp=$this->_tpl_vars['pages'][$this->_sections['i']['index']]['titles'][$this->_tpl_vars['n']]['title'])) ? $this->_run_mod_handler('addslashes', true, $_tmp) : addslashes($_tmp)); ?>
';
<?php endfor; endif; ?>
	taxonAddPage([<?php echo $this->_tpl_vars['pages'][$this->_sections['i']['index']]['id']; ?>
,pagenames,<?php if ($this->_tpl_vars['pages'][$this->_sections['i']['index']]['def_page'] == '1'): ?>1<?php else: ?>0<?php endif; ?>]);
<?php endfor; endif; ?>
	taxonActivePage = <?php echo $this->_tpl_vars['activePage']; ?>
;
	taxonUpdatePageBlock();


<?php echo '
	$(window).unload(
		function () { 
			taxonConfirmSaveOnUnload() ;
		} 
	);
});
</script>
'; ?>


</div>

<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "../shared/admin-footer.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
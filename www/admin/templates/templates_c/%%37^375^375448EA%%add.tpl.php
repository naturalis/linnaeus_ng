<?php /* Smarty version 2.6.26, created on 2010-09-07 18:08:32
         compiled from add.tpl */ ?>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "../shared/admin-header.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>

<div id="page-main">

<table class="taxon-language-table">
	<tr>
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
		<td class="taxon-language-cell<?php if ($this->_tpl_vars['languages'][$this->_sections['i']['index']]['id'] == $this->_tpl_vars['activeLanguage']): ?>-active<?php else: ?>" onclick="alert(<?php echo $this->_tpl_vars['languages'][$this->_sections['i']['index']]['id']; ?>
)<?php endif; ?>">
			<?php echo $this->_tpl_vars['languages'][$this->_sections['i']['index']]['language']; ?>
<?php if ($this->_tpl_vars['languages'][$this->_sections['i']['index']]['def_language'] == '1'): ?> *<?php endif; ?>
		</td>
<?php endfor; endif; ?>
	</tr>
</table>
<form name="theForm" id="theForm">
<input type="hidden" name="taxon_id" id="taxon_id" value="" />  
Taxon name:<input type="text" maxlength="64" name="taxon" id="taxon-name-input" value="" />
<span style="float:right">
<span id="save-message">
<input type="button" value="save" onclick="taxonSaveData()" style="float:right" />
</span>
</span>
<textarea name="content" style="width:880px;height:600px;" id="taxon-content">
</textarea>
</form>

</div>

<?php echo '
<script type="text/JavaScript">
$(document).ready(function(){
	activeLanguage = '; ?>
<?php echo $this->_tpl_vars['activeLanguage']; ?>
<?php echo ';
});
</script>
'; ?>



<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "../shared/admin-footer.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
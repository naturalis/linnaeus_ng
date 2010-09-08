<?php /* Smarty version 2.6.26, created on 2010-09-08 13:25:56
         compiled from _add_edit_body.tpl */ ?>
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
				<input type="button" value="undo" onclick="taxonMessage('coming soon')" style="margin-right:5px" />
				<input type="button" value="delete" onclick="taxonDeleteData(taxonActiveLanguage)" style="margin-right:5px" />
				<input type="button" value="taxon list" onclick="window.open('list.php','_top');" style="" />
			</span>
		</td>
	</tr>
</table>
</div>
<div id="taxon-language-table-div">
<table id="taxon-language-table" class="taxon-language-table">
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
		<td class="taxon-language-cell<?php if ($this->_tpl_vars['languages'][$this->_sections['i']['index']]['language_id'] == $this->_tpl_vars['activeLanguage']): ?>-active<?php else: ?>" onclick="taxonGetData(<?php echo $this->_tpl_vars['languages'][$this->_sections['i']['index']]['language_id']; ?>
)<?php endif; ?>">
			<?php echo $this->_tpl_vars['languages'][$this->_sections['i']['index']]['language']; ?>
<?php if ($this->_tpl_vars['languages'][$this->_sections['i']['index']]['def_language'] == '1'): ?> *<?php endif; ?>
		</td>
<?php endfor; endif; ?>
	</tr>
</table>
</div>

<p>
ADD MORE PAGES
</p>

Taxon name:<input type="text" maxlength="64" name="taxon" id="taxon-name-input" value="<?php echo $this->_tpl_vars['content'][$this->_tpl_vars['activeLanguage']]['content_name']; ?>
" />
<textarea name="content" style="width:880px;height:600px;" id="taxon-content"><?php echo $this->_tpl_vars['content'][$this->_tpl_vars['activeLanguage']]['content']; ?>
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
<?php echo '
});
</script>
'; ?>
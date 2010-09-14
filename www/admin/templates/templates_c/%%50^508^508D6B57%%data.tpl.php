<?php /* Smarty version 2.6.26, created on 2010-09-13 11:35:50
         compiled from data.tpl */ ?>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "../shared/admin-header.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>

<div id="page-main">
<form enctype="multipart/form-data" action="" method="POST">
<table>
	<tr>
		<td>
			Internal project name:
		</td>
		<td colspan="2">
			<?php echo $this->_tpl_vars['data']['sys_name']; ?>

		</td>
	</tr>
	<tr>
		<td>
			Internal project description:
		</td>
		<td colspan="2">
			<?php echo $this->_tpl_vars['data']['sys_description']; ?>

		</td>
	</tr>
	<tr>
		<td>
			Project title:
		</td>
		<td colspan="2">
			<input type="text" name="title" value="<?php echo $this->_tpl_vars['data']['title']; ?>
" style="width:300px;" />
		</td>
	</tr>
	<tr style="vertical-align:top">
		<td>
			Project logo:
		</td>
		<td colspan="2">
		<?php if ($this->_tpl_vars['data']['logo_url']): ?>
		<img src="<?php echo $this->_tpl_vars['data']['logo_url']; ?>
" width="150px" /><br />
		<label><input type="checkbox" value="1" name="deleteLogo" />Delete current logo (uploading a new logo deletes the old one as well)</label><br />
		<?php endif; ?>
		<input type="hidden" name="MAX_FILE_SIZE" value="10000000" />
		<input name="uploadedfile" type="file" /><br />
		</td>
	</tr>
	<tr style="vertical-align:top">
		<td>
			Project languages:
		</td>
		<td>
			<select name="language-select" id="language-select">
			<?php $this->assign('first', true); ?>
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
				<?php if ($this->_tpl_vars['first'] && $this->_tpl_vars['languages'][$this->_sections['i']['index']]['show_order'] == ''): ?>				
				<option disabled="disabled" class="language-select-item-disabled">
					&mdash;&mdash;&mdash;&mdash;&mdash;&mdash;&mdash;&mdash;&mdash;&mdash;&mdash;&mdash;&mdash;&mdash;
				</option><?php $this->assign('first', false); ?><?php endif; ?>
				<option 
					value="<?php echo $this->_tpl_vars['languages'][$this->_sections['i']['index']]['id']; ?>
"
					class="language-select-item<?php if ($this->_tpl_vars['languages'][$this->_sections['i']['index']]['language_project_id'] != ''): ?>-active<?php endif; ?>"
					><?php echo $this->_tpl_vars['languages'][$this->_sections['i']['index']]['language']; ?>
</option>
			<?php endfor; endif; ?>
			</select>
		</td>
	</tr>
	<tr>
		<td></td>
		<td style="text-align:center">
			<img 
				src="<?php echo $this->_tpl_vars['rootWebUrl']; ?>
admin/images/system/icons/arrow-270.png" 
				onclick="projectSaveLanguage('add',[$('#language-select :selected').val(),$('#language-select :selected').text()])" 
				class="general-clickable-image" 
				title="add language"
			/>
		</td>
	</td>
	<tr>
		<td></td>
		<td>
		<!-- u>Language(s) currently in use</u><br / -->
		<span id="language-list">	
		</span>
		</td>
	</tr>		
</table>
<input type="submit" value="save" />
</form>

<br />
The "welcome" and "contributors" texts will be added once the html-editor is in place.
</div>

<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "../shared/admin-messages.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>


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
	<?php if ($this->_tpl_vars['languages'][$this->_sections['i']['index']]['language_project_id'] != ''): ?>
	projectAddLanguage([<?php echo $this->_tpl_vars['languages'][$this->_sections['i']['index']]['id']; ?>
,'<?php echo $this->_tpl_vars['languages'][$this->_sections['i']['index']]['language']; ?>
',<?php echo $this->_tpl_vars['languages'][$this->_sections['i']['index']]['language_project_id']; ?>
,<?php if ($this->_tpl_vars['languages'][$this->_sections['i']['index']]['is_project_default']): ?>1<?php else: ?>0<?php endif; ?>,<?php if ($this->_tpl_vars['languages'][$this->_sections['i']['index']]['is_active']): ?>1<?php else: ?>0<?php endif; ?>])
	<?php endif; ?>
<?php endfor; endif; ?>
	projectUpdateLanguageBlock();
<?php echo '
});
</script>
'; ?>



<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "../shared/admin-footer.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
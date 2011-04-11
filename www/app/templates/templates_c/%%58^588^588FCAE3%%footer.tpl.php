<?php /* Smarty version 2.6.26, created on 2011-04-08 10:09:27
         compiled from ../shared/footer.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('block', 't', '../shared/footer.tpl', 3, false),array('modifier', 'escape', '../shared/footer.tpl', 7, false),array('modifier', 'addslashes', '../shared/footer.tpl', 35, false),)), $this); ?>
<?php if ($this->_tpl_vars['showBackToSearch'] && $this->_tpl_vars['session']['user']['search']['hasSearchResults']): ?>
<div id="back-to-search">
<span id="back-link" onclick="window.open('../linnaeus/redosearch.php','_self')"><?php $this->_tag_stack[] = array('t', array()); $_block_repeat=true;$this->_plugins['block']['t'][0][0]->smartyTranslate($this->_tag_stack[count($this->_tag_stack)-1][1], null, $this, $_block_repeat);while ($_block_repeat) { ob_start(); ?>back to search results<?php $_block_content = ob_get_contents(); ob_end_clean(); $_block_repeat=false;echo $this->_plugins['block']['t'][0][0]->smartyTranslate($this->_tag_stack[count($this->_tag_stack)-1][1], $_block_content, $this, $_block_repeat); }  array_pop($this->_tag_stack); ?></span>
</div>
<?php elseif ($this->_tpl_vars['backlink']['url'] == 'not implemented'): ?>
<p>
<span class="a" onclick="doBackForm('<?php echo $this->_tpl_vars['backlink']['url']; ?>
','<?php echo smarty_modifier_escape($this->_tpl_vars['backlink']['data']); ?>
');" title="Back to <?php echo $this->_tpl_vars['backlink']['name']; ?>
">BACK</span>
</p>
<?php endif; ?>
</div ends="page-container">
<div id="footer-container">
</div ends="footer-container">
</div ends="body-container">
<div id="hint-balloon" onmouseout="glossTextOut()" 
	style="
	background-color:#FFFF99;
	border:1px solid #bbbb00;
	width:225px;height:100px;
	padding:3px;
	font-size:9px;
	display:none;
	overflow:hidden;
	cursor:pointer;
	position:absolute;
	top:0px;
	left:0px;
	">
</div>
</form>
<?php echo '
<script type="text/JavaScript">
$(document).ready(function(){
'; ?>

	$('#body-container').height($(document).height());
	<?php if ($this->_tpl_vars['search']): ?>onSearchBoxSelect('<?php echo addslashes($this->_tpl_vars['search']); ?>
');<?php endif; ?>

<?php $_from = $this->_tpl_vars['requestData']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['k'] => $this->_tpl_vars['v']):
?>
addRequestVar('<?php echo $this->_tpl_vars['k']; ?>
','<?php echo ((is_array($_tmp=$this->_tpl_vars['v'])) ? $this->_run_mod_handler('addslashes', true, $_tmp) : addslashes($_tmp)); ?>
')
<?php endforeach; endif; unset($_from); ?>

})
<?php echo '
</script>
'; ?>

</body>
</html>
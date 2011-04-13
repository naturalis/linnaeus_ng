<?php /* Smarty version 2.6.26, created on 2011-04-13 15:05:39
         compiled from index.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('block', 't', 'index.tpl', 5, false),array('modifier', 'count', 'index.tpl', 7, false),array('modifier', 'escape', 'index.tpl', 53, false),array('modifier', 'nl2br', 'index.tpl', 57, false),)), $this); ?>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "../shared/header.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>

<div id="path">
	<div id="concise">
	<span onclick="keyToggleFullPath()" id="toggle"><?php $this->_tag_stack[] = array('t', array()); $_block_repeat=true;$this->_plugins['block']['t'][0][0]->smartyTranslate($this->_tag_stack[count($this->_tag_stack)-1][1], null, $this, $_block_repeat);while ($_block_repeat) { ob_start(); ?>Path:<?php $_block_content = ob_get_contents(); ob_end_clean(); $_block_repeat=false;echo $this->_plugins['block']['t'][0][0]->smartyTranslate($this->_tag_stack[count($this->_tag_stack)-1][1], $_block_content, $this, $_block_repeat); }  array_pop($this->_tag_stack); ?></span>
	<?php $_from = $this->_tpl_vars['keypath']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['k'] => $this->_tpl_vars['v']):
?>
	<?php if ($this->_tpl_vars['v']['is_start'] == 1 || count($this->_tpl_vars['keypath']) <= $this->_tpl_vars['keyPathMaxItems'] || ( count($this->_tpl_vars['keypath']) > $this->_tpl_vars['keyPathMaxItems'] && $this->_tpl_vars['k'] >= count($this->_tpl_vars['keypath']) -2 )): ?>
		<?php if ($this->_tpl_vars['v']['is_start'] != 1): ?><span class="arrow">&rarr;</span><?php endif; ?>
		<?php echo $this->_tpl_vars['v']['step_number']; ?>
. <span class="item" onclick="keyDoStep(<?php echo $this->_tpl_vars['v']['id']; ?>
)"><?php echo $this->_tpl_vars['v']['step_title']; ?>
<?php if ($this->_tpl_vars['v']['choice_marker']): ?> (<?php echo $this->_tpl_vars['v']['choice_marker']; ?>
)<?php endif; ?></span>
	<?php endif; ?>
	<?php if ($this->_tpl_vars['v']['is_start'] == 1 && count($this->_tpl_vars['keypath']) > $this->_tpl_vars['keyPathMaxItems']): ?><span class="arrow">&rarr;</span><span class="abbreviation">[...]</span><?php endif; ?>
	<?php endforeach; endif; unset($_from); ?>
	</div>
	<div id="path-full" class="full-invisible">
	<table>
	<?php $_from = $this->_tpl_vars['keypath']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['k'] => $this->_tpl_vars['v']):
?>
		<tr>
			<td class="number-cell"><?php echo $this->_tpl_vars['v']['step_number']; ?>
. </td>
			<td><span class="item" onclick="keyDoStep(<?php echo $this->_tpl_vars['v']['id']; ?>
)"><?php echo $this->_tpl_vars['v']['step_title']; ?>
<?php if ($this->_tpl_vars['v']['choice_marker']): ?> (<?php echo $this->_tpl_vars['v']['choice_marker']; ?>
)<?php endif; ?></span></td>
		</tr>
	<?php endforeach; endif; unset($_from); ?>
	</table>
	</div>
</div>

<div id="taxa" style="overflow-y:scroll;">
<?php if (count($this->_tpl_vars['taxa']) == 1): ?><?php $this->assign('w', 'taxon'); ?><?php else: ?><?php $this->assign('w', 'taxa'); ?><?php endif; ?>
<span id="header"><?php $this->_tag_stack[] = array('t', array('_s1' => count($this->_tpl_vars['taxa']),'_s2' => $this->_tpl_vars['w'])); $_block_repeat=true;$this->_plugins['block']['t'][0][0]->smartyTranslate($this->_tag_stack[count($this->_tag_stack)-1][1], null, $this, $_block_repeat);while ($_block_repeat) { ob_start(); ?>%s possible %s remaining:<?php $_block_content = ob_get_contents(); ob_end_clean(); $_block_repeat=false;echo $this->_plugins['block']['t'][0][0]->smartyTranslate($this->_tag_stack[count($this->_tag_stack)-1][1], $_block_content, $this, $_block_repeat); }  array_pop($this->_tag_stack); ?></span><br/>
<?php $_from = $this->_tpl_vars['taxa']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['k'] => $this->_tpl_vars['v']):
?>
<span class="a" style="padding-left:3px" onclick="goTaxon(<?php echo $this->_tpl_vars['v']['id']; ?>
)">
	<?php echo $this->_tpl_vars['v']['taxon']; ?>

	<?php if ($this->_tpl_vars['v']['is_hybrid'] == 1): ?><?php echo $this->_tpl_vars['session']['project']['hybrid_marker']; ?>
<?php endif; ?>
</span><br />
<?php endforeach; endif; unset($_from); ?>
</div>
	
<div id="page-main">
	<div id="step">
		<div id="question">
			<div id="head">
				<span id="step-nr"><?php echo $this->_tpl_vars['step']['number']; ?>
</span>.
				<span id="step-title"><?php echo $this->_tpl_vars['step']['title']; ?>
</span>
			</div>
			<div id="content"><?php echo $this->_tpl_vars['step']['content']; ?>
</div>
		</div>
		<div id="choices">

<?php $_from = $this->_tpl_vars['choices']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['k'] => $this->_tpl_vars['v']):
?>
			<div class="choice">
	<?php if ($this->_tpl_vars['v']['choice_img']): ?>
					<img
						class="image-small"
						onclick="showMedia('<?php echo $this->_tpl_vars['session']['project']['urls']['project_media']; ?>
<?php echo ((is_array($_tmp=$this->_tpl_vars['v']['choice_img'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'url') : smarty_modifier_escape($_tmp, 'url')); ?>
','<?php echo $this->_tpl_vars['v']['choice_img']; ?>
');" 
						src="<?php echo $this->_tpl_vars['session']['project']['urls']['project_media']; ?>
<?php echo ((is_array($_tmp=$this->_tpl_vars['v']['choice_img'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'url') : smarty_modifier_escape($_tmp, 'url')); ?>
" />
	<?php endif; ?>
					<span class="marker"><?php echo $this->_tpl_vars['v']['marker']; ?>
</span>.
					<span class="text"><?php echo ((is_array($_tmp=$this->_tpl_vars['v']['choice_txt'])) ? $this->_run_mod_handler('nl2br', true, $_tmp) : smarty_modifier_nl2br($_tmp)); ?>
</span>
					<br />
					<span class="target">
					<?php if ($this->_tpl_vars['v']['res_keystep_id'] != '' && $this->_tpl_vars['v']['res_keystep_id'] != '-1'): ?>
						<span class="arrow">&rarr;</span>
						<span class="target-step" onclick="keyDoChoice(<?php echo $this->_tpl_vars['v']['id']; ?>
)"><?php if ($this->_tpl_vars['v']['target_number']): ?><?php $this->_tag_stack[] = array('t', array()); $_block_repeat=true;$this->_plugins['block']['t'][0][0]->smartyTranslate($this->_tag_stack[count($this->_tag_stack)-1][1], null, $this, $_block_repeat);while ($_block_repeat) { ob_start(); ?>Step<?php $_block_content = ob_get_contents(); ob_end_clean(); $_block_repeat=false;echo $this->_plugins['block']['t'][0][0]->smartyTranslate($this->_tag_stack[count($this->_tag_stack)-1][1], $_block_content, $this, $_block_repeat); }  array_pop($this->_tag_stack); ?> <?php echo $this->_tpl_vars['v']['target_number']; ?>
: <?php endif; ?><?php echo $this->_tpl_vars['v']['target']; ?>
</span>
					<?php elseif ($this->_tpl_vars['v']['res_taxon_id'] != ''): ?>
						<span class="arrow">&rarr;</span>
						<span class="target-taxon" onclick="goTaxon(<?php echo $this->_tpl_vars['v']['res_taxon_id']; ?>
)">
							<?php $this->_tag_stack[] = array('t', array()); $_block_repeat=true;$this->_plugins['block']['t'][0][0]->smartyTranslate($this->_tag_stack[count($this->_tag_stack)-1][1], null, $this, $_block_repeat);while ($_block_repeat) { ob_start(); ?>Taxon:<?php $_block_content = ob_get_contents(); ob_end_clean(); $_block_repeat=false;echo $this->_plugins['block']['t'][0][0]->smartyTranslate($this->_tag_stack[count($this->_tag_stack)-1][1], $_block_content, $this, $_block_repeat); }  array_pop($this->_tag_stack); ?> <?php echo $this->_tpl_vars['v']['target']; ?>

							<?php if ($this->_tpl_vars['v']['is_hybrid'] == 1): ?><span class="hybrid-marker" title="<?php $this->_tag_stack[] = array('t', array()); $_block_repeat=true;$this->_plugins['block']['t'][0][0]->smartyTranslate($this->_tag_stack[count($this->_tag_stack)-1][1], null, $this, $_block_repeat);while ($_block_repeat) { ob_start(); ?>hybrid<?php $_block_content = ob_get_contents(); ob_end_clean(); $_block_repeat=false;echo $this->_plugins['block']['t'][0][0]->smartyTranslate($this->_tag_stack[count($this->_tag_stack)-1][1], $_block_content, $this, $_block_repeat); }  array_pop($this->_tag_stack); ?>"><?php echo $this->_tpl_vars['session']['project']['hybrid_marker']; ?>
</span><?php endif; ?>
							</span>
					<?php endif; ?>
					</span>

			</div>
<?php endforeach; endif; unset($_from); ?>
		</div>
	</div>
</div>

<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "../shared/footer.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
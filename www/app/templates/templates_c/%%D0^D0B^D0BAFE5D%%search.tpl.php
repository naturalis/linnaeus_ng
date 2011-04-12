<?php /* Smarty version 2.6.26, created on 2011-04-12 16:03:39
         compiled from search.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'strlen', 'search.tpl', 4, false),array('modifier', 'replace', 'search.tpl', 8, false),array('modifier', 'count', 'search.tpl', 19, false),array('modifier', 'strtolower', 'search.tpl', 21, false),array('block', 't', 'search.tpl', 8, false),array('block', 'h', 'search.tpl', 26, false),array('block', 'foundContent', 'search.tpl', 27, false),)), $this); ?>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "../shared/header.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>

<div id="page-main">
<?php if (strlen($this->_tpl_vars['search']) > 2): ?>
<div id="results">
<div id="header">
<?php if ($this->_tpl_vars['results']['numOfResults'] == 0): ?>
	<?php $this->_tag_stack[] = array('t', array('_s1' => ((is_array($_tmp=$this->_tpl_vars['search'])) ? $this->_run_mod_handler('replace', true, $_tmp, '"', '') : smarty_modifier_replace($_tmp, '"', '')))); $_block_repeat=true;$this->_plugins['block']['t'][0][0]->smartyTranslate($this->_tag_stack[count($this->_tag_stack)-1][1], null, $this, $_block_repeat);while ($_block_repeat) { ob_start(); ?>Your search for "%s" produced no results.<?php $_block_content = ob_get_contents(); ob_end_clean(); $_block_repeat=false;echo $this->_plugins['block']['t'][0][0]->smartyTranslate($this->_tag_stack[count($this->_tag_stack)-1][1], $_block_content, $this, $_block_repeat); }  array_pop($this->_tag_stack); ?>
<?php elseif ($this->_tpl_vars['results']['numOfResults'] == 1): ?>
	<?php $this->_tag_stack[] = array('t', array('_s1' => ((is_array($_tmp=$this->_tpl_vars['search'])) ? $this->_run_mod_handler('replace', true, $_tmp, '"', '') : smarty_modifier_replace($_tmp, '"', '')),'_s2' => $this->_tpl_vars['results']['numOfResults'])); $_block_repeat=true;$this->_plugins['block']['t'][0][0]->smartyTranslate($this->_tag_stack[count($this->_tag_stack)-1][1], null, $this, $_block_repeat);while ($_block_repeat) { ob_start(); ?>Your search for "%s" produced %s result:<?php $_block_content = ob_get_contents(); ob_end_clean(); $_block_repeat=false;echo $this->_plugins['block']['t'][0][0]->smartyTranslate($this->_tag_stack[count($this->_tag_stack)-1][1], $_block_content, $this, $_block_repeat); }  array_pop($this->_tag_stack); ?>
<?php else: ?>
	<?php $this->_tag_stack[] = array('t', array('_s1' => ((is_array($_tmp=$this->_tpl_vars['search'])) ? $this->_run_mod_handler('replace', true, $_tmp, '"', '') : smarty_modifier_replace($_tmp, '"', '')),'_s2' => $this->_tpl_vars['results']['numOfResults'],'_s3' => $this->_tpl_vars['resultWord'])); $_block_repeat=true;$this->_plugins['block']['t'][0][0]->smartyTranslate($this->_tag_stack[count($this->_tag_stack)-1][1], null, $this, $_block_repeat);while ($_block_repeat) { ob_start(); ?>Your search for "%s" produced %s results:<?php $_block_content = ob_get_contents(); ob_end_clean(); $_block_repeat=false;echo $this->_plugins['block']['t'][0][0]->smartyTranslate($this->_tag_stack[count($this->_tag_stack)-1][1], $_block_content, $this, $_block_repeat); }  array_pop($this->_tag_stack); ?>
<?php endif; ?>
</div>

<?php if ($this->_tpl_vars['results']['species']['numOfResults'] > 0): ?>
<div class="set">
	<?php $_from = $this->_tpl_vars['results']['species']['results']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['cat'] => $this->_tpl_vars['res']):
?>
	<?php if (count($this->_tpl_vars['res']['data']) > 0): ?>
	<div class="subset">
		<div class="set-header"><?php echo count($this->_tpl_vars['res']['data']); ?>
 <?php $this->_tag_stack[] = array('t', array()); $_block_repeat=true;$this->_plugins['block']['t'][0][0]->smartyTranslate($this->_tag_stack[count($this->_tag_stack)-1][1], null, $this, $_block_repeat);while ($_block_repeat) { ob_start(); ?>in<?php $_block_content = ob_get_contents(); ob_end_clean(); $_block_repeat=false;echo $this->_plugins['block']['t'][0][0]->smartyTranslate($this->_tag_stack[count($this->_tag_stack)-1][1], $_block_content, $this, $_block_repeat); }  array_pop($this->_tag_stack); ?> <?php echo strtolower($this->_tpl_vars['res']['label']); ?>
</div>
		<?php $_from = $this->_tpl_vars['res']['data']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['k'] => $this->_tpl_vars['v']):
?>
		<?php if ($this->_tpl_vars['results']['species']['taxonList'][$this->_tpl_vars['v']['taxon_id']] && $this->_tpl_vars['results']['species']['taxonList'][$this->_tpl_vars['v']['taxon_id']]['taxon'] !== $this->_tpl_vars['v']['label']): ?><?php echo $this->_tpl_vars['results']['species']['taxonList'][$this->_tpl_vars['v']['taxon_id']]['taxon']; ?>
<?php if ($this->_tpl_vars['results']['species']['categoryList'][$this->_tpl_vars['v']['cat']]): ?> (<?php echo strtolower($this->_tpl_vars['results']['species']['categoryList'][$this->_tpl_vars['v']['cat']]['title']); ?>
)<?php endif; ?>:
		<?php endif; ?>
		<span class="result" onclick="goTaxon(<?php echo $this->_tpl_vars['v']['taxon_id']; ?>
<?php if ($this->_tpl_vars['v']['cat']): ?>,'<?php echo $this->_tpl_vars['v']['cat']; ?>
'<?php endif; ?>)">
			<?php if ($this->_tpl_vars['v']['label']): ?><?php $this->_tag_stack[] = array('h', array('search' => $this->_tpl_vars['search'])); $_block_repeat=true;$this->_plugins['block']['h'][0][0]->highlightFound($this->_tag_stack[count($this->_tag_stack)-1][1], null, $this, $_block_repeat);while ($_block_repeat) { ob_start(); ?><?php echo $this->_tpl_vars['v']['label']; ?>
<?php $_block_content = ob_get_contents(); ob_end_clean(); $_block_repeat=false;echo $this->_plugins['block']['h'][0][0]->highlightFound($this->_tag_stack[count($this->_tag_stack)-1][1], $_block_content, $this, $_block_repeat); }  array_pop($this->_tag_stack); ?>
			<?php elseif ($this->_tpl_vars['v']['content']): ?>"<?php $this->_tag_stack[] = array('foundContent', array('search' => $this->_tpl_vars['search'])); $_block_repeat=true;$this->_plugins['block']['foundContent'][0][0]->foundContent($this->_tag_stack[count($this->_tag_stack)-1][1], null, $this, $_block_repeat);while ($_block_repeat) { ob_start(); ?><?php echo $this->_tpl_vars['v']['content']; ?>
<?php $_block_content = ob_get_contents(); ob_end_clean(); $_block_repeat=false;echo $this->_plugins['block']['foundContent'][0][0]->foundContent($this->_tag_stack[count($this->_tag_stack)-1][1], $_block_content, $this, $_block_repeat); }  array_pop($this->_tag_stack); ?>"
			<?php endif; ?>
		</span><br/>
		<?php endforeach; endif; unset($_from); ?>
	</div>
	<?php endif; ?>
	<?php endforeach; endif; unset($_from); ?>
</div>
<?php endif; ?>

<?php if ($this->_tpl_vars['results']['glossary']['numOfResults'] > 0): ?>
<div class="set">
	<?php $_from = $this->_tpl_vars['results']['glossary']['results']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['cat'] => $this->_tpl_vars['res']):
?>
	<?php if (count($this->_tpl_vars['res']['data']) > 0): ?>
	<div class="subset">
		<div class="set-header"><?php echo count($this->_tpl_vars['res']['data']); ?>
 <?php $this->_tag_stack[] = array('t', array()); $_block_repeat=true;$this->_plugins['block']['t'][0][0]->smartyTranslate($this->_tag_stack[count($this->_tag_stack)-1][1], null, $this, $_block_repeat);while ($_block_repeat) { ob_start(); ?>in<?php $_block_content = ob_get_contents(); ob_end_clean(); $_block_repeat=false;echo $this->_plugins['block']['t'][0][0]->smartyTranslate($this->_tag_stack[count($this->_tag_stack)-1][1], $_block_content, $this, $_block_repeat); }  array_pop($this->_tag_stack); ?> <?php echo strtolower($this->_tpl_vars['res']['label']); ?>
</div>
		<?php $_from = $this->_tpl_vars['res']['data']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['k'] => $this->_tpl_vars['v']):
?>
		<span class="result" onclick="goGlossaryTerm(<?php echo $this->_tpl_vars['v']['id']; ?>
)">
			<?php if ($this->_tpl_vars['v']['term'] && $this->_tpl_vars['v']['term'] != $this->_tpl_vars['v']['label']): ?><?php echo $this->_tpl_vars['v']['term']; ?>
: <?php endif; ?>
			<?php $this->_tag_stack[] = array('h', array('search' => $this->_tpl_vars['search'])); $_block_repeat=true;$this->_plugins['block']['h'][0][0]->highlightFound($this->_tag_stack[count($this->_tag_stack)-1][1], null, $this, $_block_repeat);while ($_block_repeat) { ob_start(); ?><?php echo $this->_tpl_vars['v']['label']; ?>
<?php $_block_content = ob_get_contents(); ob_end_clean(); $_block_repeat=false;echo $this->_plugins['block']['h'][0][0]->highlightFound($this->_tag_stack[count($this->_tag_stack)-1][1], $_block_content, $this, $_block_repeat); }  array_pop($this->_tag_stack); ?><?php if ($this->_tpl_vars['v']['content']): ?>: "<?php $this->_tag_stack[] = array('foundContent', array('search' => $this->_tpl_vars['search'])); $_block_repeat=true;$this->_plugins['block']['foundContent'][0][0]->foundContent($this->_tag_stack[count($this->_tag_stack)-1][1], null, $this, $_block_repeat);while ($_block_repeat) { ob_start(); ?><?php echo $this->_tpl_vars['v']['content']; ?>
<?php $_block_content = ob_get_contents(); ob_end_clean(); $_block_repeat=false;echo $this->_plugins['block']['foundContent'][0][0]->foundContent($this->_tag_stack[count($this->_tag_stack)-1][1], $_block_content, $this, $_block_repeat); }  array_pop($this->_tag_stack); ?>"<?php endif; ?>
			<?php if ($this->_tpl_vars['v']['synonym'] && $this->_tpl_vars['v']['synonym'] != $this->_tpl_vars['v']['label']): ?> (<?php $this->_tag_stack[] = array('t', array()); $_block_repeat=true;$this->_plugins['block']['t'][0][0]->smartyTranslate($this->_tag_stack[count($this->_tag_stack)-1][1], null, $this, $_block_repeat);while ($_block_repeat) { ob_start(); ?>synonym of<?php $_block_content = ob_get_contents(); ob_end_clean(); $_block_repeat=false;echo $this->_plugins['block']['t'][0][0]->smartyTranslate($this->_tag_stack[count($this->_tag_stack)-1][1], $_block_content, $this, $_block_repeat); }  array_pop($this->_tag_stack); ?> <?php echo $this->_tpl_vars['v']['synonym']; ?>
)<?php endif; ?>
		</span><br/>
		<?php endforeach; endif; unset($_from); ?>
	</div>
	<?php endif; ?>
	<?php endforeach; endif; unset($_from); ?>
</div>
<?php endif; ?>

<?php if ($this->_tpl_vars['results']['literature']['numOfResults'] > 0): ?>
<div class="set">
	<?php $_from = $this->_tpl_vars['results']['literature']['results']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['cat'] => $this->_tpl_vars['res']):
?>
	<?php if (count($this->_tpl_vars['res']['data']) > 0): ?>
	<div class="subset">
		<div class="set-header"><?php echo count($this->_tpl_vars['res']['data']); ?>
 <?php $this->_tag_stack[] = array('t', array()); $_block_repeat=true;$this->_plugins['block']['t'][0][0]->smartyTranslate($this->_tag_stack[count($this->_tag_stack)-1][1], null, $this, $_block_repeat);while ($_block_repeat) { ob_start(); ?>in<?php $_block_content = ob_get_contents(); ob_end_clean(); $_block_repeat=false;echo $this->_plugins['block']['t'][0][0]->smartyTranslate($this->_tag_stack[count($this->_tag_stack)-1][1], $_block_content, $this, $_block_repeat); }  array_pop($this->_tag_stack); ?> <?php echo strtolower($this->_tpl_vars['res']['label']); ?>
</div>
		<?php $_from = $this->_tpl_vars['res']['data']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['k'] => $this->_tpl_vars['v']):
?>
		<span class="result" onclick="goLiterature(<?php echo $this->_tpl_vars['v']['id']; ?>
)">
			<?php $this->_tag_stack[] = array('h', array('search' => $this->_tpl_vars['search'])); $_block_repeat=true;$this->_plugins['block']['h'][0][0]->highlightFound($this->_tag_stack[count($this->_tag_stack)-1][1], null, $this, $_block_repeat);while ($_block_repeat) { ob_start(); ?><?php echo $this->_tpl_vars['v']['author_full']; ?>
 (<?php echo $this->_tpl_vars['v']['year']; ?>
)<?php $_block_content = ob_get_contents(); ob_end_clean(); $_block_repeat=false;echo $this->_plugins['block']['h'][0][0]->highlightFound($this->_tag_stack[count($this->_tag_stack)-1][1], $_block_content, $this, $_block_repeat); }  array_pop($this->_tag_stack); ?><?php if ($this->_tpl_vars['v']['content']): ?>: "<?php $this->_tag_stack[] = array('foundContent', array('search' => $this->_tpl_vars['search'])); $_block_repeat=true;$this->_plugins['block']['foundContent'][0][0]->foundContent($this->_tag_stack[count($this->_tag_stack)-1][1], null, $this, $_block_repeat);while ($_block_repeat) { ob_start(); ?><?php echo $this->_tpl_vars['v']['content']; ?>
<?php $_block_content = ob_get_contents(); ob_end_clean(); $_block_repeat=false;echo $this->_plugins['block']['foundContent'][0][0]->foundContent($this->_tag_stack[count($this->_tag_stack)-1][1], $_block_content, $this, $_block_repeat); }  array_pop($this->_tag_stack); ?>"<?php endif; ?>
		</span><br/>
		<?php endforeach; endif; unset($_from); ?>
	</div>
	<?php endif; ?>
	<?php endforeach; endif; unset($_from); ?>
</div>
<?php endif; ?>

<?php if ($this->_tpl_vars['results']['dichkey']['numOfResults'] > 0): ?>
<div class="set">
	<?php $_from = $this->_tpl_vars['results']['dichkey']['results']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['cat'] => $this->_tpl_vars['res']):
?>
	<?php if (count($this->_tpl_vars['res']['data']) > 0): ?>
	<div class="subset">
		<div class="set-header"><?php echo count($this->_tpl_vars['res']['data']); ?>
 <?php $this->_tag_stack[] = array('t', array()); $_block_repeat=true;$this->_plugins['block']['t'][0][0]->smartyTranslate($this->_tag_stack[count($this->_tag_stack)-1][1], null, $this, $_block_repeat);while ($_block_repeat) { ob_start(); ?>in<?php $_block_content = ob_get_contents(); ob_end_clean(); $_block_repeat=false;echo $this->_plugins['block']['t'][0][0]->smartyTranslate($this->_tag_stack[count($this->_tag_stack)-1][1], $_block_content, $this, $_block_repeat); }  array_pop($this->_tag_stack); ?> <?php echo strtolower($this->_tpl_vars['res']['label']); ?>
</div>
		<?php $_from = $this->_tpl_vars['res']['data']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['k'] => $this->_tpl_vars['v']):
?>
		<span>
			<?php if ($this->_tpl_vars['v']['label']): ?><?php $this->_tag_stack[] = array('t', array()); $_block_repeat=true;$this->_plugins['block']['t'][0][0]->smartyTranslate($this->_tag_stack[count($this->_tag_stack)-1][1], null, $this, $_block_repeat);while ($_block_repeat) { ob_start(); ?>Step<?php $_block_content = ob_get_contents(); ob_end_clean(); $_block_repeat=false;echo $this->_plugins['block']['t'][0][0]->smartyTranslate($this->_tag_stack[count($this->_tag_stack)-1][1], $_block_content, $this, $_block_repeat); }  array_pop($this->_tag_stack); ?> <?php echo $this->_tpl_vars['v']['number']; ?>
:<?php $this->_tag_stack[] = array('h', array('search' => $this->_tpl_vars['search'])); $_block_repeat=true;$this->_plugins['block']['h'][0][0]->highlightFound($this->_tag_stack[count($this->_tag_stack)-1][1], null, $this, $_block_repeat);while ($_block_repeat) { ob_start(); ?> <?php echo $this->_tpl_vars['v']['label']; ?>
<?php $_block_content = ob_get_contents(); ob_end_clean(); $_block_repeat=false;echo $this->_plugins['block']['h'][0][0]->highlightFound($this->_tag_stack[count($this->_tag_stack)-1][1], $_block_content, $this, $_block_repeat); }  array_pop($this->_tag_stack); ?>
			<?php elseif ($this->_tpl_vars['v']['content']): ?><?php $this->_tag_stack[] = array('t', array()); $_block_repeat=true;$this->_plugins['block']['t'][0][0]->smartyTranslate($this->_tag_stack[count($this->_tag_stack)-1][1], null, $this, $_block_repeat);while ($_block_repeat) { ob_start(); ?>Step<?php $_block_content = ob_get_contents(); ob_end_clean(); $_block_repeat=false;echo $this->_plugins['block']['t'][0][0]->smartyTranslate($this->_tag_stack[count($this->_tag_stack)-1][1], $_block_content, $this, $_block_repeat); }  array_pop($this->_tag_stack); ?> <?php echo $this->_tpl_vars['v']['number']; ?>
 ("<?php echo $this->_tpl_vars['v']['title']; ?>
")<?php if ($this->_tpl_vars['v']['marker']): ?>, <?php $this->_tag_stack[] = array('t', array()); $_block_repeat=true;$this->_plugins['block']['t'][0][0]->smartyTranslate($this->_tag_stack[count($this->_tag_stack)-1][1], null, $this, $_block_repeat);while ($_block_repeat) { ob_start(); ?>choice<?php $_block_content = ob_get_contents(); ob_end_clean(); $_block_repeat=false;echo $this->_plugins['block']['t'][0][0]->smartyTranslate($this->_tag_stack[count($this->_tag_stack)-1][1], $_block_content, $this, $_block_repeat); }  array_pop($this->_tag_stack); ?> <?php echo $this->_tpl_vars['v']['marker']; ?>
<?php endif; ?>: "<?php $this->_tag_stack[] = array('foundContent', array('search' => $this->_tpl_vars['search'])); $_block_repeat=true;$this->_plugins['block']['foundContent'][0][0]->foundContent($this->_tag_stack[count($this->_tag_stack)-1][1], null, $this, $_block_repeat);while ($_block_repeat) { ob_start(); ?><?php echo $this->_tpl_vars['v']['content']; ?>
<?php $_block_content = ob_get_contents(); ob_end_clean(); $_block_repeat=false;echo $this->_plugins['block']['foundContent'][0][0]->foundContent($this->_tag_stack[count($this->_tag_stack)-1][1], $_block_content, $this, $_block_repeat); }  array_pop($this->_tag_stack); ?>"
			<?php endif; ?>
		</span><br/>
		<?php endforeach; endif; unset($_from); ?>
	</div>
	<?php endif; ?>
	<?php endforeach; endif; unset($_from); ?>
	<?php $this->_tag_stack[] = array('t', array('_s1' => '<a href="../key/">','_s2' => "</a>")); $_block_repeat=true;$this->_plugins['block']['t'][0][0]->smartyTranslate($this->_tag_stack[count($this->_tag_stack)-1][1], null, $this, $_block_repeat);while ($_block_repeat) { ob_start(); ?>It is not possible to jump directly to a specific step or choice of the dichotomous key. Click %shere%s to start the key from the start.<?php $_block_content = ob_get_contents(); ob_end_clean(); $_block_repeat=false;echo $this->_plugins['block']['t'][0][0]->smartyTranslate($this->_tag_stack[count($this->_tag_stack)-1][1], $_block_content, $this, $_block_repeat); }  array_pop($this->_tag_stack); ?>
</div>
<?php endif; ?>

<?php if ($this->_tpl_vars['results']['matrixkey']['numOfResults'] > 0): ?>
<div class="set">
	<?php $_from = $this->_tpl_vars['results']['matrixkey']['results']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['cat'] => $this->_tpl_vars['res']):
?>
	<?php if (count($this->_tpl_vars['res']['data']) > 0): ?>
	<div class="subset">
		<div class="set-header"><?php echo count($this->_tpl_vars['res']['data']); ?>
 <?php $this->_tag_stack[] = array('t', array()); $_block_repeat=true;$this->_plugins['block']['t'][0][0]->smartyTranslate($this->_tag_stack[count($this->_tag_stack)-1][1], null, $this, $_block_repeat);while ($_block_repeat) { ob_start(); ?>in<?php $_block_content = ob_get_contents(); ob_end_clean(); $_block_repeat=false;echo $this->_plugins['block']['t'][0][0]->smartyTranslate($this->_tag_stack[count($this->_tag_stack)-1][1], $_block_content, $this, $_block_repeat); }  array_pop($this->_tag_stack); ?> <?php echo strtolower($this->_tpl_vars['res']['label']); ?>
</div>
		<?php $_from = $this->_tpl_vars['res']['data']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['k'] => $this->_tpl_vars['v']):
?>
		<span <?php if (! $this->_tpl_vars['v']['matrices'] && $this->_tpl_vars['v']['matrix_id']): ?>class="result" onclick="goMatrix(<?php echo $this->_tpl_vars['v']['matrix_id']; ?>
)<?php endif; ?>">
			<?php if ($this->_tpl_vars['v']['label']): ?><?php $this->_tag_stack[] = array('h', array('search' => $this->_tpl_vars['search'])); $_block_repeat=true;$this->_plugins['block']['h'][0][0]->highlightFound($this->_tag_stack[count($this->_tag_stack)-1][1], null, $this, $_block_repeat);while ($_block_repeat) { ob_start(); ?><?php echo $this->_tpl_vars['v']['label']; ?>
<?php $_block_content = ob_get_contents(); ob_end_clean(); $_block_repeat=false;echo $this->_plugins['block']['h'][0][0]->highlightFound($this->_tag_stack[count($this->_tag_stack)-1][1], $_block_content, $this, $_block_repeat); }  array_pop($this->_tag_stack); ?><?php endif; ?>
			<?php if ($this->_tpl_vars['v']['content']): ?>: "<?php $this->_tag_stack[] = array('foundContent', array('search' => $this->_tpl_vars['search'])); $_block_repeat=true;$this->_plugins['block']['foundContent'][0][0]->foundContent($this->_tag_stack[count($this->_tag_stack)-1][1], null, $this, $_block_repeat);while ($_block_repeat) { ob_start(); ?><?php echo $this->_tpl_vars['v']['content']; ?>
<?php $_block_content = ob_get_contents(); ob_end_clean(); $_block_repeat=false;echo $this->_plugins['block']['foundContent'][0][0]->foundContent($this->_tag_stack[count($this->_tag_stack)-1][1], $_block_content, $this, $_block_repeat); }  array_pop($this->_tag_stack); ?>"<?php endif; ?>
			<?php if ($this->_tpl_vars['v']['characteristic']): ?>(of characteristic "<?php echo $this->_tpl_vars['v']['characteristic']; ?>
"<?php if (! $this->_tpl_vars['v']['matrices']): ?>)<?php endif; ?><?php endif; ?>
			<?php if ($this->_tpl_vars['v']['matrices']): ?><?php if (! $this->_tpl_vars['v']['characteristic']): ?>(<?php endif; ?><?php if (count($this->_tpl_vars['v']['matrices']) == 1): ?>in matrix<?php else: ?>in matrices<?php endif; ?>
			<?php $_from = $this->_tpl_vars['v']['matrices']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }$this->_foreach['matrices'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['matrices']['total'] > 0):
    foreach ($_from as $this->_tpl_vars['k'] => $this->_tpl_vars['m']):
        $this->_foreach['matrices']['iteration']++;
?><?php if (($this->_foreach['matrices']['iteration']-1) !== 0): ?>, <?php endif; ?>"<span class="result" onclick="goMatrix(<?php echo $this->_tpl_vars['m']['matrix_id']; ?>
)"><?php echo $this->_tpl_vars['results']['matrixkey']['matrices'][$this->_tpl_vars['m']['matrix_id']]['name']; ?>
</span>"<?php endforeach; endif; unset($_from); ?>)<?php endif; ?>
		</span><br/>
		<?php endforeach; endif; unset($_from); ?>
	</div>
	<?php endif; ?>
	<?php endforeach; endif; unset($_from); ?>
</div>
<?php endif; ?>

<?php if ($this->_tpl_vars['results']['map']['numOfResults'] > 0): ?>
<div class="set">
	<?php $_from = $this->_tpl_vars['results']['map']['results']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['cat'] => $this->_tpl_vars['res']):
?>
	<?php if (count($this->_tpl_vars['res']['data']) > 0): ?>
	<div class="subset">
		<div class="set-header"><?php echo count($this->_tpl_vars['res']['data']); ?>
 <?php $this->_tag_stack[] = array('t', array()); $_block_repeat=true;$this->_plugins['block']['t'][0][0]->smartyTranslate($this->_tag_stack[count($this->_tag_stack)-1][1], null, $this, $_block_repeat);while ($_block_repeat) { ob_start(); ?>in<?php $_block_content = ob_get_contents(); ob_end_clean(); $_block_repeat=false;echo $this->_plugins['block']['t'][0][0]->smartyTranslate($this->_tag_stack[count($this->_tag_stack)-1][1], $_block_content, $this, $_block_repeat); }  array_pop($this->_tag_stack); ?> <?php echo strtolower($this->_tpl_vars['res']['label']); ?>
</div>
		<?php $_from = $this->_tpl_vars['res']['data']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['k'] => $this->_tpl_vars['v']):
?>
		<span class="result" onclick="goMap(<?php echo $this->_tpl_vars['v']['id']; ?>
)"><?php $this->_tag_stack[] = array('h', array('search' => $this->_tpl_vars['search'])); $_block_repeat=true;$this->_plugins['block']['h'][0][0]->highlightFound($this->_tag_stack[count($this->_tag_stack)-1][1], null, $this, $_block_repeat);while ($_block_repeat) { ob_start(); ?><?php echo $this->_tpl_vars['v']['content']; ?>
<?php $_block_content = ob_get_contents(); ob_end_clean(); $_block_repeat=false;echo $this->_plugins['block']['h'][0][0]->highlightFound($this->_tag_stack[count($this->_tag_stack)-1][1], $_block_content, $this, $_block_repeat); }  array_pop($this->_tag_stack); ?></span> (<?php echo $this->_tpl_vars['v']['number']; ?>
 occurrences)<br/>
		<?php endforeach; endif; unset($_from); ?>
	</div>
	<?php endif; ?>
	<?php endforeach; endif; unset($_from); ?>
</div>
<?php endif; ?>

<?php if ($this->_tpl_vars['results']['modules']['numOfResults'] > 0): ?>
<div class="set">
	<?php $_from = $this->_tpl_vars['results']['modules']['results']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['cat'] => $this->_tpl_vars['res']):
?>
	<?php if (count($this->_tpl_vars['res']['data']) > 0): ?>
	<div class="subset">
		<div class="set-header"><?php echo count($this->_tpl_vars['res']['data']); ?>
 <?php $this->_tag_stack[] = array('t', array()); $_block_repeat=true;$this->_plugins['block']['t'][0][0]->smartyTranslate($this->_tag_stack[count($this->_tag_stack)-1][1], null, $this, $_block_repeat);while ($_block_repeat) { ob_start(); ?>in<?php $_block_content = ob_get_contents(); ob_end_clean(); $_block_repeat=false;echo $this->_plugins['block']['t'][0][0]->smartyTranslate($this->_tag_stack[count($this->_tag_stack)-1][1], $_block_content, $this, $_block_repeat); }  array_pop($this->_tag_stack); ?> <?php echo strtolower($this->_tpl_vars['res']['label']); ?>
</div>
		<?php $_from = $this->_tpl_vars['res']['data']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['k'] => $this->_tpl_vars['v']):
?>
		<span class="result" onclick="goModuleTopic(<?php echo $this->_tpl_vars['v']['page_id']; ?>
,<?php echo $this->_tpl_vars['v']['module_id']; ?>
)">
			<?php if ($this->_tpl_vars['v']['label']): ?><?php $this->_tag_stack[] = array('h', array('search' => $this->_tpl_vars['search'])); $_block_repeat=true;$this->_plugins['block']['h'][0][0]->highlightFound($this->_tag_stack[count($this->_tag_stack)-1][1], null, $this, $_block_repeat);while ($_block_repeat) { ob_start(); ?><?php echo $this->_tpl_vars['v']['label']; ?>
<?php $_block_content = ob_get_contents(); ob_end_clean(); $_block_repeat=false;echo $this->_plugins['block']['h'][0][0]->highlightFound($this->_tag_stack[count($this->_tag_stack)-1][1], $_block_content, $this, $_block_repeat); }  array_pop($this->_tag_stack); ?>
			<?php elseif ($this->_tpl_vars['v']['content']): ?><?php echo $this->_tpl_vars['v']['topic']; ?>
: "<?php $this->_tag_stack[] = array('foundContent', array('search' => $this->_tpl_vars['search'])); $_block_repeat=true;$this->_plugins['block']['foundContent'][0][0]->foundContent($this->_tag_stack[count($this->_tag_stack)-1][1], null, $this, $_block_repeat);while ($_block_repeat) { ob_start(); ?><?php echo $this->_tpl_vars['v']['content']; ?>
<?php $_block_content = ob_get_contents(); ob_end_clean(); $_block_repeat=false;echo $this->_plugins['block']['foundContent'][0][0]->foundContent($this->_tag_stack[count($this->_tag_stack)-1][1], $_block_content, $this, $_block_repeat); }  array_pop($this->_tag_stack); ?>"
			<?php endif; ?>
		</span><br/>
		<?php endforeach; endif; unset($_from); ?>
	</div>
	<?php endif; ?>
	<?php endforeach; endif; unset($_from); ?>
</div>
<?php endif; ?>

<?php if ($this->_tpl_vars['results']['content']['numOfResults'] > 0): ?>
<div class="set">
	<?php $_from = $this->_tpl_vars['results']['content']['results']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['cat'] => $this->_tpl_vars['res']):
?>
	<?php if (count($this->_tpl_vars['res']['data']) > 0): ?>
	<div class="subset">
		<div class="set-header"><?php echo count($this->_tpl_vars['res']['data']); ?>
 <?php $this->_tag_stack[] = array('t', array()); $_block_repeat=true;$this->_plugins['block']['t'][0][0]->smartyTranslate($this->_tag_stack[count($this->_tag_stack)-1][1], null, $this, $_block_repeat);while ($_block_repeat) { ob_start(); ?>in<?php $_block_content = ob_get_contents(); ob_end_clean(); $_block_repeat=false;echo $this->_plugins['block']['t'][0][0]->smartyTranslate($this->_tag_stack[count($this->_tag_stack)-1][1], $_block_content, $this, $_block_repeat); }  array_pop($this->_tag_stack); ?> <?php echo strtolower($this->_tpl_vars['res']['label']); ?>
</div>
		<?php $_from = $this->_tpl_vars['res']['data']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['k'] => $this->_tpl_vars['v']):
?>
		<span class="result" onclick="goContent(<?php echo $this->_tpl_vars['v']['id']; ?>
)">
			<?php $this->_tag_stack[] = array('h', array('search' => $this->_tpl_vars['search'])); $_block_repeat=true;$this->_plugins['block']['h'][0][0]->highlightFound($this->_tag_stack[count($this->_tag_stack)-1][1], null, $this, $_block_repeat);while ($_block_repeat) { ob_start(); ?><?php echo $this->_tpl_vars['v']['label']; ?>
<?php $_block_content = ob_get_contents(); ob_end_clean(); $_block_repeat=false;echo $this->_plugins['block']['h'][0][0]->highlightFound($this->_tag_stack[count($this->_tag_stack)-1][1], $_block_content, $this, $_block_repeat); }  array_pop($this->_tag_stack); ?><?php if ($this->_tpl_vars['v']['content']): ?>: "<?php $this->_tag_stack[] = array('foundContent', array('search' => $this->_tpl_vars['search'])); $_block_repeat=true;$this->_plugins['block']['foundContent'][0][0]->foundContent($this->_tag_stack[count($this->_tag_stack)-1][1], null, $this, $_block_repeat);while ($_block_repeat) { ob_start(); ?><?php echo $this->_tpl_vars['v']['content']; ?>
<?php $_block_content = ob_get_contents(); ob_end_clean(); $_block_repeat=false;echo $this->_plugins['block']['foundContent'][0][0]->foundContent($this->_tag_stack[count($this->_tag_stack)-1][1], $_block_content, $this, $_block_repeat); }  array_pop($this->_tag_stack); ?>"<?php endif; ?>
		</span><br/>
		<?php endforeach; endif; unset($_from); ?>
	</div>
	<?php endif; ?>
	<?php endforeach; endif; unset($_from); ?>
</div>
<?php endif; ?>

</div>
<?php endif; ?>
</div>

<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "../shared/messages.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "../shared/footer.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
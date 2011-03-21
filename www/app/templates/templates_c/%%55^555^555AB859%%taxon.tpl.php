<?php /* Smarty version 2.6.26, created on 2011-03-21 17:24:31
         compiled from ../species/taxon.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('block', 't', '../species/taxon.tpl', 10, false),array('modifier', 'count', '../species/taxon.tpl', 29, false),)), $this); ?>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "../shared/header.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>

<div id="categories">
<table>
	<tr>
	<?php $_from = $this->_tpl_vars['categories']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['k'] => $this->_tpl_vars['v']):
?>
		<td <?php if ($this->_tpl_vars['activeCategory'] == $this->_tpl_vars['v']['id']): ?>class="category-active"<?php else: ?>class="category" onclick="goTaxon(<?php echo $this->_tpl_vars['taxon']['id']; ?>
,<?php echo $this->_tpl_vars['v']['id']; ?>
)"<?php endif; ?>><?php echo $this->_tpl_vars['v']['title']; ?>
</td><td class="space"></td>
		<?php endforeach; endif; unset($_from); ?>
<?php if ($this->_tpl_vars['contentCount']['media'] > 0): ?>
		<td <?php if ($this->_tpl_vars['activeCategory'] == 'media'): ?>class="category-active"<?php else: ?>class="category" onclick="goTaxon(<?php echo $this->_tpl_vars['taxon']['id']; ?>
,'media')"<?php endif; ?>><?php $this->_tag_stack[] = array('t', array()); $_block_repeat=true;$this->_plugins['block']['t'][0][0]->smartyTranslate($this->_tag_stack[count($this->_tag_stack)-1][1], null, $this, $_block_repeat);while ($_block_repeat) { ob_start(); ?>Media<?php $_block_content = ob_get_contents(); ob_end_clean(); $_block_repeat=false;echo $this->_plugins['block']['t'][0][0]->smartyTranslate($this->_tag_stack[count($this->_tag_stack)-1][1], $_block_content, $this, $_block_repeat); }  array_pop($this->_tag_stack); ?></td><td class="space"></td>
<?php endif; ?>
		<td <?php if ($this->_tpl_vars['activeCategory'] == 'classification'): ?>class="category-active"<?php else: ?>class="category" onclick="goTaxon(<?php echo $this->_tpl_vars['taxon']['id']; ?>
,'classification')"<?php endif; ?>><?php $this->_tag_stack[] = array('t', array()); $_block_repeat=true;$this->_plugins['block']['t'][0][0]->smartyTranslate($this->_tag_stack[count($this->_tag_stack)-1][1], null, $this, $_block_repeat);while ($_block_repeat) { ob_start(); ?>Classification<?php $_block_content = ob_get_contents(); ob_end_clean(); $_block_repeat=false;echo $this->_plugins['block']['t'][0][0]->smartyTranslate($this->_tag_stack[count($this->_tag_stack)-1][1], $_block_content, $this, $_block_repeat); }  array_pop($this->_tag_stack); ?></td><td class="space"></td>
<?php if ($this->_tpl_vars['contentCount']['literature'] > 0): ?>
		<td <?php if ($this->_tpl_vars['activeCategory'] == 'literature'): ?>class="category-active"<?php else: ?>class="category" onclick="goTaxon(<?php echo $this->_tpl_vars['taxon']['id']; ?>
,'literature')"<?php endif; ?>><?php $this->_tag_stack[] = array('t', array()); $_block_repeat=true;$this->_plugins['block']['t'][0][0]->smartyTranslate($this->_tag_stack[count($this->_tag_stack)-1][1], null, $this, $_block_repeat);while ($_block_repeat) { ob_start(); ?>Literature<?php $_block_content = ob_get_contents(); ob_end_clean(); $_block_repeat=false;echo $this->_plugins['block']['t'][0][0]->smartyTranslate($this->_tag_stack[count($this->_tag_stack)-1][1], $_block_content, $this, $_block_repeat); }  array_pop($this->_tag_stack); ?></td><td class="space"></td>
<?php endif; ?>
<?php if ($this->_tpl_vars['contentCount']['names'] > 0): ?>
		<td <?php if ($this->_tpl_vars['activeCategory'] == 'names'): ?>class="category-active"<?php else: ?>class="category" onclick="goTaxon(<?php echo $this->_tpl_vars['taxon']['id']; ?>
,'names')"<?php endif; ?>><?php $this->_tag_stack[] = array('t', array()); $_block_repeat=true;$this->_plugins['block']['t'][0][0]->smartyTranslate($this->_tag_stack[count($this->_tag_stack)-1][1], null, $this, $_block_repeat);while ($_block_repeat) { ob_start(); ?>Synonyms<?php $_block_content = ob_get_contents(); ob_end_clean(); $_block_repeat=false;echo $this->_plugins['block']['t'][0][0]->smartyTranslate($this->_tag_stack[count($this->_tag_stack)-1][1], $_block_content, $this, $_block_repeat); }  array_pop($this->_tag_stack); ?></td>
<?php endif; ?>
	</tr>
</table>
</div>

<div id="page-main">
<?php if ($this->_tpl_vars['activeCategory'] == 'classification'): ?>
<div id="classification">
	<table>
	<?php $_from = $this->_tpl_vars['content']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }$this->_foreach['classification'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['classification']['total'] > 0):
    foreach ($_from as $this->_tpl_vars['k'] => $this->_tpl_vars['v']):
        $this->_foreach['classification']['iteration']++;
?>
		<tr>
			<td <?php if (($this->_foreach['classification']['iteration']-1) == count($this->_tpl_vars['content']) -1): ?>class="current-taxon"<?php else: ?>class="a" onclick="<?php if ($this->_tpl_vars['v']['lower_taxon'] == 1): ?>goTaxon<?php else: ?>goHigherTaxon<?php endif; ?>(<?php echo $this->_tpl_vars['v']['id']; ?>
)"<?php endif; ?>><?php echo $this->_tpl_vars['v']['taxon']; ?>
</td>
			<td>(<?php echo $this->_tpl_vars['v']['rank']; ?>
)</td>
		</tr>
	<?php endforeach; endif; unset($_from); ?>
	</table>
</div>
<?php elseif ($this->_tpl_vars['activeCategory'] == 'literature' && $this->_tpl_vars['contentCount']['literature'] > 0): ?>
<div id="literature">
	<?php $_from = $this->_tpl_vars['content']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['k'] => $this->_tpl_vars['v']):
?>
	<div class="author">
		<span class="name">
			<?php echo $this->_tpl_vars['v']['author_full']; ?>

		</span>
		<span class="year"><?php echo $this->_tpl_vars['v']['year']; ?>
</span>
	</div>
	<div class="text"><?php echo $this->_tpl_vars['v']['text']; ?>
</div>
	<?php endforeach; endif; unset($_from); ?>
</div>
<?php elseif ($this->_tpl_vars['activeCategory'] == 'names' && $this->_tpl_vars['contentCount']['names'] > 0): ?>
<?php if ($this->_tpl_vars['content']['synonyms']): ?>
<div id="synonyms">
	<div class="title"><?php $this->_tag_stack[] = array('t', array()); $_block_repeat=true;$this->_plugins['block']['t'][0][0]->smartyTranslate($this->_tag_stack[count($this->_tag_stack)-1][1], null, $this, $_block_repeat);while ($_block_repeat) { ob_start(); ?>Synonyms<?php $_block_content = ob_get_contents(); ob_end_clean(); $_block_repeat=false;echo $this->_plugins['block']['t'][0][0]->smartyTranslate($this->_tag_stack[count($this->_tag_stack)-1][1], $_block_content, $this, $_block_repeat); }  array_pop($this->_tag_stack); ?></div>
	<table>
	<?php $_from = $this->_tpl_vars['content']['synonyms']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['k'] => $this->_tpl_vars['v']):
?>
		<tr class="highlight">
			<td><?php echo $this->_tpl_vars['v']['synonym']; ?>
</td>
			<td><?php if ($this->_tpl_vars['v']['reference']): ?><span onclick="goLiterature(<?php echo $this->_tpl_vars['v']['reference']['id']; ?>
);" class="a"><?php echo $this->_tpl_vars['v']['reference']['author_full']; ?>
</span><?php endif; ?></td>
		</tr>
			<?php endforeach; endif; unset($_from); ?>
	</table>
</div>
<?php endif; ?>
<?php if ($this->_tpl_vars['content']['common']): ?>
<div id="common">
	<div class="title"><?php $this->_tag_stack[] = array('t', array()); $_block_repeat=true;$this->_plugins['block']['t'][0][0]->smartyTranslate($this->_tag_stack[count($this->_tag_stack)-1][1], null, $this, $_block_repeat);while ($_block_repeat) { ob_start(); ?>Common Names<?php $_block_content = ob_get_contents(); ob_end_clean(); $_block_repeat=false;echo $this->_plugins['block']['t'][0][0]->smartyTranslate($this->_tag_stack[count($this->_tag_stack)-1][1], $_block_content, $this, $_block_repeat); }  array_pop($this->_tag_stack); ?></div>
	<table>
	<thead>
		<tr class="highlight">
			<th><?php $this->_tag_stack[] = array('t', array()); $_block_repeat=true;$this->_plugins['block']['t'][0][0]->smartyTranslate($this->_tag_stack[count($this->_tag_stack)-1][1], null, $this, $_block_repeat);while ($_block_repeat) { ob_start(); ?>Common name<?php $_block_content = ob_get_contents(); ob_end_clean(); $_block_repeat=false;echo $this->_plugins['block']['t'][0][0]->smartyTranslate($this->_tag_stack[count($this->_tag_stack)-1][1], $_block_content, $this, $_block_repeat); }  array_pop($this->_tag_stack); ?></th>
			<th><?php $this->_tag_stack[] = array('t', array()); $_block_repeat=true;$this->_plugins['block']['t'][0][0]->smartyTranslate($this->_tag_stack[count($this->_tag_stack)-1][1], null, $this, $_block_repeat);while ($_block_repeat) { ob_start(); ?>Transliteration<?php $_block_content = ob_get_contents(); ob_end_clean(); $_block_repeat=false;echo $this->_plugins['block']['t'][0][0]->smartyTranslate($this->_tag_stack[count($this->_tag_stack)-1][1], $_block_content, $this, $_block_repeat); }  array_pop($this->_tag_stack); ?></th>
			<th><?php $this->_tag_stack[] = array('t', array()); $_block_repeat=true;$this->_plugins['block']['t'][0][0]->smartyTranslate($this->_tag_stack[count($this->_tag_stack)-1][1], null, $this, $_block_repeat);while ($_block_repeat) { ob_start(); ?>Language<?php $_block_content = ob_get_contents(); ob_end_clean(); $_block_repeat=false;echo $this->_plugins['block']['t'][0][0]->smartyTranslate($this->_tag_stack[count($this->_tag_stack)-1][1], $_block_content, $this, $_block_repeat); }  array_pop($this->_tag_stack); ?></th>
		</tr>
	</thead>
	<tbody>
	<?php $_from = $this->_tpl_vars['content']['common']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['k'] => $this->_tpl_vars['v']):
?>
		<tr class="highlight">
			<td><?php echo $this->_tpl_vars['v']['commonname']; ?>
</td>
			<td><?php echo $this->_tpl_vars['v']['transliteration']; ?>
</td>
			<td><?php echo $this->_tpl_vars['v']['language_name']; ?>
</td>
		</tr>
	<?php endforeach; endif; unset($_from); ?>
	</tbody>
	</table>
</div>
<?php endif; ?>
<?php elseif ($this->_tpl_vars['activeCategory'] == 'media' && $this->_tpl_vars['contentCount']['media'] > 0): ?>
<div id="media">
	<table>
	<?php $this->assign('mediaCat', false); ?>
	<?php $_from = $this->_tpl_vars['content']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['k'] => $this->_tpl_vars['v']):
?>
	<?php if ($this->_tpl_vars['mediaCat'] != $this->_tpl_vars['v']['category']): ?>
	<?php if ($this->_tpl_vars['k'] != 0): ?>
	<tr>
		<td colspan="2">&nbsp;</td>
	</tr>
	<?php endif; ?>
	<tr>
		<td colspan="2" class="media-cat-header"><?php echo $this->_tpl_vars['v']['category_label']; ?>
</td>
	</tr>
	<?php endif; ?>
	<tr>
		<td class="media-image-cell">
	<?php if ($this->_tpl_vars['v']['category'] == 'image'): ?>
		<?php if ($this->_tpl_vars['v']['thumb_name'] != ''): ?>
			<img
				onclick="showMedia('<?php echo $this->_tpl_vars['session']['project']['urls']['project_media']; ?>
<?php echo $this->_tpl_vars['v']['file_name']; ?>
','<?php echo $this->_tpl_vars['v']['original_name']; ?>
');" 
				src="<?php echo $this->_tpl_vars['session']['project']['urls']['project_thumbs']; ?>
<?php echo $this->_tpl_vars['v']['thumb_name']; ?>
"
				class="media-image" />
		<?php else: ?>
			<img
				onclick="showMedia('<?php echo $this->_tpl_vars['session']['project']['urls']['project_media']; ?>
<?php echo $this->_tpl_vars['v']['file_name']; ?>
','<?php echo $this->_tpl_vars['v']['original_name']; ?>
');" 
				src="<?php echo $this->_tpl_vars['session']['project']['urls']['project_media']; ?>
<?php echo $this->_tpl_vars['v']['file_name']; ?>
"
				class="media-image" />
		<?php endif; ?>
	<?php elseif ($this->_tpl_vars['v']['category'] == 'video'): ?>
			<img 
				src="../../media/system/video.jpg" 
				onclick="showMedia('<?php echo $this->_tpl_vars['session']['project']['urls']['project_media']; ?>
<?php echo $this->_tpl_vars['v']['file_name']; ?>
','<?php echo $this->_tpl_vars['v']['original_name']; ?>
');" 
				class="media-video-icon" />
	<?php elseif ($this->_tpl_vars['v']['category'] == 'audio'): ?>
			<object type="application/x-shockwave-flash" data="../../media/system/player_mp3.swf" width="130" height="20">
				<param name="movie" value="player_mp3.swf" />
				<param name="FlashVars" value="mp3=<?php echo $this->_tpl_vars['session']['project']['urls']['project_media']; ?>
<?php echo $this->_tpl_vars['v']['file_name']; ?>
" />
			</object>
	<?php endif; ?>
			</td>
		<td class="media-description-cell"><?php echo $this->_tpl_vars['v']['description']; ?>
</td>
	</tr>
	<?php $this->assign('mediaCat', $this->_tpl_vars['v']['category']); ?>
	<?php endforeach; endif; unset($_from); ?>
	</table>
</div>
<?php else: ?>
<div id="content">
<?php echo $this->_tpl_vars['content']; ?>

</div>
<?php endif; ?>

	<div id="navigation">
		<span id="back" onclick="window.open('<?php if ($this->_tpl_vars['taxon']['lower_taxon'] == 1): ?>../species/<?php else: ?>../highertaxa/<?php endif; ?>','_self')"><?php $this->_tag_stack[] = array('t', array()); $_block_repeat=true;$this->_plugins['block']['t'][0][0]->smartyTranslate($this->_tag_stack[count($this->_tag_stack)-1][1], null, $this, $_block_repeat);while ($_block_repeat) { ob_start(); ?>back to index<?php $_block_content = ob_get_contents(); ob_end_clean(); $_block_repeat=false;echo $this->_plugins['block']['t'][0][0]->smartyTranslate($this->_tag_stack[count($this->_tag_stack)-1][1], $_block_content, $this, $_block_repeat); }  array_pop($this->_tag_stack); ?></span>
	</div>
</div>

<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "../shared/footer.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
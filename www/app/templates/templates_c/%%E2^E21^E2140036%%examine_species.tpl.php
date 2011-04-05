<?php /* Smarty version 2.6.26, created on 2011-04-05 12:19:12
         compiled from examine_species.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('block', 't', 'examine_species.tpl', 5, false),)), $this); ?>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "../shared/header.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>

<div id="page-main">
<?php if (! $this->_tpl_vars['isOnline']): ?>
<?php $this->_tag_stack[] = array('t', array()); $_block_repeat=true;$this->_plugins['block']['t'][0][0]->smartyTranslate($this->_tag_stack[count($this->_tag_stack)-1][1], null, $this, $_block_repeat);while ($_block_repeat) { ob_start(); ?>Your computer appears to be offline. Unfortunately, the map key doesn't work without an internet connection.<?php $_block_content = ob_get_contents(); ob_end_clean(); $_block_repeat=false;echo $this->_plugins['block']['t'][0][0]->smartyTranslate($this->_tag_stack[count($this->_tag_stack)-1][1], $_block_content, $this, $_block_repeat); }  array_pop($this->_tag_stack); ?>
<?php else: ?>

	<div id="map_canvas"><?php if (! $this->_tpl_vars['isOnline']): ?><?php $this->_tag_stack[] = array('t', array()); $_block_repeat=true;$this->_plugins['block']['t'][0][0]->smartyTranslate($this->_tag_stack[count($this->_tag_stack)-1][1], null, $this, $_block_repeat);while ($_block_repeat) { ob_start(); ?>Unable to display map.<?php $_block_content = ob_get_contents(); ob_end_clean(); $_block_repeat=false;echo $this->_plugins['block']['t'][0][0]->smartyTranslate($this->_tag_stack[count($this->_tag_stack)-1][1], $_block_content, $this, $_block_repeat); }  array_pop($this->_tag_stack); ?><?php endif; ?></div>
	<div id="map_options">
		<b><?php echo $this->_tpl_vars['taxon']['taxon']; ?>
</b><br/>
		<?php $this->_tag_stack[] = array('t', array()); $_block_repeat=true;$this->_plugins['block']['t'][0][0]->smartyTranslate($this->_tag_stack[count($this->_tag_stack)-1][1], null, $this, $_block_repeat);while ($_block_repeat) { ob_start(); ?>Coordinates:<?php $_block_content = ob_get_contents(); ob_end_clean(); $_block_repeat=false;echo $this->_plugins['block']['t'][0][0]->smartyTranslate($this->_tag_stack[count($this->_tag_stack)-1][1], $_block_content, $this, $_block_repeat); }  array_pop($this->_tag_stack); ?> <span id="coordinates">(-1,-1)</span><br />
		<hr style="height:1px;color:#999" />
		<table>
		<?php $_from = $this->_tpl_vars['geoDataTypes']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['k'] => $this->_tpl_vars['v']):
?>
		<?php if ($this->_tpl_vars['count']['data'][$this->_tpl_vars['k']]): ?>
			<tr style="vertical-align:top">
				<td style="width:25px;border:1px solid black;background-color:#<?php echo $this->_tpl_vars['v']['colour']; ?>
"></td>
				<td style="width:5px;"></td>
				<td style="width:215px;"><?php echo $this->_tpl_vars['v']['title']; ?>
 (<?php echo $this->_tpl_vars['count']['data'][$this->_tpl_vars['k']]; ?>
)</td>
				<td style="width:25px;" hidden="0" onclick="doMapTypeToggle(this,<?php echo $this->_tpl_vars['v']['id']; ?>
)" class="a">hide</td>
			</tr>
			<tr><td colspan="4" style="height:1px;"></td></tr>
		<?php endif; ?>
		<?php endforeach; endif; unset($_from); ?>
		<?php if ($this->_tpl_vars['count']['total'] == 0): ?>
			<tr><td colspan="4"><?php $this->_tag_stack[] = array('t', array()); $_block_repeat=true;$this->_plugins['block']['t'][0][0]->smartyTranslate($this->_tag_stack[count($this->_tag_stack)-1][1], null, $this, $_block_repeat);while ($_block_repeat) { ob_start(); ?>no data available<?php $_block_content = ob_get_contents(); ob_end_clean(); $_block_repeat=false;echo $this->_plugins['block']['t'][0][0]->smartyTranslate($this->_tag_stack[count($this->_tag_stack)-1][1], $_block_content, $this, $_block_repeat); }  array_pop($this->_tag_stack); ?></td></tr>
		<?php endif; ?>
		</table>
		<hr style="height:1px;color:#999" />
		<span id="back" onclick="goMap(null,'examine.php')"><?php $this->_tag_stack[] = array('t', array()); $_block_repeat=true;$this->_plugins['block']['t'][0][0]->smartyTranslate($this->_tag_stack[count($this->_tag_stack)-1][1], null, $this, $_block_repeat);while ($_block_repeat) { ob_start(); ?>back to species list<?php $_block_content = ob_get_contents(); ob_end_clean(); $_block_repeat=false;echo $this->_plugins['block']['t'][0][0]->smartyTranslate($this->_tag_stack[count($this->_tag_stack)-1][1], $_block_content, $this, $_block_repeat); }  array_pop($this->_tag_stack); ?></span>

		<?php if ($this->_tpl_vars['showBackToSearch'] && $this->_tpl_vars['session']['user']['search']['hasSearchResults']): ?>
		<p>
		<span class="back-link" onclick="window.open('../linnaeus/redosearch.php','_self')"><?php $this->_tag_stack[] = array('t', array()); $_block_repeat=true;$this->_plugins['block']['t'][0][0]->smartyTranslate($this->_tag_stack[count($this->_tag_stack)-1][1], null, $this, $_block_repeat);while ($_block_repeat) { ob_start(); ?>back to search results<?php $_block_content = ob_get_contents(); ob_end_clean(); $_block_repeat=false;echo $this->_plugins['block']['t'][0][0]->smartyTranslate($this->_tag_stack[count($this->_tag_stack)-1][1], $_block_content, $this, $_block_repeat); }  array_pop($this->_tag_stack); ?></span>
		</p>
		<?php endif; ?>

	</div>

</div>

<?php echo '
<script type="text/JavaScript">
$(document).ready(function(){
'; ?>


	initMap(<?php echo $this->_tpl_vars['mapInitString']; ?>
);
	<?php if ($this->_tpl_vars['mapBorder']): ?>
	map.fitBounds(new google.maps.LatLngBounds(new google.maps.LatLng(<?php echo $this->_tpl_vars['mapBorder']['sw']['lat']; ?>
, <?php echo $this->_tpl_vars['mapBorder']['sw']['lng']; ?>
), new google.maps.LatLng(<?php echo $this->_tpl_vars['mapBorder']['ne']['lat']; ?>
, <?php echo $this->_tpl_vars['mapBorder']['ne']['lng']; ?>
)));
	<?php endif; ?>

<?php $_from = $this->_tpl_vars['occurrences']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['k'] => $this->_tpl_vars['v']):
?>

<?php if ($this->_tpl_vars['taxon']): ?>
<?php $this->assign('taxonName', $this->_tpl_vars['taxon']['taxon']); ?>
<?php else: ?>
<?php $this->assign('taxonName', $this->_tpl_vars['v']['taxon']['taxon']); ?>
<?php endif; ?>

<?php if ($this->_tpl_vars['v']['type'] == 'marker' && $this->_tpl_vars['v']['latitude'] && $this->_tpl_vars['v']['longitude']): ?>
	placeMarker([<?php echo $this->_tpl_vars['v']['latitude']; ?>
,<?php echo $this->_tpl_vars['v']['longitude']; ?>
],<?php echo '{'; ?>

		name: '<?php echo $this->_tpl_vars['taxonName']; ?>
',
		addMarker: true,
		addDelete: false,
		occurrenceId: <?php echo $this->_tpl_vars['v']['id']; ?>
,
		colour:'<?php echo $this->_tpl_vars['v']['colour']; ?>
',
		typeId:<?php echo $this->_tpl_vars['v']['type_id']; ?>

	<?php echo '});'; ?>

<?php elseif ($this->_tpl_vars['v']['type'] == 'polygon' && $this->_tpl_vars['v']['nodes']): ?>
	var nodes<?php echo $this->_tpl_vars['k']; ?>
 = Array();
	<?php $_from = $this->_tpl_vars['v']['nodes']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['kn'] => $this->_tpl_vars['vn']):
?>
	nodes<?php echo $this->_tpl_vars['k']; ?>
[<?php echo $this->_tpl_vars['kn']; ?>
] = [<?php echo $this->_tpl_vars['vn'][0]; ?>
, <?php echo $this->_tpl_vars['vn'][1]; ?>
];
	<?php endforeach; endif; unset($_from); ?>
	drawPolygon(nodes<?php echo $this->_tpl_vars['k']; ?>
,null,<?php echo '{'; ?>

		name: '<?php echo $this->_tpl_vars['taxonName']; ?>
',
		addMarker: true,
		addDelete: false,
		occurrenceId: <?php echo $this->_tpl_vars['v']['id']; ?>
,
		colour:'<?php echo $this->_tpl_vars['v']['colour']; ?>
',
		typeId:<?php echo $this->_tpl_vars['v']['type_id']; ?>

	<?php echo '});'; ?>


<?php endif; ?>
<?php endforeach; endif; unset($_from); ?>

<?php echo '
});
</script>
'; ?>





<?php endif; ?>
</div>

<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "../shared/footer.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
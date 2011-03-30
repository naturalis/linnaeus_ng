<?php /* Smarty version 2.6.26, created on 2011-03-30 19:24:44
         compiled from search.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('block', 't', 'search.tpl', 5, false),)), $this); ?>
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
		<form method="post" action="" id="theForm">
		<?php $this->_tag_stack[] = array('t', array()); $_block_repeat=true;$this->_plugins['block']['t'][0][0]->smartyTranslate($this->_tag_stack[count($this->_tag_stack)-1][1], null, $this, $_block_repeat);while ($_block_repeat) { ob_start(); ?>Coordinates:<?php $_block_content = ob_get_contents(); ob_end_clean(); $_block_repeat=false;echo $this->_plugins['block']['t'][0][0]->smartyTranslate($this->_tag_stack[count($this->_tag_stack)-1][1], $_block_content, $this, $_block_repeat); }  array_pop($this->_tag_stack); ?> <span id="coordinates">(-1,-1)</span><br />
		<hr style="height:1px;color:#999" />
	
		<input type="button" onclick="startPolygonDraw()" id="button-draw" value="<?php $this->_tag_stack[] = array('t', array()); $_block_repeat=true;$this->_plugins['block']['t'][0][0]->smartyTranslate($this->_tag_stack[count($this->_tag_stack)-1][1], null, $this, $_block_repeat);while ($_block_repeat) { ob_start(); ?>draw area to search<?php $_block_content = ob_get_contents(); ob_end_clean(); $_block_repeat=false;echo $this->_plugins['block']['t'][0][0]->smartyTranslate($this->_tag_stack[count($this->_tag_stack)-1][1], $_block_content, $this, $_block_repeat); }  array_pop($this->_tag_stack); ?>" /><br/>
		<input type="button" onclick="doMapSearch()" value="<?php $this->_tag_stack[] = array('t', array()); $_block_repeat=true;$this->_plugins['block']['t'][0][0]->smartyTranslate($this->_tag_stack[count($this->_tag_stack)-1][1], null, $this, $_block_repeat);while ($_block_repeat) { ob_start(); ?>search<?php $_block_content = ob_get_contents(); ob_end_clean(); $_block_repeat=false;echo $this->_plugins['block']['t'][0][0]->smartyTranslate($this->_tag_stack[count($this->_tag_stack)-1][1], $_block_content, $this, $_block_repeat); }  array_pop($this->_tag_stack); ?>" />
		<input type="button" onclick="clearPolygon();clearSearchResults();" value="<?php $this->_tag_stack[] = array('t', array()); $_block_repeat=true;$this->_plugins['block']['t'][0][0]->smartyTranslate($this->_tag_stack[count($this->_tag_stack)-1][1], null, $this, $_block_repeat);while ($_block_repeat) { ob_start(); ?>clear<?php $_block_content = ob_get_contents(); ob_end_clean(); $_block_repeat=false;echo $this->_plugins['block']['t'][0][0]->smartyTranslate($this->_tag_stack[count($this->_tag_stack)-1][1], $_block_content, $this, $_block_repeat); }  array_pop($this->_tag_stack); ?>" />
		</form>
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
				<td style="width:25px;" hidden="0" onclick="doMapTypeToggle(<?php echo $this->_tpl_vars['v']['id']; ?>
,this)" class="a">hide</td>
			</tr>
			<tr><td colspan="4" style="height:1px;"></td></tr>
		<?php endif; ?>
		<?php endforeach; endif; unset($_from); ?>
		</table>
		<hr style="height:1px;color:#999" />
		<table>
			<tr><td colspan="2" ><b><?php $this->_tag_stack[] = array('t', array()); $_block_repeat=true;$this->_plugins['block']['t'][0][0]->smartyTranslate($this->_tag_stack[count($this->_tag_stack)-1][1], null, $this, $_block_repeat);while ($_block_repeat) { ob_start(); ?>Found species<?php $_block_content = ob_get_contents(); ob_end_clean(); $_block_repeat=false;echo $this->_plugins['block']['t'][0][0]->smartyTranslate($this->_tag_stack[count($this->_tag_stack)-1][1], $_block_content, $this, $_block_repeat); }  array_pop($this->_tag_stack); ?></b></td></tr>
		<?php $this->assign('prev', false); ?>
		<?php $_from = $this->_tpl_vars['results']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['k'] => $this->_tpl_vars['v']):
?>
		<?php if ($this->_tpl_vars['prev']['taxon_id'] != $this->_tpl_vars['v']['taxon_id']): ?>
			<tr style="vertical-align:top">
				<td style="width:245px;"><?php echo $this->_tpl_vars['taxa'][$this->_tpl_vars['v']['taxon_id']]['taxon']; ?>
 (<?php echo $this->_tpl_vars['count']['taxa'][$this->_tpl_vars['v']['taxon_id']]; ?>
)</td>
				<td style="width:25px;" hidden="0" onclick="doMapTypeToggle(null,this,<?php echo $this->_tpl_vars['v']['taxon_id']; ?>
)" class="a">hide</td>
			</tr>
			<tr><td colspan="2" style="height:1px;"></td></tr>
		<?php endif; ?>
		<?php $this->assign('prev', $this->_tpl_vars['v']); ?>
		<?php endforeach; endif; unset($_from); ?>

		</table>
	</div>

</div>

<?php echo '
<script type="text/JavaScript">
$(document).ready(function(){
'; ?>


	initMap(<?php echo $this->_tpl_vars['mapInitString']; ?>
);
	initMapSearch();
	<?php if ($this->_tpl_vars['mapBorder']): ?>
	map.fitBounds(new google.maps.LatLngBounds(new google.maps.LatLng(<?php echo $this->_tpl_vars['mapBorder']['sw']['lat']; ?>
, <?php echo $this->_tpl_vars['mapBorder']['sw']['lng']; ?>
), new google.maps.LatLng(<?php echo $this->_tpl_vars['mapBorder']['ne']['lat']; ?>
, <?php echo $this->_tpl_vars['mapBorder']['ne']['lng']; ?>
)));
	<?php endif; ?>

<?php $_from = $this->_tpl_vars['results']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['k'] => $this->_tpl_vars['v']):
?>

<?php if ($this->_tpl_vars['v']['type'] == 'marker' && $this->_tpl_vars['v']['latitude'] && $this->_tpl_vars['v']['longitude']): ?>
	placeMarker([<?php echo $this->_tpl_vars['v']['latitude']; ?>
,<?php echo $this->_tpl_vars['v']['longitude']; ?>
],<?php echo '{'; ?>

		name: '<?php echo $this->_tpl_vars['taxa'][$this->_tpl_vars['v']['taxon_id']]['taxon']; ?>
: <?php echo $this->_tpl_vars['geoDataTypes'][$this->_tpl_vars['v']['type_id']]['title']; ?>
',
		addMarker: true,
		addDelete: false,
		occurrenceId: <?php echo $this->_tpl_vars['v']['id']; ?>
,
		taxonId: <?php echo $this->_tpl_vars['v']['taxon_id']; ?>
,
		colour:'<?php echo $this->_tpl_vars['geoDataTypes'][$this->_tpl_vars['v']['type_id']]['colour']; ?>
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

		name: '<?php echo $this->_tpl_vars['taxa'][$this->_tpl_vars['v']['taxon_id']]['taxon']; ?>
: <?php echo $this->_tpl_vars['geoDataTypes'][$this->_tpl_vars['v']['type_id']]['title']; ?>
',
		addMarker: true,
		addDelete: false,
		occurrenceId: <?php echo $this->_tpl_vars['v']['id']; ?>
,
		taxonId: <?php echo $this->_tpl_vars['v']['taxon_id']; ?>
,
		colour:'<?php echo $this->_tpl_vars['geoDataTypes'][$this->_tpl_vars['v']['type_id']]['colour']; ?>
',
		typeId:<?php echo $this->_tpl_vars['v']['type_id']; ?>

	<?php echo '});'; ?>


<?php endif; ?>
<?php endforeach; endif; unset($_from); ?>

	setPredefPolygon('<?php echo $this->_tpl_vars['coordinates']; ?>
');

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
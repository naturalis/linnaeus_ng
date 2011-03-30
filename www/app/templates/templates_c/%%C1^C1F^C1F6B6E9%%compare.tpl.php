<?php /* Smarty version 2.6.26, created on 2011-03-30 16:28:36
         compiled from compare.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('block', 't', 'compare.tpl', 13, false),)), $this); ?>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "../shared/header.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<?php echo '
<style>
.taxon-select {
	font-size:inherit;
	height:25px;
}
</style>
'; ?>


<div id="page-main">
<?php if (! $this->_tpl_vars['isOnline']): ?>
<?php $this->_tag_stack[] = array('t', array()); $_block_repeat=true;$this->_plugins['block']['t'][0][0]->smartyTranslate($this->_tag_stack[count($this->_tag_stack)-1][1], null, $this, $_block_repeat);while ($_block_repeat) { ob_start(); ?>Your computer appears to be offline. Unfortunately, the map key doesn't work without an internet connection.<?php $_block_content = ob_get_contents(); ob_end_clean(); $_block_repeat=false;echo $this->_plugins['block']['t'][0][0]->smartyTranslate($this->_tag_stack[count($this->_tag_stack)-1][1], $_block_content, $this, $_block_repeat); }  array_pop($this->_tag_stack); ?>
<?php else: ?>

	<div id="map_canvas"><?php if (! $this->_tpl_vars['isOnline']): ?><?php $this->_tag_stack[] = array('t', array()); $_block_repeat=true;$this->_plugins['block']['t'][0][0]->smartyTranslate($this->_tag_stack[count($this->_tag_stack)-1][1], null, $this, $_block_repeat);while ($_block_repeat) { ob_start(); ?>Unable to display map.<?php $_block_content = ob_get_contents(); ob_end_clean(); $_block_repeat=false;echo $this->_plugins['block']['t'][0][0]->smartyTranslate($this->_tag_stack[count($this->_tag_stack)-1][1], $_block_content, $this, $_block_repeat); }  array_pop($this->_tag_stack); ?><?php endif; ?></div>
	<div id="map_options">

		<form method="post" action="">
		<p>
		Taxon A:
		<select name="idA" class="taxon-select">	
		<option value="" <?php if (! $this->_tpl_vars['taxonA']): ?>selected="selected"<?php endif; ?>>--choose taxon--</option>
		<?php $_from = $this->_tpl_vars['taxa']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['k'] => $this->_tpl_vars['v']):
?>
		<option value="<?php echo $this->_tpl_vars['v']['id']; ?>
" <?php if ($this->_tpl_vars['taxonA']['id'] == $this->_tpl_vars['v']['id']): ?>selected="selected"<?php endif; ?>><?php echo $this->_tpl_vars['v']['taxon']; ?>
</option>
		<?php endforeach; endif; unset($_from); ?>
		</select>	
		</p>
		<p>
		Taxon B:
		<select name="idB" class="taxon-select">	
		<option value="" <?php if (! $this->_tpl_vars['taxonB']): ?>selected="selected"<?php endif; ?>>--choose taxon--</option>
		<?php $_from = $this->_tpl_vars['taxa']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['k'] => $this->_tpl_vars['v']):
?>
		<option value="<?php echo $this->_tpl_vars['v']['id']; ?>
" <?php if ($this->_tpl_vars['taxonB']['id'] == $this->_tpl_vars['v']['id']): ?>selected="selected"<?php endif; ?>><?php echo $this->_tpl_vars['v']['taxon']; ?>
</option>
		<?php endforeach; endif; unset($_from); ?>
		</select>	
		</p>
		<p>
		<input type="submit" value="compare" />
		</p>
		</form>

		<hr style="height:1px;color:#999" />
		<?php $this->_tag_stack[] = array('t', array()); $_block_repeat=true;$this->_plugins['block']['t'][0][0]->smartyTranslate($this->_tag_stack[count($this->_tag_stack)-1][1], null, $this, $_block_repeat);while ($_block_repeat) { ob_start(); ?>Coordinates:<?php $_block_content = ob_get_contents(); ob_end_clean(); $_block_repeat=false;echo $this->_plugins['block']['t'][0][0]->smartyTranslate($this->_tag_stack[count($this->_tag_stack)-1][1], $_block_content, $this, $_block_repeat); }  array_pop($this->_tag_stack); ?> <span id="coordinates">(-1,-1)</span><br />

		<?php if ($this->_tpl_vars['taxonA'] && $this->_tpl_vars['taxonB']): ?>
		<hr style="height:1px;color:#999" />
		<b><?php $this->_tag_stack[] = array('t', array()); $_block_repeat=true;$this->_plugins['block']['t'][0][0]->smartyTranslate($this->_tag_stack[count($this->_tag_stack)-1][1], null, $this, $_block_repeat);while ($_block_repeat) { ob_start(); ?>Comparison<?php $_block_content = ob_get_contents(); ob_end_clean(); $_block_repeat=false;echo $this->_plugins['block']['t'][0][0]->smartyTranslate($this->_tag_stack[count($this->_tag_stack)-1][1], $_block_content, $this, $_block_repeat); }  array_pop($this->_tag_stack); ?></b><br />
		<?php $_from = $this->_tpl_vars['overlap']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['k'] => $this->_tpl_vars['v']):
?>
			<i><?php echo $this->_tpl_vars['geoDataTypes'][$this->_tpl_vars['v']['type_id']]['title']; ?>
</i>:
			<?php $this->_tag_stack[] = array('t', array('_s1' => $this->_tpl_vars['taxonA']['taxon'],'_s2' => $this->_tpl_vars['taxonB']['taxon'],'_s3' => $this->_tpl_vars['v']['total'])); $_block_repeat=true;$this->_plugins['block']['t'][0][0]->smartyTranslate($this->_tag_stack[count($this->_tag_stack)-1][1], null, $this, $_block_repeat);while ($_block_repeat) { ob_start(); ?>%s intersects or overlaps %s in %s instances.<?php $_block_content = ob_get_contents(); ob_end_clean(); $_block_repeat=false;echo $this->_plugins['block']['t'][0][0]->smartyTranslate($this->_tag_stack[count($this->_tag_stack)-1][1], $_block_content, $this, $_block_repeat); }  array_pop($this->_tag_stack); ?><br /><br />
		<?php endforeach; endif; unset($_from); ?>
		<?php if (! $this->_tpl_vars['overlap']): ?><?php $this->_tag_stack[] = array('t', array()); $_block_repeat=true;$this->_plugins['block']['t'][0][0]->smartyTranslate($this->_tag_stack[count($this->_tag_stack)-1][1], null, $this, $_block_repeat);while ($_block_repeat) { ob_start(); ?>There is no overlap between these two species.<?php $_block_content = ob_get_contents(); ob_end_clean(); $_block_repeat=false;echo $this->_plugins['block']['t'][0][0]->smartyTranslate($this->_tag_stack[count($this->_tag_stack)-1][1], $_block_content, $this, $_block_repeat); }  array_pop($this->_tag_stack); ?><?php endif; ?>
		<hr style="height:1px;color:#999" />
		<?php endif; ?>

		<table>
		<?php if ($this->_tpl_vars['taxonA']): ?>
			<tr style="vertical-align:top">
				<td colspan="4"><b><?php echo $this->_tpl_vars['taxonA']['taxon']; ?>
</b><?php if ($this->_tpl_vars['countA']['total'] > 0): ?> (<?php echo $this->_tpl_vars['countA']['total']; ?>
)<?php endif; ?></td>
			</tr>
			<?php $_from = $this->_tpl_vars['geoDataTypes']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['k'] => $this->_tpl_vars['v']):
?>
			<?php if ($this->_tpl_vars['countA']['data'][$this->_tpl_vars['k']]): ?>
				<tr style="vertical-align:top">
					<td style="width:25px;border:1px solid black;background-color:#<?php echo $this->_tpl_vars['v']['colour']; ?>
"></td>
					<td style="width:5px;"></td>
					<td style="width:215px;"><?php echo $this->_tpl_vars['v']['title']; ?>
 (<?php echo $this->_tpl_vars['countA']['data'][$this->_tpl_vars['k']]; ?>
)</td>
					<td style="width:25px;" hidden="0" onclick="doMapTypeToggle(<?php echo $this->_tpl_vars['v']['id']; ?>
,this,<?php echo $this->_tpl_vars['taxonA']['id']; ?>
)" class="a">hide</td>
				</tr>
				<tr><td colspan="4" style="height:1px;"></td></tr>
			<?php endif; ?>
			<?php endforeach; endif; unset($_from); ?>
			<?php if ($this->_tpl_vars['countA']['total'] == 0): ?>
				<tr><td colspan="4"><?php $this->_tag_stack[] = array('t', array()); $_block_repeat=true;$this->_plugins['block']['t'][0][0]->smartyTranslate($this->_tag_stack[count($this->_tag_stack)-1][1], null, $this, $_block_repeat);while ($_block_repeat) { ob_start(); ?>no data available<?php $_block_content = ob_get_contents(); ob_end_clean(); $_block_repeat=false;echo $this->_plugins['block']['t'][0][0]->smartyTranslate($this->_tag_stack[count($this->_tag_stack)-1][1], $_block_content, $this, $_block_repeat); }  array_pop($this->_tag_stack); ?></td></tr>
			<?php endif; ?>
		<?php endif; ?>
		
		<?php if ($this->_tpl_vars['taxonA'] && $this->_tpl_vars['taxonB']): ?>
			<tr style="vertical-align:top">
				<td colspan="4">&nbsp;</td>
			</tr>
		<?php endif; ?>
	
		<?php if ($this->_tpl_vars['taxonB']): ?>
			<tr style="vertical-align:top">
				<td colspan="4"><b><?php echo $this->_tpl_vars['taxonB']['taxon']; ?>
</b><?php if ($this->_tpl_vars['countB']['total'] > 0): ?> (<?php echo $this->_tpl_vars['countB']['total']; ?>
)<?php endif; ?></td>
			</tr>
			<?php $_from = $this->_tpl_vars['geoDataTypes']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['k'] => $this->_tpl_vars['v']):
?>
			<?php if ($this->_tpl_vars['countB']['data'][$this->_tpl_vars['k']]): ?>
				<tr style="vertical-align:top">
					<td style="width:25px;border:1px solid black;background-color:#<?php echo $this->_tpl_vars['v']['colour_inverse']; ?>
"></td>
					<td style="width:5px;"></td>
					<td style="width:215px;"><?php echo $this->_tpl_vars['v']['title']; ?>
 (<?php echo $this->_tpl_vars['countB']['data'][$this->_tpl_vars['k']]; ?>
)</td>
					<td style="width:25px;" hidden="0" onclick="doMapTypeToggle(<?php echo $this->_tpl_vars['v']['id']; ?>
,this,<?php echo $this->_tpl_vars['taxonB']['id']; ?>
)" class="a">hide</td>
				</tr>
				<tr><td colspan="4" style="height:1px;"></td></tr>
			<?php endif; ?>
			<?php endforeach; endif; unset($_from); ?>
			<?php if ($this->_tpl_vars['countB']['total'] == 0): ?>
				<tr><td colspan="4"><?php $this->_tag_stack[] = array('t', array()); $_block_repeat=true;$this->_plugins['block']['t'][0][0]->smartyTranslate($this->_tag_stack[count($this->_tag_stack)-1][1], null, $this, $_block_repeat);while ($_block_repeat) { ob_start(); ?>no data available<?php $_block_content = ob_get_contents(); ob_end_clean(); $_block_repeat=false;echo $this->_plugins['block']['t'][0][0]->smartyTranslate($this->_tag_stack[count($this->_tag_stack)-1][1], $_block_content, $this, $_block_repeat); }  array_pop($this->_tag_stack); ?></td></tr>
			<?php endif; ?>
		<?php endif; ?>
		</table>

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

<?php $_from = $this->_tpl_vars['occurrencesA']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
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

		name: '<?php echo $this->_tpl_vars['taxonA']['taxon']; ?>
: <?php echo $this->_tpl_vars['v']['type_title']; ?>
',
		addMarker: true,
		addDelete: false,
		occurrenceId: <?php echo $this->_tpl_vars['v']['id']; ?>
,
		taxonId: <?php echo $this->_tpl_vars['taxonA']['id']; ?>
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

		name: '<?php echo $this->_tpl_vars['taxonA']['taxon']; ?>
: <?php echo $this->_tpl_vars['v']['type_title']; ?>
',
		addMarker: true,
		addDelete: false,
		occurrenceId: <?php echo $this->_tpl_vars['v']['id']; ?>
,
		taxonId: <?php echo $this->_tpl_vars['taxonA']['id']; ?>
,
		colour:'<?php echo $this->_tpl_vars['v']['colour']; ?>
',
		typeId:<?php echo $this->_tpl_vars['v']['type_id']; ?>

	<?php echo '});'; ?>


<?php endif; ?>
<?php endforeach; endif; unset($_from); ?>

<?php $_from = $this->_tpl_vars['occurrencesB']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
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

		name: '<?php echo $this->_tpl_vars['taxonB']['taxon']; ?>
: <?php echo $this->_tpl_vars['v']['type_title']; ?>
',
		addMarker: true,
		addDelete: false,
		occurrenceId: <?php echo $this->_tpl_vars['v']['id']; ?>
,
		taxonId: <?php echo $this->_tpl_vars['taxonB']['id']; ?>
,
		colour:'<?php if ($this->_tpl_vars['occurrencesA'] && $this->_tpl_vars['occurrencesB']): ?><?php echo $this->_tpl_vars['v']['colour_inverse']; ?>
<?php else: ?><?php echo $this->_tpl_vars['v']['colour']; ?>
<?php endif; ?>',
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

		name: '<?php echo $this->_tpl_vars['taxonB']['taxon']; ?>
: <?php echo $this->_tpl_vars['v']['type_title']; ?>
',
		addMarker: true,
		addDelete: false,
		occurrenceId: <?php echo $this->_tpl_vars['v']['id']; ?>
,
		taxonId: <?php echo $this->_tpl_vars['taxonB']['id']; ?>
,
		colour:'<?php if ($this->_tpl_vars['occurrencesA'] && $this->_tpl_vars['occurrencesB']): ?><?php echo $this->_tpl_vars['v']['colour_inverse']; ?>
<?php else: ?><?php echo $this->_tpl_vars['v']['colour']; ?>
<?php endif; ?>',
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
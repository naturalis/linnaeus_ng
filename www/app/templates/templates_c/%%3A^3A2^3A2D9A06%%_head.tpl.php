<?php /* Smarty version 2.6.26, created on 2011-03-31 14:14:21
         compiled from ../shared/_head.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'strpos', '../shared/_head.tpl', 19, false),)), $this); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title><?php echo $this->_tpl_vars['session']['project']['title']; ?>
<?php if ($this->_tpl_vars['pageName']): ?>: <?php echo $this->_tpl_vars['pageName']; ?>
<?php endif; ?></title>
	<style type="text/css" media="all">
<?php if ($this->_tpl_vars['cssToLoad']): ?>
<?php unset($this->_sections['i']);
$this->_sections['i']['name'] = 'i';
$this->_sections['i']['loop'] = is_array($_loop=$this->_tpl_vars['cssToLoad']) ? count($_loop) : max(0, (int)$_loop); unset($_loop);
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
		@import url("<?php echo $this->_tpl_vars['session']['project']['urls']['project_css']; ?>
<?php echo $this->_tpl_vars['cssToLoad'][$this->_sections['i']['index']]; ?>
");
<?php endfor; endif; ?>
<?php endif; ?>
	</style>
	<script type="text/javascript" src="<?php echo $this->_tpl_vars['baseUrl']; ?>
app/javascript/jquery-1.4.2.min.js"></script>
	<script type="text/javascript" src="<?php echo $this->_tpl_vars['baseUrl']; ?>
app/javascript/jquery.tools.min.js"></script>
	<script type="text/javascript" src="<?php echo $this->_tpl_vars['baseUrl']; ?>
admin/javascript/sprintf-0.7-beta1.js"></script>
	
<?php if ($this->_tpl_vars['javascriptsToLoad']): ?>
<?php unset($this->_sections['i']);
$this->_sections['i']['name'] = 'i';
$this->_sections['i']['loop'] = is_array($_loop=$this->_tpl_vars['javascriptsToLoad']['all']) ? count($_loop) : max(0, (int)$_loop); unset($_loop);
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
<?php if (((is_array($_tmp=$this->_tpl_vars['javascriptsToLoad']['all'][$this->_sections['i']['index']])) ? $this->_run_mod_handler('strpos', true, $_tmp, "http:") : strpos($_tmp, "http:")) === false && ((is_array($_tmp=$this->_tpl_vars['javascriptsToLoad']['all'][$this->_sections['i']['index']])) ? $this->_run_mod_handler('strpos', true, $_tmp, "https:") : strpos($_tmp, "https:")) === false): ?>
	<script type="text/javascript" src="<?php echo $this->_tpl_vars['baseUrl']; ?>
app/javascript/<?php echo $this->_tpl_vars['javascriptsToLoad']['all'][$this->_sections['i']['index']]; ?>
"></script>
<?php else: ?>
	<script type="text/javascript" src="<?php echo $this->_tpl_vars['javascriptsToLoad']['all'][$this->_sections['i']['index']]; ?>
"></script>
<?php endif; ?>
<?php endfor; endif; ?>
<?php unset($this->_sections['i']);
$this->_sections['i']['name'] = 'i';
$this->_sections['i']['loop'] = is_array($_loop=$this->_tpl_vars['javascriptsToLoad']['IE']) ? count($_loop) : max(0, (int)$_loop); unset($_loop);
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
	<!--[if IE]><script type="text/javascript" src="<?php echo $this->_tpl_vars['baseUrl']; ?>
app/javascript/<?php echo $this->_tpl_vars['javascriptsToLoad']['IE'][$this->_sections['i']['index']]; ?>
"></script><![endif]-->
<?php endfor; endif; ?>
<?php endif; ?>
</head>
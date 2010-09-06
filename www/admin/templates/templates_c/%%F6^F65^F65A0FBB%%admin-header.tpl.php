<?php /* Smarty version 2.6.26, created on 2010-09-06 18:52:59
         compiled from ../shared/admin-header.tpl */ ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />

	<title><?php echo $this->_tpl_vars['session']['project']['name']; ?>
<?php if ($this->_tpl_vars['session']['project']['name'] != '' && $this->_tpl_vars['pageName'] != ''): ?> - <?php endif; ?><?php echo $this->_tpl_vars['pageName']; ?>
</title>

	<link href="<?php echo $this->_tpl_vars['rootWebUrl']; ?>
admin/images/system/favicon.ico" rel="shortcut icon" type="image/x-icon" />
	<link href="<?php echo $this->_tpl_vars['rootWebUrl']; ?>
admin/images/system/favicon.ico" rel="icon" type="image/x-icon" />

	<style type="text/css" media="all">
		@import url("<?php echo $this->_tpl_vars['rootWebUrl']; ?>
admin/style/main.css");
		@import url("<?php echo $this->_tpl_vars['rootWebUrl']; ?>
admin/style/admin-inputs.css");
		@import url("<?php echo $this->_tpl_vars['rootWebUrl']; ?>
admin/style/admin-help.css");
		@import url("<?php echo $this->_tpl_vars['rootWebUrl']; ?>
admin/style/admin.css");
	</style>

	<script type="text/javascript" src="<?php echo $this->_tpl_vars['rootWebUrl']; ?>
admin/javascript/jquery-1.4.2.min.js"></script>
	<script type="text/javascript" src="<?php echo $this->_tpl_vars['rootWebUrl']; ?>
admin/javascript/main.js"></script>

</head>

<body><div id="body-container">
<div id="header-container">
	<a href="<?php echo $this->_tpl_vars['rootWebUrl']; ?>
admin/admin-index.php"><img src="<?php echo $this->_tpl_vars['rootWebUrl']; ?>
admin/images/system/linnaeus_logo.png" id="lng-logo" />
	<img src="<?php echo $this->_tpl_vars['rootWebUrl']; ?>
admin/images/system/eti_logo.png" id="eti-logo" /></a>
</div>
<div id="page-container">

<div id="page-header-titles">
	<span id="page-header-title"><?php echo $this->_tpl_vars['applicationName']; ?>
 v<?php echo $this->_tpl_vars['applicationVersion']; ?>
</span><br />
<?php if ($this->_tpl_vars['session']['project']['name'] != ''): ?>	<span id="page-header-projectname"><?php echo $this->_tpl_vars['session']['project']['name']; ?>
</span>
<!--DEBUG ONLY:--><span style="color:white"><?php echo $this->_tpl_vars['session']['project']['id']; ?>
</span>
<br /><?php endif; ?>
<?php if ($this->_tpl_vars['controllerPublicName'] != '' && ! $this->_tpl_vars['hideControllerPublicName']): ?>	<span id="page-header-appname"><a href="index.php"><?php echo $this->_tpl_vars['controllerPublicName']; ?>
</a></span><br /><?php endif; ?>
	<span id="page-header-pageaction"><?php echo $this->_tpl_vars['pageName']; ?>
</span>
</div>

<?php if ($this->_tpl_vars['helpTexts']): ?>
<div id="block-inline-help">
	<div id="title" onclick="allToggleHelpVisibility();">Help</div>
	<div class="body-collapsed" id="body-visible">
<?php unset($this->_sections['i']);
$this->_sections['i']['name'] = 'i';
$this->_sections['i']['loop'] = is_array($_loop=$this->_tpl_vars['helpTexts']) ? count($_loop) : max(0, (int)$_loop); unset($_loop);
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
		<div class="subject"><?php echo $this->_tpl_vars['helpTexts'][$this->_sections['i']['index']]['subject']; ?>
</div>
		<div class="text"><?php echo $this->_tpl_vars['helpTexts'][$this->_sections['i']['index']]['helptext']; ?>
</div>
<?php endfor; endif; ?>
	</div>
</div>
<?php endif; ?>
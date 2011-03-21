<?php /* Smarty version 2.6.26, created on 2011-03-20 15:59:39
         compiled from set_project.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('block', 't', 'set_project.tpl', 48, false),)), $this); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

  <title></title>

  <link href="admin/media/system/favicon.ico" rel="shortcut icon" type="image/x-icon" />
  <link href="admin/media/system/favicon.ico" rel="icon" type="image/x-icon" />

  <style type="text/css" media="all">
    @import url("../../../admin/style/main.css");
    @import url("../../../admin/style/admin.css");
  </style>
</head>

<body><div id="body-container">
<div id="header-container">
  <a href="../../../admin/admin-index.php"><img src="../../../admin/media/system/linnaeus_logo.png" id="lng-logo" />
  <img src="../../../admin/media/system/eti_logo.png" id="eti-logo" /></a>


</div>

<div id="page-container">

<div id="page-header-titles"><span id="page-header-title">Linnaeus NG</span><br />
<span id="admin-subtitle"></span></div>
<?php echo '
<style>
#messages,#errors {
	margin-left: 10px;
	margin-top: 10px;
	padding: 5px 5px 5px 5px;
	border: 1px dotted black;
	width: 350px;
	background-color: #eef;
}

#errors {
	background-color: #fee;
}
</style>
'; ?>

<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "../shared/messages.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<div id="page-main">
<p>
<a href="<?php echo $this->_tpl_vars['baseUrl']; ?>
"><?php $this->_tag_stack[] = array('t', array()); $_block_repeat=true;$this->_plugins['block']['t'][0][0]->smartyTranslate($this->_tag_stack[count($this->_tag_stack)-1][1], null, $this, $_block_repeat);while ($_block_repeat) { ob_start(); ?>Back to Linnaeus NG root<?php $_block_content = ob_get_contents(); ob_end_clean(); $_block_repeat=false;echo $this->_plugins['block']['t'][0][0]->smartyTranslate($this->_tag_stack[count($this->_tag_stack)-1][1], $_block_content, $this, $_block_repeat); }  array_pop($this->_tag_stack); ?></a>
</p>
</div>


</div ends="page-container">
<div id="footer-container"></div ends="footer-container">
</div ends="body-container">

</body>
</html>
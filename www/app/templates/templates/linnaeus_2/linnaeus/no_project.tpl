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
  <a href="../../../admin/admin-index.php"><img alt="Linnaeus NG" src="../../../admin/media/system/linnaeus_logo.png" id="lng-logo" />
  <img alt="ETI" src="../../../admin/media/system/eti_logo.png" id="eti-logo" /></a>


</div>

<div id="page-container">

<div id="page-header-titles"><span id="page-header-title">Linnaeus NG</span><br />
<span id="admin-subtitle"></span></div>
{literal}
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
{/literal}
{include file="../shared/messages.tpl"}
<div id="page-main">
{t}Unknown or no project ID.{/t}
<p>
<a href="{$baseUrl}">{t}Back to{/t} {t}Linnaeus NG root{/t}</a>
</p>
</div>


</div ends="page-container">
<div id="footer-container"></div ends="footer-container">
</div ends="body-container">

</body>
</html>
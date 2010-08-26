<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>{$session._current_project_name}{if $session._current_project_name!=''} - {/if}{$pageName}</title>
<style type="text/css" media="all">
  @import url("../../style/main.css");
  @import url("../../style/admin.css");
</style>
<script type="text/javascript" src="../../javascript/main.js"></script>
</head>
<body><div id="admin-body-container">
<div id="admin-header-container">
	<img src="../../images/system/eti-logo.png" id="admin-page-eti-logo" />
</div>
<div id="admin-page-container">

<div id="admin-titles">
	<span id="admin-title">{$applicationName} v{$applicationVersion}</span><br />
{if $session._current_project_name!=''}	<span id="admin-project-title">{$session._current_project_name}</span><br />{/if}
	<span id="admin-subtitle">{$pageName}</span>
</div>

{if $helpTexts}
<div id="inlineHelp">
	<div id="inlineHelp-title" onclick="toggleHelpVisibility();">Help</div>
	<div class="inlineHelp-body-hidden" id="inlineHelp-body">
{section name=i loop=$helpTexts}
		<div class="inlineHelp-subject">{$helpTexts[i].subject}</div>
		<div class="inlineHelp-text">{$helpTexts[i].helptext}</div>
{/section}
	</div>
</div>
{/if}
{include file="shared/_admin-head.tpl"}
{include file="shared/_admin-body-start.tpl"}
<div id="page-main">Welcome to Linnaeus NG.<br />
<br />
To use the administration, follow <a href="admin/views/users/login.php">this link</a>.<br />
To use an application, follow one of the following links:<br />
<ul>
{foreach from=$projects key=k item=v}
<li><span class="pseudo-a" onclick="$('#p').val('{$v.id}');$('#theForm').submit();">{$v.title}</span></li>
{/foreach}
</ul>
(if a project appears to be missing here, its "published" status is most likely set to "no")
<br />
</div>
<form method="post" id="theForm" action="app/views/linnaeus/set_project.php">
<input type="hidden" name="p" id="p" value="" />
<input type="hidden" name="rnd" value="<?php echo uniqid(null,true); ?>" />
</form>

</div ends="page-container">
<div id="footer-container"></div ends="footer-container">
</div ends="body-container">

</body>
</html>

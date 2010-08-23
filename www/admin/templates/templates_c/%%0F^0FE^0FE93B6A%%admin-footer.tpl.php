<?php /* Smarty version 2.6.26, created on 2010-08-23 15:20:13
         compiled from ../shared/admin-footer.tpl */ ?>
<?php echo '
<style>
#debug {
	white-space:pre;
	font-size:10px;
	margin-top:15px;
	color:#666;
	font-family:Arial, Helvetica, sans-serif;
}
</style>
<hr style="border-top:1px dotted #ddd;" />
<span onclick="var a=document.getElementById(\'debug\'); if(a.style.visibility==\'visible\') {a.style.visibility=\'collapse\';} else {a.style.visibility=\'visible\';} " style="cursor:pointer">&nbsp;&Delta;&nbsp;</span>
'; ?>

<div id="debug" style="visibility:collapse;">
<?php 
var_dump($_SESSION);
 ?>
</div>
</body>
</html>
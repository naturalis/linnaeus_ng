<?php /* Smarty version 2.6.26, created on 2011-04-13 15:05:40
         compiled from C:/Users/maarten/htdocs/linnaeus+ng/linnaeus_ng/www/app/templates/templates/shared/0064/_footer.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'addslashes', 'C:/Users/maarten/htdocs/linnaeus ng/linnaeus_ng/www/app/templates/templates/shared/0064/_footer.tpl', 45, false),)), $this); ?>
</div ends="page-container">
<div id="footer-container">

					<table border="0" cellspacing="0" cellpadding="0" width="940">
						<tr>

							<td valign="top">
								<p class="footerlinks">
								<a style="color:inherit" href="/tanbif/contentpage.php?cat=about-tanbif&pag=contact">Contact TanBIF</a> | <a style="color:inherit" href="/tanbif/contentpage.php?cat=about-tanbif&pag=help-desk">Help desk</a> | <a style="color:inherit" href="/tanbif/contentpage.php?cat=about-tanbif&pag=disclaimer">Disclaimer</a> | <a style="color:inherit" href="/tanbif/contentpage.php?cat=about-tanbif&pag=credits">Credits</a>
								</p>

							</td>
							<td valign="top" align="right">
								<p class="footerlinks">Site developed by <a href="http://www.eti.uva.nl" target="_blank">ETI BioInformatics</a> with the BioPortal&trade; Toolkit
								</p>
							</td>
						</tr>
					</table>



</div ends="footer-container">
</div ends="body-container">
<div id="hint-balloon" onmouseout="glossTextOut()" 
	style="
	background-color:#FFFF99;
	border:1px solid #bbbb00;
	width:225px;height:100px;
	padding:3px;
	font-size:9px;
	display:none;
	overflow:hidden;
	cursor:pointer;
	position:absolute;
	top:0px;
	left:0px;
	">
</div>
</form>
<?php echo '
<script type="text/JavaScript">
$(document).ready(function(){
'; ?>

<?php $_from = $this->_tpl_vars['requestData']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['k'] => $this->_tpl_vars['v']):
?>
addRequestVar('<?php echo $this->_tpl_vars['k']; ?>
','<?php echo ((is_array($_tmp=$this->_tpl_vars['v'])) ? $this->_run_mod_handler('addslashes', true, $_tmp) : addslashes($_tmp)); ?>
')
<?php endforeach; endif; unset($_from); ?>

})
<?php echo '
</script>
'; ?>

</body>
</html>
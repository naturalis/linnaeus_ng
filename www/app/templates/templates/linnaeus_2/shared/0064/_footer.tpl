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
{literal}
<script type="text/JavaScript">
$(document).ready(function(){
{/literal}
{foreach from=$requestData key=k item=v}
addRequestVar('{$k}','{$v|addslashes}')
{/foreach}

})
{literal}
</script>
{/literal}
</body>
</html>
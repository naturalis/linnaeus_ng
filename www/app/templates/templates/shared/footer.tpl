{if $showBackToSearch && $session.user.search.hasSearchResults}
<div id="back-to-search">
<span id="back-link" onclick="window.open('../linnaeus/redosearch.php','_self')">{t}back to search results{/t}</span>
</div>
{elseif $backlink.url=='not implemented'}
<p>
<span class="a" onclick="doBackForm('{$backlink.url}','{$backlink.data|@escape}');" title="Back to {$backlink.name}">BACK</span>
</p>
{/if}
</div ends="page-container">
<div id="footer-container">
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
	$('#body-container').height($(document).height());
	{if $search}onSearchBoxSelect('{$search|@addslashes}');{/if}

{foreach from=$requestData key=k item=v}
addRequestVar('{$k}','{$v|addslashes}')
{/foreach}

})
{literal}
</script>
{/literal}
</body>
</html>
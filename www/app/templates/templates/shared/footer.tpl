{if $showBackToSearch && $session.user.search.hasSearchResults}
<div id="back-to-search">
<span id="link" onclick="window.open('../linnaeus/redosearch.php','_self')">{t}back to search results{/t}</span>
</div>
{/if}
</div ends="page-container">
<div id="footer-container">
</div ends="footer-container">
</div ends="body-container">
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
{literal}
});
</script>
{/literal}
</body>
</html>



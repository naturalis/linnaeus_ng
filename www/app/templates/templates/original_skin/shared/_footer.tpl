<div id="allLookupList" class="allLookupListInvisible"></div>
{if $showBackToSearch && $session.app.user.search.hasSearchResults}
<div id="back-to-search">
<span id="back-link" onclick="window.open('../search/redosearch.php','_self')">{t}back to search results{/t}</span>
</div>

{if $backlink}
    {if $session.app.user.map.search.taxa}
        {assign var=backUrl value='l2_search.php?action=research'}
    {elseif $session.app.user.map.index}
        {assign var=backUrl value='l2_diversity.php?action=reindex'}
    {else}
        {assign var=backUrl value='$backlink.url'}
    {/if}
    <a class="navigation-icon" id="back-icon" href="{$backUrl}" 
    title="{t}Back{/t}{if $session.app.user.map.search.taxa}{t} to Search results{/t}{else if $session.app.user.map.index}{t} to Diversity index{/t}{/if}">
    {t}Back{/t}</a>
{else}
    <span class="navigation-icon" id="back-icon-inactive">{t}Back{/t}</span>
{/if}

{/if}
</div ends="page-container">
<div id="footer-container">
</div ends="footer-container">
</div ends="body-container">
<div id="hint-balloon" 
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
	if(jQuery().prettyPhoto) {
	 	$("a[rel^='prettyPhoto']").prettyPhoto({
	 		opacity: 0.70, 
			show_title: false,
	 		overlay_gallery: false,
	 		social_tools: false
	 	});
	}
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
<!--[if IE 7]>
<script type="text/javascript">
$('#dialog-close').html('X');
</script>
<![endif]-->
</body>
</html>
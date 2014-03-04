{*
	usage:

	<span id="resultcount-header">{aantal}</span>

	{assign var=pgnResultCount value=$results.count}
	{assign var=pgnResultsPerPage value=$results.perpage}
	{assign var=pgnCurrPage value=$search.page}
	{assign var=pgnURL value=$smarty.server.PHP_SELF}
	{assign var=pgnQuerystring value=$querystring}
	{include file="_paginator.tpl"}

*}
{if $pgnResultCount > $pgnResultsPerPage}
{math equation="ceil(x/y)" assign=pages x=$pgnResultCount y=$pgnResultsPerPage}
{assign var=buffer value=3}
{if !$pgnCurrPage}{assign var=currPage value=1}{else}{assign var=currPage value=$pgnCurrPage}{/if}
<div id="paginator">
	<ul>
		<li class="no-border">{t}pagina:{/t}</li>
		{if $pgnCurrPage>1}
		<li><a href="{$pgnURL}?{$pgnQuerystring}page={$currPage-1}"><<</a></li>
		{/if}
		{for $foo=1 to $pages}
		{if !($currPage<=(2*$buffer) && ($foo<=(2*$buffer))) && $foo==$currPage-$buffer}
		<li><span class="cell">...</span></li>
		{/if}
		{if 
			$foo==1 || 
			($currPage<=(2*$buffer) && ($foo<=(2*$buffer))) ||
			($foo>=$currPage-$buffer && $foo<=$currPage+3) ||
			($currPage>=$pages-(2*$buffer) && ($foo>=$pages-(2*$buffer))) ||
			$foo==$pages
		}
		<li>{if $foo==$currPage}<span class="cell">{$foo}</span>{else}<a href="{$pgnURL}?{$pgnQuerystring}page={$foo}">{$foo}</a>{/if}</li>
		{/if}
		{if !($currPage>=$pages-(2*$buffer) && ($foo>=$pages-(2*$buffer))) && $foo==$currPage+(2*$buffer)}
		<li><span class="cell">...</span></li>
		{/if}
		{/for}
		{if $pgnCurrPage<$pages}
		<li class="no-border"><a href="{$pgnURL}?{$pgnQuerystring}page={$currPage+1}">>></a></li>
		{/if}
	</ul>
</div>
{literal}
<script type="text/JavaScript">
$(document).ready(function(){
{/literal}
	{math assign=temp equation="(x*y)" x=$currPage y=$pgnResultsPerPage}
	$('#resultcount-header').html(
		{math equation="((x-1)*y)+1" x=$currPage y=$pgnResultsPerPage}+
		' - '+
		{if $temp>$pgnResultCount}{$pgnResultCount}{else}{$temp}{/if}+
		' van '+
		{$pgnResultCount}+
		({$pgnResultCount}==1 ? ' resultaat' : ' resultaten')
	);
{literal}
});
</script>
{/literal}
{else}
{literal}
<script type="text/JavaScript">
$(document).ready(function(){
{/literal}
	$('#resultcount-header').html({$pgnResultCount}+({$pgnResultCount}==1 ? ' resultaat' : ' resultaten'));
{literal}
});
</script>
{/literal}

{/if}

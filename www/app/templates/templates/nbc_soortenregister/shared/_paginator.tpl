{*
	usage:

	<span id="resultcount-header">{aantal}</span>


	{assign var=pgnEntityNames value=['resultaat','resultaten']}
	{assign var=pgnResultCount value=$results.count}
	{assign var=pgnResultsPerPage value=$results.perpage}
	{assign var=pgnCurrPage value=$search.page}
	{assign var=pgnURL value=$smarty.server.PHP_SELF}
	{assign var=pgnQuerystring value=$querystring}
	{include file="_paginator.tpl"}

*}

{if !pgnEntityNames}
{assign var=pgnEntityNames value=['resultaat','resultaten']}
{/if}

{if $pgnResultCount > $pgnResultsPerPage}
{math equation="ceil(x/y)" assign=pages x=$pgnResultCount y=$pgnResultsPerPage}
{assign var=buffer value=3}
{if !$pgnCurrPage}{assign var=currPage value=1}{else}{assign var=currPage value=$pgnCurrPage}{/if}
<div id="paginator">
	<input type="hidden" id="results-per-page" value="{$results.perpage}" />
	<ul>
		<li class="no-border">{t}pagina:{/t}</li>
		{if $pgnCurrPage>1}
		<li><a id="paginator-prev-link" href="{$pgnURL}?{$pgnQuerystring}page={$currPage-1}"><<</a></li>
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
		<li class="no-border"><a id="paginator-next-link" href="{$pgnURL}?{$pgnQuerystring}page={$currPage+1}">>></a></li>
		{/if}
	</ul>
</div>

<script type="text/JavaScript">
$(document).ready(function()
{
	{math assign=temp equation="(x*y)" x=$currPage y=$pgnResultsPerPage}
	$('#resultcount-header').html({math equation="((x-1)*y)+1" x=$currPage y=$pgnResultsPerPage}+' - '+ {if $temp>$pgnResultCount}{$pgnResultCount}{else}{$temp}{/if});
});
</script>
{else}
<script type="text/JavaScript">
$(document).ready(function()
{
	$('#resultcount-header').html({$pgnResultCount});
});
</script>
{/if}
<script type="text/JavaScript">
$(document).ready(function()
{
	$('#resultcount-header').html($('#resultcount-header').html()+' van '+{$pgnResultCount}+({$pgnResultCount}==1 ? ' {$pgnEntityNames[0]}' : ' {$pgnEntityNames[1]}'));
});
</script>



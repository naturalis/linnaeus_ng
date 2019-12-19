{include file="../shared/admin-header.tpl"}

<div id="page-main">
	<div id="results">
	Done.
	<p>
		{$replacementResultCounters.replaced} match(es) replaced.<br />
		{$replacementResultCounters.skipped} match(es) skipped.<br />
		{$replacementResultCounters.mismatched} unchanged (mismatch: changes made between search and replace?).
	</p>
	<input type="button" value="{t}back{/t}" onclick="window.open('index.php','_self')" />
	</div>
</div>

{include file="../shared/admin-footer.tpl"}

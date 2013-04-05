{include file="../shared/admin-header.tpl"}
{include file="../shared/admin-messages.tpl"}

<div id="page-main">
<p>
Below is a sample of the data parsed from the input file. Please verify that it looks okay, and click 'save'.
</p>
<p>
	Project title: "<b>{$title}</b>"{if !$exists} <span class="message-error">(does not exist in database! no use importing!)</span>{/if}<br />
    Soortgroep: {$soortgroep}<br />
</p>

<p>
<b>Labels & image names:</b><br />
<table>
<tr><th>Group</th><th>Character</th><th>State</th><th>(Translation)</th><th>(Image)</th></tr>
{foreach from=$states item=v}
<tr><td>{$v[0]}</td><td>{$v[1]}</td><td>{$v[2]}</td><td>{$v[3]}</td><td>{$v[4]}</td></tr>
{/foreach}
</table>
</p>

<p>
	<form method="post" action="nbc_labels_3.php">
	<input type="hidden" name="rnd" value="{$rnd}" />
	<input type="submit" value="Save">
	</form>
</p>
<p>
	<a href="nbc_labels_1.php">Back</a>
</p>

</div>

{include file="../shared/admin-footer.tpl"}
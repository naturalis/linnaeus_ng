{include file="../shared/admin-header.tpl"}

<div id="page-main">


<form method=post>
<input type="hidden" name="id" value="{$reference.id}">
<input type="hidden" name="action" value="save">
<input type="hidden" name="rnd" value="{$rnd}">

<table>
	<tr><td>Language:</td><td>{$reference.language_id}</td></tr>
	<tr><td>Label:</td><td><input type="text" name="xxx" value="{$reference.label}" /></td></tr>
	<tr><td>Date:</td><td><input type="text" name="xxx" value="{$reference.date}" /></td></tr>
	<tr><td>Author (literal):</td><td><input type="text" name="xxx" value="{$reference.author}" /></td></tr>
	<tr><td>Author:</td><td>{$reference.actor_id}</td></tr>
	<tr><td>Publication type:</td><td><input type="text" name="xxx" value="{$reference.publication_type}" /></td></tr>
	<tr><td>Citation:</td><td><input type="text" name="xxx" value="{$reference.citation}" /></td></tr>
	<tr><td>Source:</td><td><input type="text" name="xxx" value="{$reference.source}" /></td></tr>
	<tr><td>Published in (literal):</td><td><input type="text" name="xxx" value="{$reference.publishedin}" /></td></tr>
	<tr><td>Published in:</td><td>{$reference.publishedin_id}</td></tr>
	<tr><td>Pages:</td><td><input type="text" name="xxx" value="{$reference.pages}" /></td></tr>
	<tr><td>Volume:</td><td><input type="text" name="xxx" value="{$reference.volume}" /></td></tr>
	<tr><td>Periodical (literal):</td><td><input type="text" name="xxx" value="{$reference.periodical}" /></td></tr>
	<tr><td>Periodical:</td><td>{$reference.periodical_id}</td></tr>
	<tr><td>Order number:</td><td><input type="text" name="xxx" value="{$reference.order_number}" /></td></tr>
	<tr><td>External link:</td><td><input type="text" name="xxx" value="{$reference.external_link}" /></td></tr>
</table>
<input type="submit" value="save" />
</form>


</div>

{include file="../shared/admin-footer.tpl"}

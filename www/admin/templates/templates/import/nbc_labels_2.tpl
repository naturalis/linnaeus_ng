{include file="../shared/admin-header.tpl"}
{include file="../shared/admin-messages.tpl"}

<div id="page-main">
<form method="post" action="nbc_labels_3.php">
<p>
Below is the data parsed from the input file. Please verify that it looks okay, and click 'save'.
</p>
<p>
	Project title: "<b>{$title}</b>"{if !$exists} <span class="message-error">(does not exist in database! no use importing!)</span>{/if}<br />
    Soortgroep: {$soortgroep}<br />
    Matrix: {$matrix}<br />
</p>

<p>
    <b>Labels & image names:</b><br />
    <table>
    <tr><th>Group</th><th>Character</th><th>State</th><th>(Translation)</th><th>(Image)</th></tr>
    {foreach from=$states item=v}
    <tr class="tr-highlight"><td>{$v[0]}</td><td>{$v[1]}</td><td>{$v[2]}</td><td>{$v[3]}</td><td>{$v[4]}</td></tr>
    {/foreach}
    </table>
</p>
<p>
	Re-evaluate the character types (text, media):<br />
    <label><input type="radio" name="re_type_chars" value="all"  />set to 'media' when all states have an image, else to 'text'</label><br />
    <label><input type="radio" name="re_type_chars" value="partial" checked="checked" />set to 'media' when at least one state has an image, else to 'text'</label><br />
    <label><input type="radio" name="re_type_chars" value="none" />do not re-evaluate</label><br />
</p>
<p>
	<input type="hidden" name="rnd" value="{$rnd}" />
	<input type="submit" value="Save">
</p>
<p>
	<a href="nbc_labels_1.php">Back</a>
</p>
	</form>
</div>

{include file="../shared/admin-footer.tpl"}
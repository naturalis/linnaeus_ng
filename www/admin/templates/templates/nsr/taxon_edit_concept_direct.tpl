{include file="../shared/admin-header.tpl"}

<div id="page-main">

<h2><span style="font-size:12px;font-style:normal">concept:</span> {$concept.taxon}</h2>

<p>
    <h3>Let op!</h3>
    Via dit scherm kan de conceptnaam direct worden aangepast, zonder checks. 
    Doet dit alleen in uitzonderingsgevallen waarin er een discrepantie bestaat tussen de conceptnaam en de geldige naam.
    De correcte manier om de naam van een concept aan te passen is via <a href="synonym.php?id={$validname.id}">de geldige naam</a>.
</p>

<form method="post">
<input type="hidden" id="rnd" name="rnd" value="{$rnd}" />
<input type="hidden" name="action" value="save" />
<input type="hidden" name="id" value="{$concept.id}" />

<p>
    naam: <input type="text" id="taxon" name="taxon" value="{$concept.taxon}"  /><br />
</p>

<p>
    <input type="submit" value="opslaan"  />
</p>

</form>

    <p>
        <a href="taxon.php?id={$concept.id}">terug</a>
    </p>

</div>

{include file="../shared/admin-messages.tpl"}

<script>
$(document).ready(function()
{
	$('#page-block-messages').fadeOut(3000);
});
</script>

{include file="../shared/admin-footer.tpl"}
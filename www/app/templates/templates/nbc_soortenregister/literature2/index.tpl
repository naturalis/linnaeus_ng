{include file="../shared/header.tpl"}

<div id="dialogRidge">

	<div id="left">
        <div>
            <h2>&nbsp;</h2>
        </div>
	</div>
	
	<div id="content" class="literature">

		<h2 style="margin-top: 17px; margin-bottom: 6px">{t}Literatuur{/t}</h2>
		{t}Referentie niet gevonden.{/t}
		</div>

	</div>

	{include file="../shared/_right_column.tpl"}

</div>

{include file="../shared/footer.tpl"}

<script type="text/JavaScript">
$(document).ready(function()
{
	$('title').html('Literatuur - '+$('title').html());
});
</script>
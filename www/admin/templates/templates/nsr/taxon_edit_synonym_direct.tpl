{include file="../shared/admin-header.tpl"}

<script>

function doSynDirectForm()
{
	form = $("<form method=post></form>");
	form.append('<input type="hidden" name="action" value="save" />');
	
	$( 'input' ).each(function()
	{
		var id=$(this).attr('id')
		var val=$(this).val();

		if (id)
		{
			if (id=='id' || id=='rnd')
			{
				form.append('<input type="hidden" name="'+id+'" value="'+val+'" />');
			}
			else
			if (val)
			{
				form.append('<input type="hidden" name="'+id+'[new]" value="'+val+'" />');
			}
			else
			{
				form.append('<input type="hidden" name="'+id+'[delete]" value="1" />');
			}
		}
	});

	$(window).unbind('beforeunload');
	$('body').append(form);
	form.submit();
}

</script>

<div id="page-main">

<h2><span style="font-size:12px">naamkaart:</span> {$name.name}</h2>
<h3><span style="font-size:12px;font-style:normal">concept:</span> {$concept.taxon}</h3>

<p>
    <h3>Let op!</h3>
    Via dit scherm kunnen alle delen van de geldige naam direct worden aangepast, zonder checks. 
    Doet dit alleen in uitzonderingsgevallen waarin er een discrepantie bestaat tussen de conceptnaam en de geldige naam.<br />
    Let op dat alle delen los moeten worden ingevoerd, er wordt niets automatisch samengevoegd!
</p>

<form method="post" onsubmit="return false;">

    <input type="hidden" id="rnd" name="rnd" value="{$rnd}" />
    <input type="hidden" id="id" value="{$name.id}" />

    <p>
        <table>
            <tr>
                <th>samengestelde naam:</th>
                <td><input type="text" class="medium" id="name_name" value="{$name.name}" /></td>
            </tr>
            <tr>
                <th>genus / uninomial:</th>
                <td><input type="text" class="medium" id="name_uninomial" value="{$name.uninomial}" /></td>
            </tr>
            <tr>
                <th>soort:</th>
                <td><input type="text" class="medium" id="name_specific_epithet" value="{$name.specific_epithet}" /></td>
            </tr>
            <tr>
                <th>derde naamdeel:</th>
                <td><input type="text" class="medium" id="name_infra_specific_epithet" value="{$name.infra_specific_epithet}" /></td>
            </tr>
        
            <tr>
                <th>auteurschap:</th>
                <td><input type="text" class="medium" id="name_authorship" value="{$name.authorship}" /></td>
            </tr>
            <tr>
                <th>auteur(s):</th>
                <td><input type="text" class="medium" id="name_name_author" value="{$name.name_author}" /></td>
            </tr>	
            <tr>
                <th>jaar:</th>
                <td><input type="text" class="small" id="name_authorship_year" value="{$name.authorship_year}" /></td>
            </tr>	
        
        </table>
    </p>
    
    

    
    <p>
        <input type="button" value="opslaan" onclick="doSynDirectForm()"/>
    </p>

    </form>

    <p>
        <a href="synonym.php?id={$name.id}">terug</a>
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
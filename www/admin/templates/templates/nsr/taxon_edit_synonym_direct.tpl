{include file="../shared/admin-header.tpl"}

{include file="../shared/left_column_tree.tpl"}


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

<h2><span style="font-size:12px">{t}naamkaart:{/t}</span> {$name.name}</h2>
<h3><span style="font-size:12px;font-style:normal">{t}concept:{/t}</span> {$concept.taxon}</h3>

<p>
    <h3>{t}Let op!{/t}</h3>
    {t}Via dit scherm kunnen alle delen van de geldige naam direct worden aangepast, zonder checks. 
    Doet dit alleen in uitzonderingsgevallen waarin er een discrepantie bestaat tussen de conceptnaam en de geldige naam.<br />
    Let op dat alle delen los moeten worden ingevoerd, er wordt niets automatisch samengevoegd!{/t}
</p>

<form method="post" onsubmit="return false;">

    <input type="hidden" id="rnd" name="rnd" value="{$rnd}" />
    <input type="hidden" id="id" value="{$name.id}" />

    <p>
        <table>
            <tr>
                <th>{t}samengestelde naam:{/t}</th>
                <td><input type="text" class="medium" id="name_name" value="{$name.name}" /></td>
            </tr>
            <tr>
                <th>{t}genus / uninomial:{/t}</th>
                <td><input type="text" class="medium" id="name_uninomial" value="{$name.uninomial}" /></td>
            </tr>
            <tr>
                <th>{t}soort:{/t}</th>
                <td><input type="text" class="medium" id="name_specific_epithet" value="{$name.specific_epithet}" /></td>
            </tr>
            <tr>
                <th>{t}derde naamdeel:{/t}</th>
                <td><input type="text" class="medium" id="name_infra_specific_epithet" value="{$name.infra_specific_epithet}" /></td>
            </tr>
        
            <tr>
                <th>{t}auteurschap:{/t}</th>
                <td><input type="text" class="medium" id="name_authorship" value="{$name.authorship}" /></td>
            </tr>
            <tr>
                <th>{t}auteur(s):{/t}</th>
                <td><input type="text" class="medium" id="name_name_author" value="{$name.name_author}" /></td>
            </tr>	
            <tr>
                <th>{t}jaar:{/t}</th>
                <td><input type="text" class="small" id="name_authorship_year" value="{$name.authorship_year}" /></td>
            </tr>	
        
        </table>
    </p>
    
    

    
    <p>
        <input type="button" value="{t}opslaan{/t}" onclick="doSynDirectForm()"/>
    </p>

    </form>

    <p>
        <a href="synonym.php?id={$name.id}">{t}terug{/t}</a>
    </p>

</div>

{include file="../shared/admin-messages.tpl"}
{include file="../shared/admin-footer.tpl"}
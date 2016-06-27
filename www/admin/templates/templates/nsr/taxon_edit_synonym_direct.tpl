{include file="../shared/admin-header.tpl"}

{include file="../shared/left_column_tree.tpl"}
{include file="../shared/left_column_admin_menu.tpl"}


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

<div id="page-container-div">

<div id="page-main">

<h2><span style="font-size:12px">{t}name card{/t}:</span> {$name.name}</h2>
<h3><span style="font-size:12px;font-style:normal">{t}concept{/t}:</span> {$concept.taxon}</h3>

<p>
    <h3>{t}Let op!{/t}</h3>
    {t}This screen allows you to change the concept name directly, circumventing any checks. Use it only in exceptional cases, when there's a discrepancy between the concept name and the synonym.{/t}
    <br />
    {t}Note that all elements should be entered separately, nothing will be concatenated automatically!{/t}
</p>

<form method="post" onsubmit="return false;">

    <input type="hidden" id="rnd" name="rnd" value="{$rnd}" />
    <input type="hidden" id="id" value="{$name.id}" />

    <p>
        <table>
            <tr>
                <th>{t}concatenated name{/t}:</th>
                <td><input type="text" class="medium" id="name_name" value="{$name.name}" /></td>
            </tr>
            <tr>
                <th>{t}genus/uninomial{/t}:</th>
                <td><input type="text" class="medium" id="name_uninomial" value="{$name.uninomial}" /></td>
            </tr>
            <tr>
                <th>{t}species{/t}:</th>
                <td><input type="text" class="medium" id="name_specific_epithet" value="{$name.specific_epithet}" /></td>
            </tr>
            <tr>
                <th>{t}third name element{/t}:</th>
                <td><input type="text" class="medium" id="name_infra_specific_epithet" value="{$name.infra_specific_epithet}" /></td>
            </tr>

            <tr>
                <th>{t}authorship{/t}:</th>
                <td><input type="text" class="medium" id="name_authorship" value="{$name.authorship}" /></td>
            </tr>
            <tr>
                <th>{t}author(s){/t}:</th>
                <td><input type="text" class="medium" id="name_name_author" value="{$name.name_author}" /></td>
            </tr>
            <tr>
                <th>{t}year{/t}:</th>
                <td><input type="text" class="small" id="name_authorship_year" value="{$name.authorship_year}" /></td>
            </tr>

        </table>
    </p>




    <p>
        <input type="button" value="{t}save{/t}" onclick="doSynDirectForm()"/>
    </p>

    </form>

    <p>
        <a href="synonym.php?id={$name.id}">{t}back{/t}</a>
    </p>

</div>

{include file="../shared/admin-messages.tpl"}

</div>

{include file="../shared/admin-footer.tpl"}
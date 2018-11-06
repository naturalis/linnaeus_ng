{include file="../shared/admin-header.tpl"}

<div id="page-main">

	<h3>
        {t}rename trait value{/t} ({$group.sysname}: {$trait.sysname})
    </h3>

    <form id="theForm" method="post">
    <input type="hidden" name="rnd" value="{$rnd}" />
    <input type="hidden" name="action" id="action" value="save">

    <table>
        <tr>
            <th>{t}system name:{/t}</th>
            <td>
            <input class="required" type="text" value="{$trait_value.string_value}" name="sysname" id="sysname" maxlength="4000"> *
			</td>
		</tr>
    </table>
    
    </p>
    <p>
	    <input type="submit" value="save" />
    </p>
    </form>
    <p>
    	<a href="traitgroup_trait_values.php?trait={$trait.id}">back</a>&nbsp;&nbsp;
    	<a href="index.php">index</a><br />
    </p>
</div>



<script>
$("#theForm").submit(function(){
    if ($.trim($("input.required").val()).length == 0){
        alert({t}"System name field is mandatory!"{/t});
        return false;
    }
});
</script>

{include file="../shared/admin-messages.tpl"}
{include file="../shared/admin-footer.tpl"}
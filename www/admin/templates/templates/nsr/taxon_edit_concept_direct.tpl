{include file="../shared/admin-header.tpl"}

{include file="../shared/left_column_tree.tpl"}
{include file="../shared/left_column_admin_menu.tpl"}

<div id="page-container-div">

<div id="page-main">

<h2><span style="font-size:12px;font-style:normal">{t}concept{/t}:</span> {$concept.taxon}</h2>

<p>
    <h3>{t}Note{/t}!</h3>
    {t}This screen allows you to change the concept name directly, circumventing any checks. Use it only in exceptional cases, when there's a discrepancy between the concept name and the valid name. The correct way to change the concept name is through{/t} <a href="synonym.php?id={$validname.id}">{t}the valid name{/t}</a>.
</p>

<form method="post">
<input type="hidden" id="rnd" name="rnd" value="{$rnd}" />
<input type="hidden" name="action" value="save" />
<input type="hidden" name="id" value="{$concept.id}" />

<p>
    {t}name{/t}: <input type="text" id="taxon" name="taxon" value="{$concept.taxon_no_infix}"  /><br />
</p>

<p>
    <input type="submit" value="{t}save{/t}"  />
</p>

</form>

    <p>
        <a href="taxon.php?id={$concept.id}">{t}back{/t}</a>
    </p>

</div>

{include file="../shared/admin-messages.tpl"}

</div>

{include file="../shared/admin-footer.tpl"}
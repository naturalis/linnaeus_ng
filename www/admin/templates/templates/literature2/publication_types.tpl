{include file="../shared/admin-header.tpl"}

<style>
.type-labels {
    font-size: 0.9em;
}
.type-labels input {
    font-size: 0.9em;
    margin: 0 10px 0 2px;
    width: 125px;
}
li {
    margin-left: 15px;
	list-style-position:outside;
}
</style>

<div id="page-main">

<p>
<h3>{t}Publication types{/t}</h3>
</p>

<p>

    <form method="post">
    <input type="hidden" id="action" name="action" value="save_translations">

	<ul>
	{foreach $publicationTypes v}
    	<li>
        	{$v.sys_label}{if $v.total==0}
            <a class='edit' href='#' onclick="$('#id').val({$v.id});$('#action2').val('delete');$('#theForm').submit();return false;">delete</a>
            {else}<a class='edit' href='index_by_type.php?id={$v.id}'>({$v.total})</a>
            {/if}
            <br />
            {foreach $languages l}
            <span class="type-labels">
            {$l.language}:
            <input class="type-labels" type="text" name="translations[{$v.id}][{$l.language_id}]" value="{$v.translations[{$l.language_id}].label}" onkeyup="">
            </span>
            {/foreach }
        </li>
    {/foreach}
    </ul>

	{if $publicationTypes|@count==0}
    <p>
    ({t}no publication types defined{/t})
    </p>
    {else}
    <input type="submit" value="{t}save translations{/t}" />
    {/if}
    </form>



	<p>
    <form method="post" id="theForm">
    <input type="hidden" id="action2" name="action" value="save">
    <input type="hidden" id="id" name="id" value="">
    {t}New{/t}: <input type="text" name="type" />
    <input type="submit" value="{t}save{/t}" />
    </form>
    </p>


</p>
<p>
	<a href="index.php">{t}back{/t}</a>
</p>
</div>

{include file="../shared/admin-messages.tpl"}
{include file="../shared/admin-footer.tpl"}

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
<h3>Publicatievormen</h3>
</p>

<p>

    <form method="post">
    <input type="hidden" id="action" name="action" value="save_translations">

	<ul>
	{foreach $publicationTypes v}
    	<li>
        	{$v.sys_label} ({$v.total})
            {if $v.total==0}
            <a class='edit' href='#' onclick="$('#id').val({$v.id});$('#action2').val('delete');$('#theForm').submit();return false;">delete</a>
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
    (geen publicatievormen gedefinieerd)
    </p>
    {else}
    <input type="submit" value="vertalingen opslaan" />
    {/if}
    </form>
        


	<p>    
    <form method="post" id="theForm">
    <input type="hidden" id="action2" name="action" value="save">
    <input type="hidden" id="id" name="id" value="">
    Nieuw: <input type="text" name="type" />
    <input type="submit" value="opslaan" />
    </form>
    </p>
    
    
</p>
<p>
	<a href="bulk_upload.php">back</a>
</p>
</div>

<script>
$(document).ready(function()
{
	$('#page-block-messages').fadeOut(3000);
});
</script>

{include file="../shared/admin-messages.tpl"}
{include file="../shared/admin-footer.tpl"}

{include file="../shared/admin-header.tpl"}

<div id="page-main">
	{if $groups|@count==0}
    No traitgroups have been defined.
    {else}
	<form method="post" action="" id="theForm" enctype="multipart/form-data">
    <p>
        Traitgroup:
        <select name="traitgroup">
        {foreach $groups as $entry}
            <option value="{$entry.id}">{$entry.sysname}</option>
        {/foreach}
        </select>
    </p>

    <p>
        {if $s.file}
            <b>CSV-file "{$s.file.name}" loaded.</b>
            (<span class="a" onclick="$('#clear').val('file');$('#theForm').submit()">load another file</span>)
        {else}
            CSV-file to load: <input name="importFile" type="file" />&nbsp;*<br />
        {/if}
    </p>
    <p>   
    	<input type="submit" value="upload and parse" />
    </p>
	</form>
    {/if}
</div>

{include file="../shared/admin-messages.tpl"}
{include file="../shared/admin-footer.tpl"}
{include file="../shared/admin-header.tpl"}

<div id="page-main">
	<form method="post" action="" id="theForm" enctype="multipart/form-data">
    <p>
        Yes!
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
    <input type="hidden" name="clear" id="clear" value="" />
    <input type="hidden" name="process" id="process" value="0" />
    {if $s.file}
    <p>
        Press the button to parse the data in your file.
    </p>
    <input type="button" onclick="$('#process').val('1');$('#theForm').submit()" value="Parse file" />
    {else}
    <input type="submit" value="Upload" />
    {/if}
    </p>
	</form>
</div>

{include file="../shared/admin-messages.tpl"}
{include file="../shared/admin-footer.tpl"}
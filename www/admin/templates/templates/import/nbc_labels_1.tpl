{include file="../shared/admin-header.tpl"}

<div id="page-main">
	<form method="post" action="" id="theForm" enctype="multipart/form-data">
    <p>
        This function allows you to specify translations and image names for the various states of a NBC-style matrix key.
        It matches the file you upload to the relevant matrix by lokking up the project name specified in cell A1.
        <a href="{$baseUrl}admin/media/system/sample-nbc-labels.csv">{t}Download a sample CSV-file{/t}</a>.
	</p>
    <p>
        To create an import file, save the relevant sheet of your Excel-file as CSV-file. Make sure the program treats 
        all columns as text, and does not convert text values to something else (like for instance interpreting ranges as dates, 
        turning a value of "10-20" into "October 20th").
	</p>
    <p>        
        MS Excel does not have the option to explicitly save the CSV-file as UTF-8 encoded. Doing so may not be necessary,
        however if your data contains non-ASCII characters (like &euml; of &iuml;), it is advisable to explicitly convert the file. 
        Do so by opening the saved CSV-file in Notepad or another text-editor, and save it while explicitly specifying UTF-8 
        as encoding.<br />
	</p>
    <p>
        Next, click 'browse', select your file and click 'upload'.
    </p>
    <fieldset>
        <legend>Filename</legend>
        <p>
            {if $s.file}
                <b>CSV-file "{$s.file.name}" loaded.</b>
                (<span class="a" onclick="$('#clear').val('file');$('#theForm').submit()">load another file</span>)
            {else}
                CSV-file to load: <input name="importFile" type="file" />&nbsp;*<br />
            {/if}
        </p>
    </fieldset>

    
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
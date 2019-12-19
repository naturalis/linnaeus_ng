{include file="../shared/admin-header.tpl"}

<div id="page-main">
	<form method="post" action="" id="theForm" enctype="multipart/form-data">
    <p>
        This function allows you to create a matrix-key from a spreadsheet. The expected format if tuned to a specific key,
        designed for Naturalis Biodiversity Centre. <a href="{$baseUrl}admin/media/system/sample-nbc-matrix.csv">{t}Download a sample CSV-file{/t}</a>.
	</p>
    <p>
    	The name in cell A1 is treated as project name. If it already exists in the database, the matrix is created under that
        project. In that case, if the (optional) matrix name in cell A3 exists, you will be asked how to treat existing data: delete
        it, or create a new matrix next to the existing one.<br />
        Also, when you create a matrix into an existing project, the names in column B will first be matched against existing species
        names (or variations, depending on your data). Next, against existing matrix names (can be used for "stacked" matrix keys). And
        if that fails, a new species or variation is created.
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
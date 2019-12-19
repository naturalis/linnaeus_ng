{include file="../shared/admin-header.tpl"}

<div id="page-main">
<form method="post" action="" enctype="multipart/form-data">
	<input type="hidden" name="MAX_FILE_SIZE" value="10000000" />
    <input type="hidden" name="rnd" value="{$rnd}" />
    <p>
	<input name="uploadedfile" type="file" />
    </p>
    <p>
    field separator:
    <label><input type="radio" value="," name="fieldsep" />comma</label>
    <label><input type="radio" value=";" name="fieldsep" checked="checked" />semi-colon</label>
    </p>
    <p>
    <label><input type="checkbox" name="del_all" value="1" />replace all data, not just that for the categories in your file (see below).</label><br />
    </p>
    <p>
	<input type="submit" value="{t}upload{/t}" />
    </p>
</form>
</div>

{include file="../shared/admin-messages.tpl"}

<div class="page-generic-div">
<p>
{t}To load taxa content from file, click the 'browse'-button above, select the file to load from your computer and click 'upload'.{/t}
{t}The file must meet the following conditions:{/t}
</p>
<ol>
	<li>{t}The format needs to be CSV.{/t}</li>
	<li>{t}The field delimiter must be either a comma or a semi-colon, and can be selected above.{/t}</li>
	<li>{t}The fields in the CSV-file *may* be enclosed by " (double-quotes), but this is not mandatory.{/t}</li>
	<li>{t}Each line consists of the following fields:{/t}
		<ol>
            <li>Taxon ID <i>or</i> scientific name; interpretation is based on the value being an integer or not</li>
            <li>{t}Language ID{/t} ({foreach item=v key=i from=$session.admin.project.languages}{if $i>0}, {/if}{$v.language}: {$v.language_id}{/foreach})</li>
            <li>{t}One or more fields containing the actual content, one field per category. All content will be loaded <i>as is</i>, any existant data for that combination of taxon and category will be overwritten without warning.{/t}</li>
		</ol>
		{t}The first two fields are mandatory, all fields are expected in the order displayed above.{/t}
	</li>
	<li>{t}The first line should contain the field headers:{/t}
		<ol>
            <li>{t}Taxon ID: optional, program explicitly expects the first column ("A") to be the taxon ID.{/t}</li>
            <li>{t}Language ID: optional, program explicitly expects the second column ("B") to be the language ID.{/t}</li>
            <li>{t}The content column headers (mandatory) should contain the system names of the corresponding categories in your project. Currently, these are:{/t}
            	<ol>
                {foreach from=$categories.categories item=v}
	                <li>{$v.page}</li>
                {/foreach}
                </ol>
                Columns with other headers will be ignored (and can safely be kept in the sheet).<br />
			</li>
		</ol>
	</li>
</ol>
<p>
Please note: existant data for a category will be overwritten without warning. Existing data for a category for which <i>no</i> data has 
been specified in your sheet will remain untouched unless you check the checkbox above, in which case all old content for a taxon in the
specified language will be deleted before any new data is inserted.
</p>
<p>        
    MS Excel does not have the option to explicitly save a CSV-file as UTF-8 encoded. Doing so may not be necessary,
    however if your data contains non-ASCII characters (like &euml; of &iuml;), it is advisable to explicitly convert the file. 
    Do so by opening the saved CSV-file in Notepad or another text-editor, and save it while explicitly specifying UTF-8 
    as encoding. When importing, the program will compare the length of the saved field with the data in the sheet, and will
    generate an error of the lengths differ, which is likely to be an indicator for encoding problems.<br />
</p>



</div>
</form>

{include file="../shared/admin-footer.tpl"}

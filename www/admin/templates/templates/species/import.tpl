{include file="../shared/admin-header.tpl"}

<div id="page-main">
<form method="post" action="" enctype="multipart/form-data">
	<input type="hidden" name="MAX_FILE_SIZE" value="10000000" />
    <input type="hidden" name="rnd" value="{$rnd}" />
	<input name="uploadedfile" type="file" /><br />
	<input type="submit" value="{t}upload{/t}" />
</form>
</div>

{include file="../shared/admin-messages.tpl"}

<form method="post" action="" enctype="multipart/form-data">
<div class="page-generic-div">
<p>
{t}To load taxa content from file, click the 'browse'-button above, select the file to load from your computer and click 'upload'.{/t}
{t}The file must meet the following conditions:{/t}
</p>
<ol>
	<li>{t}The format needs to be CSV.{/t}</li>
	<li>{t}The field delimiter must be a comma.{/t}</li>
	<li>{t}The fields in the CSV-file *may* be enclosed by " (double-quotes), but this is not mandatory.{/t}</li>
	<li>{t}Each line consists of the following fields:{/t}
		<ol>
            <li>{t}Taxon ID (currently there is no automated lookup - sorry){/t}</li>
            <li>{t}Language ID{/t}</li>
            <li>{t}One or more fields containing the actual content, one field per category. All content will be loaded <i>as is</i>, any existent data for that combination of taxon and category will be overwritten without warning.{/t}</li>
		</ol>
		{t}The first two fields are mandatory, all fields are expected in the order displayed above.{/t}
	</li>
	<li>{t}The first line should contain the field headers:{/t}
		<ol>
            <li>{t}Taxon ID: optional, program explicitly expects the first column to be the taxon ID.{/t}</li>
            <li>{t}Language ID: optional, program explicitly expects the first column to be the taxon ID.{/t}</li>
            <li>{t}The content column headers should contain the system names of the corresponding categories in your project. Currently, these are:{/t}
            	<ol>
                {foreach from=$categories.categories item=v}
	                <li>{$v.page}</li>
                {/foreach}
                </ol>
            </li>
		</ol>
	</li>
</ol>

</div>
</form>

{include file="../shared/admin-footer.tpl"}

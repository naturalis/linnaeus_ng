{include file="../shared/admin-header.tpl"}

<div id="page-main">
<form method="post" action="" enctype="multipart/form-data">
	<input type="hidden" name="MAX_FILE_SIZE" value="10000000" />
    <input type="hidden" name="rnd" value="{$rnd}" />
    <p>
	<input name="uploadedfile" type="file" />
    </p>
    <p>
    <label><input type="checkbox" name="del_existing" value="1" />delete existing remote images for the specified taxa.</label><br />
    <!-- label><input type="checkbox" name="check_exists" value="1" />check if the images exist (by retrieving their headers).</label><br />
	&nbsp;&nbsp;<label><input type="radio" name="insert_non_existing" value="0" />insert URLs even if the headers cannot be be r.</label><br />
	&nbsp;&nbsp;<label><input type="radio" name="insert_non_existing" value="1" />check if the images exist (by retrieving their headers).</label><br / -->
    </p>
    <p>
	<input type="submit" value="{t}upload{/t}" />
    </p>
</form>
</div>

{include file="../shared/admin-messages.tpl"}

<div class="page-generic-div">
<p>
{t}To load remote image URLs from file, click the 'browse'-button above, select the file to load from your computer and click 'upload'.{/t}
{t}The file must meet the following conditions:{/t}
</p>
<ol>
	<li>{t}The format needs to be CSV.{/t}</li>
	<li>{t}The field delimiter must be a comma.{/t}</li>
	<li>{t}The fields in the CSV-file *may* be enclosed by " (double-quotes), but this is not mandatory.{/t}</li>
	<li>{t}Each line consists of the following fields:{/t}
		<ol>
            <li>Taxon ID <i>or</i> scientific name; interpretation is based on the value being an integer or not</li>
            <li>{t}A field containing one or more image URL's, separated with semi-colons.{/t}</li>
		</ol>
	</li>
</ol>
The application does not currently check whether the images actually exist. Mime-types are inferred from the file-extension.

</div>
</form>

{include file="../shared/admin-footer.tpl"}

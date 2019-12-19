{include file="../shared/admin-header.tpl"}

<form method="post" action="" enctype="multipart/form-data">
<div id="page-main">
	<input type="hidden" name="MAX_FILE_SIZE" value="10000000" />
    <input type="hidden" name="rnd" value="{$rnd}" />
    <p>
	<input name="uploadedfile" type="file" />
    </p>
    <p>
    <label><input type="checkbox" name="del_existing" value="1" />delete existing local images for the specified taxa.</label><br />

    </p>
    <p>
	<input type="submit" value="{t}upload{/t}" />
    </p>
</div>

{include file="../shared/admin-messages.tpl"}

<div class="page-generic-div">
<p>
{t}To load local image names from file, click the 'browse'-button above, select the file to load from your computer and click 'upload'.{/t}
{t}The file must meet the following conditions:{/t}
</p>
<ol>
	<li>{t}The format needs to be CSV.{/t}</li>
	<li>{t}The field delimiter is a {/t}<input type="text" name="delimiter" value="," maxlength="1" style="width:10px;font-size:12px"> (comma by default)</li>
	<li>{t}The fields in the CSV-file *may* be enclosed by " (double-quotes), but this is not mandatory.{/t}</li>
	<li>{t}Each line consists of the following fields:{/t}
		<ol>
            <li>Taxon ID <i>or</i> scientific name; interpretation is based on the value being an integer or not</li>
            <li>{t}A field containing one or more image names, separated with semi-colons.{/t}</li>
			<li>{t}An optional third field, in which "1" (or "y" or "yes") indicates if the image is the overview image for that species. If set to "1" while multiple images have been specified in the second field, the first of those images is considered the overview image.{/t}</li>
		</ol>
	</li>
</ol>
The application does not currently check whether the images actually exist. Mime-types are inferred from the file-extension.

</div>
</form>

{include file="../shared/admin-footer.tpl"}

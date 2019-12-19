{include file="../shared/admin-header.tpl"}

<div id="page-header-localmenu">
<div id="page-header-localmenu-content">
    <a href="mass_upload.php" class="allLookupLink">{t}Mass upload images{/t}</a>
    &nbsp;&nbsp;
    <a href="browse_media.php" class="allLookupLink">{t}Browse images{/t}</a>
</div>
</div>

<div id="page-main">

<form enctype="multipart/form-data" action="" method="post">
<p>
This function allows you to upload files to your projects media directory, including mutiple files within a ZIP-file.
</p>
<p>
<input type="hidden" name="id" value="{$id}" />  
<input type="hidden" name="rnd" value="{$rnd}" />
{t}Choose a file to upload:{/t} <input name="uploadedfile" type="file" />
</p>
<p>
How to handle files that already exist:<br />
<label><input type="radio" name="overwrite" value="overwrite" checked="checked" />overwrite (when file exists, overwrite it)</label><br />
<label><input type="radio" name="overwrite" value="rename" />rename (when file exists, rename new file to "file (1)" and upload)</label><br />
<label><input type="radio" name="overwrite" value="skip" />skip (don't upload files that already exist)</label><br />
(the application only checks for the same filename and does not look at any other attributes)
</p>
<p>
<input type="submit" value="{t}upload{/t}" />&nbsp;
<input type="button" value="{t}back{/t}" onclick="window.open('admin_index.php','_top')" />
</p>
</form>
</div>
{include file="../shared/admin-messages.tpl"}

<div class="page-generic-div">
<p>
</p>
Current server-limited maximum on file uploads: {$iniSettings.maximum}MB
<p>
<b>{t}Allowed MIME-types{/t}</b><br />
{t}Files of the following MIME-types are allowed:{/t}<ul>
{section name=i loop=$allowedFormats}
<li>
	{$allowedFormats[i].mime}
	({t _s1=$allowedFormats[i].media_name}%s{/t}; {t}max.{/t} {math equation="x/y" x=$allowedFormats[i].maxSize y=1000000 format="%.0fM"} {t}per file{/t}{if $allowedFormats[i].media_type=='archive'}; {t}see below for information on uploading archives{/t}{/if})</li>
{/section}
</ul>
</p>
<p>
<b>{t}Overwriting and identical file names{/t}</b><br />
{t}All uploaded files are assigned unique file names, so there is no danger of accidentally overwriting an existing file. The original file names are retained in the project database and shown in the media management screens.{/t}
</p>
<p>
<b>{t}Uploading multiple files at once{/t}</b><br />
{t}In the current HTML-specification there are no cross-broswer possibilities for the uploading of multiple files at once without resorting to Flash or Java. Despite this limitation, you can upload several images at once by adding them to a ZIP-archive and uploading that file. The application will unpack the ZIP-file and store the separate files contained within. To the files within a ZIP-file the same limitations with regards to format and size apply as to files that are uploaded normally.{/t}
</p>

<p>
<b>Temporary alternative</b><br />
Drag & drop files below. Be aware: same limitations in regards to size and format apply, but you won't
get any feedback when they are enforced. You <i>can</i> drag & drop multiple files at once, though.<br />
Wait for the upload of all dropped files to finish before leaving this page; take care, progress bars
and status icons appear as overlays and can be a little tricky to see against a white background.<br />
Not sure whether there is a limit on the number of files you can drop at once, but circa 200 files in
one drop seemed to work fine.<br />
This uploader never overwrites, always makes a new copy of a duplicate file by adding "(1)" etc. To avoid
duplicates, delete old file first via the <a href="browse_media.php">browse page</a>. That page now also
includes an option to delete all files in the project media directory with one action.
<script src="../../javascript/dropzone/dropzone.js"></script>
<link rel="stylesheet" href="../../javascript/dropzone/dropzone.css">

<script >
Dropzone.options.myAwesomeDropzone = {
  init: function() {
    this.on("addedfile", function(file)
	{
		$('#noise').html("Added " + file.name);
	});
    this.on("success", function(file)
	{
		$('#noise').html("Uploaded" + file.name);
	});
  }
};
</script>
<p id=noise></p>
<form action="mass_upload.php"
      class="dropzone"
      id="my-awesome-dropzone"></form>

</p>      








</div>


{include file="../shared/admin-footer.tpl"}
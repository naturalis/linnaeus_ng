{include file="../shared/admin-header.tpl"}

<div id="page-header-localmenu">
<div id="page-header-localmenu-content">
    <a href="browse_media.php" class="allLookupLink">{t}Browse images{/t}</a>
    &nbsp;&nbsp;
    <a href="mass_upload.php" class="allLookupLink">{t}Mass upload images{/t}</a>
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
<input type="button" value="{t}back{/t}" onclick="window.open('media.php?id={$id}','_top')" />
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
</div>


{include file="../shared/admin-footer.tpl"}
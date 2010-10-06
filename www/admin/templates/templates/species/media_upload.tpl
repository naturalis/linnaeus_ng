{include file="../shared/admin-header.tpl"}

<div id="page-main">


{if $id}
<p>
<form enctype="multipart/form-data" action="" method="post">
<input type="hidden" name="taxon_id" id="taxon_id" value="{$id}" />  
<input type="hidden" name="rnd" value="{$rnd}" />
Choose a file to upload: <input name="uploadedfile" type="file" /><br />
<input type="submit" value="upload" />
</form>
</p>
{/if}
<p>
<a href="media.php?id={$id}">See all media for this taxon</a>
</p>
</div>

{include file="../shared/admin-messages.tpl"}
{if $id}
<div class="page-generic-div">
<p>
<b>Allowed MIME-types</b><br />
Files of the following MIME-types are allowed:<ul>
{section name=i loop=$allowedFormats}
<li>{$allowedFormats[i].mime} ({$allowedFormats[i].media_name}; max. {math equation="x/y" x=$allowedFormats[i].maxSize y=1000000 format="%.0fM"} per file{if $allowedFormats[i].media_type=='archive'}; see below for information on uploading archives{/if})</li>
{/section}
</ul>
</p>
<p>
<b>Size</b><br />
The maximum size of a file you are allowed to upload is influenced by the settings of the server. Currently, these are:<br />
Upload maximum size: {$iniSettings.upload_max_filesize}<br />
POST maximum size: {$iniSettings.post_max_size}<br />
The effective size limit is determined by the smallest of these two.
</p>
<p>
<b>Overwriting and identical filenames</b><br />
All uploaded files are assigned unique filenames, so there is no danger of accidentally overwriting an existing file. The original file names are retained in the project database and shown in the media management screens. Please be aware that there is no check on duplicate original filenames during the upload process, so do not rely on the being overwritten of identical filenames for the maintenance of your database.
</p>
<p>
<b>Uploading multiple files at once</b><br />
In the current HTML-specification there are no cross-broswer possibilities for the uploading of multiple files at once without resorting to Flash or Java. Despite this limitation, you can upload several images at once by adding them to a ZIP-archive and uploading that file. The application will unpack the ZIP-file and store the separate files contained within. To the files within a ZIP-file the same limitations with regards to format and size apply as to files that are uploaded normally.
</p>
</div>
{/if}

{literal}
<script type="text/JavaScript">
$(document).ready(function(){
{/literal}
	allSetHeartbeatFreq({$heartbeatFrequency});
	taxonSetHeartbeat('{$session.user.id}','{$session.system.active_page.appName}','{$session.system.active_page.controllerBaseName}','{$session.system.active_page.viewName}');
{literal}	
});
</script>
{/literal}


{include file="../shared/admin-footer.tpl"}
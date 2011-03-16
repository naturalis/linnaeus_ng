{include file="../shared/admin-header.tpl"}

<div id="page-main">

{if $id}
<p>
<form enctype="multipart/form-data" action="" method="post">
<input type="hidden" name="id" value="{$id}" />  
<input type="hidden" name="rnd" value="{$rnd}" />
{t}Choose a file to upload:{/t} <input name="uploadedfile" type="file" /><br />
<input type="submit" value="{t}upload{/t}" />
<input type="button" value="{t}back{/t}" onclick="window.open('edit.php?id={$id}','_self');" />
</form>
</p>
{/if}
</div>
{include file="../shared/admin-messages.tpl"}
{if $id}
<div class="page-generic-div">
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
<b>{t}Overwriting and identical filenames{/t}</b><br />
{t}All uploaded files are assigned unique filenames, so there is no danger of accidentally overwriting an existing file. The original file names are retained in the project database and shown in the media management screens. Please be aware that there is no check on duplicate original filenames during the upload process, so do not rely on the being overwritten of identical filenames for the maintenance of your database.{/t}
</p>
<p>
<b>{t}Uploading multiple files at once{/t}</b><br />
{t}In the current HTML-specification there are no cross-broswer possibilities for the uploading of multiple files at once without resorting to Flash or Java. Despite this limitation, you can upload several images at once by adding them to a ZIP-archive and uploading that file. The application will unpack the ZIP-file and store the separate files contained within. To the files within a ZIP-file the same limitations with regards to format and size apply as to files that are uploaded normally.{/t}
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
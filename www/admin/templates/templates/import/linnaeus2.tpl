{include file="../shared/admin-header.tpl"}

<div id="page-main">
<form method="post" action="" id="theForm" enctype="multipart/form-data">
<p>
This module allows you to import data from an existing Linnaeus 2-project. In order to do so, export the data of the project as a standard Linnaeus 2 XML export file and upload that file in the form below. If you wish to import media files as well, save them to a location that can be accessed by this webserver.
</p>
<p>
{if $s.file}
<b>XML-file "{$s.file}" loaded.</b> (click <span class="pseudo-a" onclick="$('#clear').val('file');$('#theForm').submit()">here</span> to load another file)
{else}
XML-file to load: <input name="importFile" type="file" /><br />
{/if}
</p>
<p>
Set the paths below where the media files and, if they exists, thumbnails can be found. These paths should be directly accessible to the webserver.<br />
Please check the corresponding checboxes if you do not wish to import images and/or thumbnails.
</p>
<p>
{if $s.imagePath}
<b>Path to images: "{$s.imagePath}"</b> (click <span class="pseudo-a" onclick="$('#clear').val('imagePath');$('#theForm').submit()">here</span> to change)
{elseif $s.imagePath===false}
<b>Do not load images</b> (click <span class="pseudo-a" onclick="$('#clear').val('imagePath');$('#theForm').submit()">here</span> to change)
{else}
Path to images:  <input type="text" name="imagePath" />&nbsp;or&nbsp;<label><input type="checkbox" name="noImages" value="1">do not load images</label><br />
{/if}
</p>
<p>
{if $s.thumbsPath}
<b>Path to thumbnails: "{$s.thumbsPath}"</b> (click <span class="pseudo-a" onclick="$('#clear').val('thumbsPath');$('#theForm').submit()">here</span> to change)
{elseif $s.thumbsPath===false}
<b>Do not load thumbnails</b> (click <span class="pseudo-a" onclick="$('#clear').val('thumbsPath');$('#theForm').submit()">here</span> to change)
{else}
Path to thumbnails: <input type="text" name="thumbsPath" />&nbsp;or&nbsp;<label><input type="checkbox" name="noThumbs" value="1">do not load thumbnails</label><br />
{/if}
</p>
<p>
<input type="hidden" name="clear" id="clear" value="" />
<input type="hidden" name="process" id="process" value="0" />
{if $s.file && isset($s.imagePath) && isset($s.thumbsPath)}
<p>
Press the button to create the new project. In the steps after that, the project data will be loaded.
</p>
<input type="button" onclick="$('#process').val('1');$('#theForm').submit()" value="Create project" />
{else}
<input type="submit" value="save" />
{/if}
</form>
</p>
</div>

{include file="../shared/admin-messages.tpl"}
{include file="../shared/admin-footer.tpl"}
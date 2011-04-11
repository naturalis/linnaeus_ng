{include file="../shared/admin-header.tpl"}

<div id="page-main">
<form method="post" action="" id="theForm" enctype="multipart/form-data">
Specify import-file and locations:
<p>
{if $s.file}
XML-file "{$s.file}" loaded. (click <span class="pseudo-a" onclick="$('#clear').val('file');$('#theForm').submit()">here</span> to load another file)
{else}
XML-file: <input name="importFile" type="file" /><br />
{/if}
</p>
<p>
path to images: 
{if $s.imagePath}
"{$s.imagePath}" (click <span class="pseudo-a" onclick="$('#clear').val('imagePath');$('#theForm').submit()">here</span> to change)
{elseif $s.imagePath===false}
no images (click <span class="pseudo-a" onclick="$('#clear').val('imagePath');$('#theForm').submit()">here</span> to change)
{else}
<input type="text" name="imagePath" />&nbsp;or&nbsp;<label><input type="checkbox" name="noImages" value="1">no images</label><br />
{/if}
</p>
<p>
path to thumbnails:
{if $s.thumbsPath}
"{$s.thumbsPath}" (click <span class="pseudo-a" onclick="$('#clear').val('thumbsPath');$('#theForm').submit()">here</span> to change)
{elseif $s.thumbsPath===false}
no thumbnails (click <span class="pseudo-a" onclick="$('#clear').val('thumbsPath');$('#theForm').submit()">here</span> to change)
{else}
<input type="text" name="thumbsPath" />&nbsp;or&nbsp;<label><input type="checkbox" name="noThumbs" value="1">no thumbnails</label><br />
{/if}
</p>
<p>
<input type="hidden" name="clear" id="clear" value="" />
<input type="hidden" name="process" id="process" value="0" />
{if $s.file && isset($s.imagePath) && isset($s.thumbsPath)}
<input type="button" onclick="$('#process').val('1');$('#theForm').submit()" value="set project" />
{else}
<input type="submit" value="save" />
{/if}
</form>
</p>

<p>
</p>
</div>

{include file="../shared/admin-messages.tpl"}
{include file="../shared/admin-footer.tpl"}
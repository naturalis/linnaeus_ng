{include file="../shared/admin-header.tpl"}

<div id="page-main">
	<form method="post" action="" id="theForm" enctype="multipart/form-data">
		<p>
			This module allows you to import data from an existing Linnaeus 2-project. In order to do so, export the data of the project as a standard
			Linnaeus 2 XML export file and upload that file in the form below. If you wish to import media files as well, save them to a location that
			can be accessed by this webserver.
		</p>
		<fieldset>
			<legend>DATA</legend>
			<p>
				{if $s.file}
					<b>XML-file "{$s.file.name}" loaded.</b>
					(<span class="a" onclick="$('#clear').val('file');$('#theForm').submit()">load another file</span>)
				{else}
					XML-file to load: <input name="importFile" type="file" />&nbsp;*<br />
					<i>or</i><br />
					Specify the location of your file on the server:
					<input type="text" name="serverFile" style="width:500px;" />&nbsp;*
				{/if}
			</p>
		</fieldset>
		<br />
		<fieldset>
			<legend>MEDIA</legend>
			<p>
				Set the paths below where the media files can be found.<br />
				These paths should be fully qualified and be <b>writable</b> by the <i>webserver</i> (not the client from which you are viewing this page).<br />
				Please check the corresponding checkboxes if you do not wish to import images and/or thumbnails.
			</p>
			<p>
				Path to images:<br />
			{if $isSharedMediaDirWritable}
				{if $s.imagePath}
					<b>"{$s.imagePath}"</b> (<span class="a" onclick="$('#clear').val('imagePath');$('#theForm').submit()">change</span>)
				{elseif $s.imagePath===false}
					<b>Do not load images</b> (<span class="a" onclick="$('#clear').val('imagePath');$('#theForm').submit()">change</span>)
				{else}
					<input type="text" name="imagePath" style="width:500px;"/>&nbsp;or&nbsp;
					<label><input type="checkbox" name="noImages" value="1">do not load images</label>&nbsp;*<br />
				{/if}
			{else}
				<span class="message-error">The project media parent directory "{$mediaDir}" is read only!</span><br />
					<input type="hidden" name="noImages" value="1" />
					<input type="hidden" name="noThumbs" value="1" />
					<label><input type="checkbox" checked="checked" disabled="disabled">do not load images</label>&nbsp;<br />
					<label><input type="checkbox" checked="checked" disabled="disabled">do not load thumbnails</label>&nbsp;
			{/if}
			</p>
			{* if $isSharedMediaDirWritable}
			<p>
				Path to thumbnails:<br />
				{if $s.thumbsPath}
					<b>"{$s.thumbsPath}"</b> (<span class="a" onclick="$('#clear').val('thumbsPath');$('#theForm').submit()">change</span>)
				{elseif $s.thumbsPath===false}
					<b>Do not load thumbnails</b> (<span class="a" onclick="$('#clear').val('thumbsPath');$('#theForm').submit()">change</span>)
				{else}
					<input type="text" name="thumbsPath" style="width:500px;"/>&nbsp;or&nbsp;
					<label><input type="checkbox" name="noThumbs" value="1">do not load thumbnails</label>&nbsp;*
				{/if}
			</p>
			{/if *}
		</fieldset>


		<p>
		<input type="hidden" name="clear" id="clear" value="" />
		<input type="hidden" name="process" id="process" value="0" />
		{if $s.file && isset($s.imagePath) && isset($s.thumbsPath)}
		<p>
		Press the button to create the new project. In the steps after that, the project data will be loaded.
		</p>
		<input type="button" onclick="$('#process').val('1');$('#theForm').submit()" value="Create project" />
		{else}
		<input type="submit" value="Save" />
		{/if}
		</p>
	</form>
</div>

{include file="../shared/admin-messages.tpl"}
{include file="../shared/admin-footer.tpl"}
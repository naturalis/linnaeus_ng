{include file="../shared/admin-header.tpl"}

<div id="page-main">

	<form method="post" action="" id="theForm" enctype="multipart/form-data">
		<p>
			This module allows you to import data from an SDD-file.
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
			<p>
				Please note: the importer will attempt to match taxa in the file to those in the Species module. Matching will be literal, albeit
				not case-sensitive. For instance, "meles meles" will not match "Meles meles (Linnaeus, 1758)", but will match "Meles meles".
			</p>
		</fieldset>
		<br />
		<fieldset>
			<legend>MEDIA</legend>
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
			{/if}
			</p>
		</fieldset>
		
		
		<p>
		<input type="hidden" name="clear" id="clear" value="" />
		<input type="hidden" name="process" id="process" value="0" />
		<input type="hidden" name="rnd" value="{$rnd}" />
		{if $s.file && isset($s.imagePath) && isset($s.thumbsPath)}
		<input type="button" onclick="$('#process').val('1');$('#theForm').submit()" value="import matrix" />
		{else}
		<input type="submit" value="save" />
		{/if}
		</p>
	</form>
</div>

{include file="../shared/admin-messages.tpl"}
{include file="../shared/admin-footer.tpl"}

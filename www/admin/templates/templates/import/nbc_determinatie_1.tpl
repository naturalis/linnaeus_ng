{include file="../shared/admin-header.tpl"}

<div id="page-main">
	<form method="post" action="" id="theForm" enctype="multipart/form-data">
		<p>
			First, open the Excel XML file in MS Excel, select the entire first sheet, and format is as 'text'.<br /> 
			Then, save the first sheet as CSV-file; this effectively gets rid of all the superfluous Excel-related XML-code. <br />
			Then, load the file below and click 'Parse file'.
		</p>
		<fieldset>
			<legend>DATA</legend>
			<p>
				{if $s.file}
					<b>CSV-file "{$s.file.name}" loaded.</b>
					(<span class="a" onclick="$('#clear').val('file');$('#theForm').submit()">load another file</span>)
				{else}
					CSV-file to load: <input name="importFile" type="file" />&nbsp;*<br />
				{/if}
			</p>
		</fieldset>

		
		<p>
		<input type="hidden" name="clear" id="clear" value="" />
		<input type="hidden" name="process" id="process" value="0" />
		{if $s.file}
		<p>
		Press the button to create the new project. In the steps after that, the project data will be loaded.
		</p>
		<input type="button" onclick="$('#process').val('1');$('#theForm').submit()" value="Parse file" />
		{else}
		<input type="submit" value="Save" />
		{/if}
		</p>
	</form>
</div>

{include file="../shared/admin-messages.tpl"}
{include file="../shared/admin-footer.tpl"}
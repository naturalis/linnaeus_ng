{include file="../shared/admin-header.tpl"}

<style>
.small {
	color:#666;
	font-size:0.9em;
}
li.main-list {
	padding-bottom:1px;
	margin-bottom:1px;
	border-bottom:1px solid #ddd;
	list-style-type:none;
}
table.lines {
	border-collapse:collapse;
}
.warnings {
	color:orange;
}
.errors {
	color:red;
}
div.messages {
	margin-left:10px;
}
td {
	vertical-align:top;
	border-bottom:1px solid #efefef;
}
td.taxon {
  width:250px;
  background-color:#efefef;
}
td.text, td.text-head {
  max-width: 120px;
  min-width: 120px;
}
td.text {
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}
td.text-head {
	vertical-align:bottom;
	background-color:#efefef;
}
td.no-line {
	border-bottom:none;
}
td.no-fill {
	border-bottom:none;
	background-color:#fff;
}
.variable-list {
	color:#69F
}
</style>

<div id="page-main">

{if $lines}

<h4>{t}Parsed lines:{/t}</h4>


<form method="post" action="import_passport_file_process.php">
<input type="hidden" name="rnd" value="{$rnd}" />
<input type="hidden" name="action" value="save" />

<ul style="padding-left:0px;">
	<li style="list-style-type:none;">
    <table>

    	<tr><td class="taxon no-line no-fill"></td>
        {foreach $lines.topics v k}
            <td class="text-head{if $v.error} errors{/if}">
            {$v.column}
            {if $v.error}<br />[{$v.error}]{/if}
            </td>
		{/foreach}
    	</tr>

    	<tr><td class="taxon no-fill"></td>
        {foreach $lines.languages v k}
            <td class="text-head{if $v.error} errors{/if}">
            {$v.column}
            {if $v.error}<br />[{$v.error}]{/if}
            </td>
		{/foreach}
    	</tr>

    </table>
	</li>
	{foreach $lines.data v k}
	<li style="list-style-type:none;">
    <table><tr>
    	<td class="taxon{if $v.taxon.error} errors{/if}">
        	{$v.taxon.conceptName}
	        {if $v.taxon.error}<br />[{$v.taxon.error}]{/if}
        </td>
        {foreach $v.data vv kk}
            <td class="text{if $v.taxon.error || $lines.languages[$kk].error || $lines.topics[$kk].error} errors{/if}">
            {$vv}
            </td>
		{/foreach}
    </tr></table>
    </li>
	{/foreach}
	</ul>    
</ul>

<p>
	Cells in red will not be saved due to the errors displayed. Click "save" to the other cells.
</p>
<p>
	Choose how to handle text if it already exist in your database. If data for a combination of taxon, topic and language already exists, then:<br/>
    <label><input type="radio" name="handle_existing" value="skip" />skip importing the new data for that record</label><br />
    <label><input type="radio" name="handle_existing" value="overwrite" checked="checked" />overwrite existing data for that record with the new data</label><br />
    <label><input type="radio" name="handle_existing" value="append" />append new data to existing for that record</label><br />
    <label><input type="radio" name="handle_existing" value="prepend" />prepend new data to existing for that record</label><br />
</p>

<input type="submit" value="{t}save{/t}" />

</form>

<p>

<a href="import_passport_file_reset.php">{t}load a new file{/t}</a>

</p>

{else}

<h4>{t}Import a file:{/t}</h4>

<form method="post" action="" enctype="multipart/form-data">
	<input type="hidden" name="MAX_FILE_SIZE" value="10000000" />
	<input name="uploadedfile" type="file" /><br />

	<p>
	{t}Choose CSV field delimiter:{/t}
    <ul>
    	<li><label><input type="radio" name="delimiter" value="comma"/>, {t}(comma){/t}</label></li>
        <li><label><input type="radio" name="delimiter" value="semi-colon" checked="checked"/>; {t}(semi-colon){/t}</label></li>
        <li><label><input type="radio" name="delimiter" value="tab"/>{t}tab{/t}</label></li>
	</ul>
	</p>
	        
		<!--
			CSV field enclosure:
            <label><input type="radio" name="enclosure" value="double" checked="checked" />" (double qoutes)</label>
            <label><input type="radio" name="enclosure" value="single" />' (quote)</label>
            <label><input type="radio" name="enclosure" value="none" />none</label><br />
		-->
	<p>
	<input type="submit" value="{t}upload{/t}" />
    </p>
</form>

{include file="../shared/admin-messages.tpl"}


<p>
{t}File and data must meet the following conditions:{/t}
</p>
<ul>
	<li>
    	{t}The format needs to be CSV.{/t}
        {t}Please take into account the following:{/t}
        <ul>
            <li>
            	{t}The field delimiter must be a comma, semi-colon or tab.{/t}
                {t}Although the name of the filetype suggests differently (the "CS" in CSV stands for "comma separated"), it is not always obvious by what character the fields are actually separated when saving as CSV from MS Excel or other spreadsheet programs. Sometimes the program allows you to choose the delimiter when exporting, more often it chooses the delimiting character for you.{/t}
                {t}If you are unsure what the delimiting character in your file is, please open it in a plain text-editor (like Notepad) to check, after you have saved it from your spreadsheet program.{/t}
            	{t}The example file below uses a semi-colon as delimiting character. This is also the default setting when importing; you can change it by selecting another delimiter above.{/t}
			</li>
            <li>{t}The fields in the CSV-file may be enclosed by " (double-quotes), but this is not mandatory.{/t}</li>
        </ul>
    </li>
	<li>{t}There should be two header lines:{/t}
        <ul>
            <li>{t}The first line should contain the topic of the texts in that column. Valid topics are:{/t}
                <ul class="variable-list">
                	{foreach $categories  v}
                    {if $v.type!='auto'}
                    <li>{$v.page}</li>
                    {/if}
                    {/foreach}
                </ul>    
            	{t}Please note that the header must be identical to one of the titles above, regardless of the language of the actual text.{/t}
            </li>
            <li>{t}The second line must contain the language of the texts in that column. Languages defined in your poject are currently:{/t}
                <ul class="variable-list">
                	{foreach $languages  v}
                    <li>{$v.language}</li>
                    {/foreach}
                </ul>
            </li>
        </ul>    
    </li>
	<li>{t}All following lines contain data and should consist of the following fields, in this order:{/t}
		<ol>
		<li>{t}Valid scientific name of taxon{/t} {t}(mandatory, must already exist in database){/t}</li>
        <li>{t}Text corresponding with that taxon and the topic and language of the column.{/t} {t}(mandatory){/t}</li>
		</ol>
	</li>
	<li>
		{t}After uploading, you can choose how to treat data for records that are already present in the database.{/t}
	</li>
</ul>

<p><a href="import_passport_file_example.php">{t}download a sample file{/t}</a></p>

{/if}
</div>
</form>

{include file="../shared/admin-footer.tpl"}

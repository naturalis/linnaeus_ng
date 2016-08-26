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
table.lines tr td:nth-child(1) {  
	min-width:215px;
}
table.lines tr td:nth-child(2) {  
	min-width:100px;
}
table.lines tr td:nth-child(3) {  
	min-width:210px;
}
table.lines tr td:nth-child(4) {  
	min-width:180px;
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
.variable-list {
	color:#36F;
}
</style>


{include file="../shared/admin-messages.tpl"}


<div id="page-main">


{if $lines}

<h4>{t}Parsed lines:{/t}</h4>


<form method="post" action="import_file_process.php">
<input type="hidden" name="rnd" value="{$rnd}" />
<input type="hidden" name="action" value="save" />

<ul style="padding-left:0px;">
	<li style="list-style-type:none;">
    	<table class="lines"><tr>
        	<td><span class="small">{t}taxon{/t}</span></td>
        	<td><span class="small">{t}rank{/t}</span></td>
        	<td><span class="small">{t}parent{/t}</span></td>
	        <td><span class="small">{t}common name{/t}</span></td>
	        <td><span class="small">{t}import status{/t}</span></td>
		</tr></table>
	</li>
    {$willsave=false}
	{foreach $lines v k}
    {if !$willsave && !$v.errors}{$willsave=true}{/if}
	<li class="main-list">
    	<table class="lines"><tr>
    		<td>{$v[$importColumns['conceptName']]}</td>
        	<td>{$v[$importColumns['rank']]}</td>
        	<td>{$v[$importColumns['parent']]}</td>
	        <td>{$v[$importColumns['commonName']]}</td>
	        <td>
            	{if $v.errors}
                	<span class="errors">{t}errors, will not import{/t}</span>
                {else if $v.warnings}
                	<span class="warnings">{t}will import, with warnings{/t}</span><br />
                    <label><input type="checkbox" value="{$v.line_id}" name="do_not_import[]" />{t}do not import{/t}</label>
                {else}&#10003;{/if}</td>
		</tr></table>
        {if $v.warnings}
        <div class="messages">
        <span class="warnings">warning(s):</span>
        <ul>
        {foreach $v.warnings e}
        <li>
        	{$e.message}
        	{if $e.data}{$i=0}({foreach $e.data d dk}{if $i++>0}, {/if}{$dk}: {$d}{/foreach}){/if}
        </li>
        {/foreach}
        </ul>
        </div>
        {/if}
        {if $v.errors}
        <div class="messages">
        <span class="errors">error(s):</span>
        <ul>
        {foreach $v.errors e}
        <li>
        	{$e.message}
        	{if $e.data}{$i=0}({foreach $e.data d dk}{if $i++>0}, {/if}{$dk}: {$d}{/foreach}){/if}
		</li>
        {/foreach}
        </ul>
        </div>
        {/if}
	</li>
	{/foreach}
</ul>
{if $willsave}
<input type="submit" value="{t}save{/t}" />
{else}
{t}(nothing to save){/t}
{/if}
</form>

<p>

<a href="import_file_reset.php">{t}load a new file{/t}</a>

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
{t}To load a list of taxa from file, click the 'browse'-button above, select the file to load from your computer and click 'upload'.
The contents of the file will be displayed so you can review them before they are saved to your project's database.{/t}<br />
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
	<li>{t}There should be one taxon per line. No header line should be present.{/t}</li>
	<li>{t}Each line should consist of the following fields, in this order:{/t}
		<ol>
		<li>{t}Valid scientific name{/t} {t}(mandatory){/t}</li>
        <li>{t}Rank{/t} {t}(mandatory){/t}</li>
        <li>{t}Parent name{/t} {t}(optional){/t}</li>
        <li>{t _s=$session.admin.project.languageList[$session.admin.project.default_language_id].language}Common name in %s{/t} {t}(optional){/t}</li>
		</ol>
	</li>
	<li>
		{t}You can upload a complete or partial taxonomy. If there are already taxa in your project's database, you can import additional taxa without altering the existing data.{/t}
	</li>
	<li>
    	{t}Valid scientifc names must be unique, both in the file and in the database. Duplicates will be discarded.{/t}
    </li>
	<li>{t}Ranks should match the list of ranks you have selected for your project.{/t}
		{if $projectRanks|@count==0}
		<br /><span class="message-error">{t}Currently, you have defined no ranks in this project.{/t} <a href="ranks.php">{t}Define ranks{/t}</a>.</span>
		{else}
		{t}These currently are:{/t}
		<ul class="variable-list">
		{section name=i loop=$projectRanks}
			<li>{$projectRanks[i].rank}</li>
		{/section}
		</ul>
		{t}Taxa of ranks that do not appear in this list will not be loaded. Please use the rank names exactly as they appear in the list.{/t}
		{/if}
	</li>
	<li>
    	{t}Taxonomic parent-child relations are inferred from the parent name in the third column. Parent names must be present in either the file or the database. By specifying parent names of taxa that are already in your database, you can import and attach partial taxonomies to the already existing taxonomic tree in your project. If a parent name cannot be resolved, the taxon will be saved without a parent. You can later assign a parent manually in the taxon editor.{/t}
	</li>
</ul>

<p><a href="../../media/system/example-taxon-import-file.csv">{t}download a sample file{/t}</a></p>

{/if}
</div>
</form>

{include file="../shared/admin-footer.tpl"}

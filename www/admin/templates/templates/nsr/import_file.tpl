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
	min-width:200px;
}
table.lines tr td:nth-child(2) {  
	min-width:100px;
}
table.lines tr td:nth-child(3) {  
	min-width:200px;
}
table.lines tr td:nth-child(4) {  
	min-width:150px;
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
</style>

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
	{foreach $lines v k}
	<li class="main-list">
    	<table class="lines"><tr>
    		<td>{$v[$importColumns['conceptName']]}</td>
        	<td>{$v[$importColumns['rank']]}</td>
        	<td>{$v[$importColumns['parent']]}</td>
	        <td>{$v[$importColumns['commonName']]}</td>
	        <td>
            	{if $v.errors}
                	<span class="errors">errors, will not import</span>
                {else if $v.warnings}
                	<span class="warnings">will import with warnings</span><br />
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
        <li>{$e.message}</li>
        {/foreach}
        </ul>
        </div>
        {/if}
	</li>
	{/foreach}
</ul>

<input type="submit" value="{t}save{/t}" />

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
	{t}CSV field delimiter:{/t}
    <label><input type="radio" name="delimiter" value="comma" checked="checked" />, {t}(comma){/t}</label>
    <label><input type="radio" name="delimiter" value="semi-colon" />; {t}(semi-colon){/t}</label>
    <label><input type="radio" name="delimiter" value="tab" />{t}tab{/t}</label>
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
{t}The file must meet the following conditions:{/t}
</p>
<ul>
	<li>{t}The format needs to be CSV.{/t}</li>
	<li>{t}The field delimiter must be a comma, semi-colon or tab stop, and can be selected above.{/t}</li>
	<li>{t}The fields in the CSV-file *may* be enclosed by " (double-quotes), but this is not mandatory.{/t}</li>
	<li>{t}There should be one taxon per line. No header line should be present.{/t}</li>
	<li>{t}Each taxon consists of the following fields:{/t}
		<ol>
		<li>{t}Taxon name{/t}</li>
        <li>{t}Taxon rank{/t}</li>
        <li>{t}Parent name (optional){/t}</li>
        <li>{t _s=$session.admin.project.languageList[$session.admin.project.default_language_id].language}Common name in %s (optional){/t}</li>
		</ol>
		{t}in that order. The first two are mandatory. {/t}
	</li>
	<li>
    	{t}Taxon names must be unique, both in the file and in the database. Duplicates will not be loaded.{/t}
    </li>
	<li>{t}Ranks should match the list of ranks you have selected for your project.{/t}
		{if $projectRanks|@count==0}
		<br /><span class="message-error">{t}Currently, you have defined no ranks in this project.{/t} <a href="ranks.php">{t}Define ranks{/t}</a>.</span>
		{else}
		{t}These currently are:{/t}
		<ul style="list-style:none;margin-left:0px;padding-left:20px;">		
		{section name=i loop=$projectRanks}
			<li>{$projectRanks[i].rank}</li>
		{/section}
		</ul>
		{t}Taxa with a rank that does not appear in this list will not be loaded.{/t}
		{/if}
	</li>
	<li>
    	{t}Parent-child relations are inferred from the parent name in the third column. Parent names must be present in either the file or the database. If a parent name cannot be resolved, the taxon will be saved as an orphan.{/t}
	</li>
</ul>

{/if}
</div>
</form>

{include file="../shared/admin-footer.tpl"}

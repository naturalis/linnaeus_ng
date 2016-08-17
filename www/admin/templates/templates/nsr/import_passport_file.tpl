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
table.lines tr td {  
	min-width:150px;
}
table.lines tr td:nth-child(1) {  
	min-width:215px;
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
	</li>
	{foreach $lines v k}
	<li>
	<table class="lines"><tr>
	{if $k==$importRows['topicNames']}
       <td>taxon</td>
		{foreach $lines.topics vv kk}
        <td>{$vv.column}|{$vv.page_id}|{$vv.error}</td>
        {/foreach}
	{elseif $k==$importRows['languages']}
       <td></td>
		{foreach $lines.languages vv kk}
        <td>{$vv.column}|{$vv.language_id}|{$vv.error}</td>
        {/foreach}
    {else}
		{foreach $v vv kk}
        {if $kk==$importColumns['conceptName']}
        <td>{$vv}</td>
        {else if $kk!='line_id'}
        <td>{$vv|@substr:0:25}...</td>
        {/if}
        {/foreach}
	{/if}
    </tr></table>
	</li>
	{/foreach}
</ul>





	<li class="main-list">
    	<table class="lines"><tr>
        {foreach $v l}
        <td>{$l}</td>
        {/foreach}
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

</ul>

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
	{t}CSV field delimiter:{/t}
    <label><input type="radio" name="delimiter" value="comma"/>, {t}(comma){/t}</label>
    <label><input type="radio" name="delimiter" value="semi-colon" />; {t}(semi-colon){/t}</label>
    <label><input type="radio" name="delimiter" value="tab" checked="checked"  />{t}tab{/t}</label>
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
</p>


<p><a href="../../media/system/example-taxon-import-file.csv">{t}download a sample file{/t}</a></p>

{/if}
</div>
</form>

{include file="../shared/admin-footer.tpl"}

{include file="../shared/admin-header.tpl"}

<style>
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

</style>

<div id="page-main">

<h4>{t}Results:{/t}</h4>

<form method="post" action="import_passport_file_process.php">
<input type="hidden" name="rnd" value="{$rnd}" />
<input type="hidden" name="action" value="save" />

<ul style="padding-left:0px;">
	<li style="list-style-type:none;">
    <table>

    	<tr><td class="taxon no-line no-fill"></td>
        {foreach $lines.topics v k}
            <td class="text-head">
            {$v.column}
            </td>
		{/foreach}
    	</tr>

    	<tr><td class="taxon no-fill"></td>
        {foreach $lines.languages v k}
            <td class="text-head">
            {$v.column}
            </td>
		{/foreach}
    	</tr>

    </table>
	</li>
	{foreach $lines.data v k}
	<li style="list-style-type:none;">
    <table><tr>
    	<td class="taxon">
        	{$v.taxon.conceptName}
        </td>
        {foreach $v.data vv kk}
            <td class="text">
			{if $lines.results[$k]['cells'][$kk]['saved']}
	        <span class="saved">&#10004;</span> <a href="paspoort.php?id={$v.taxon.id}" target="_new">show</a>
            {else}
			<span class="errors">&#10008;</span> {'<br />'|@implode:$lines.results[$k]['cells'][$kk]['errors']}
            {/if}
            </td>
		{/foreach}
    </tr></table>
    </li>
	{/foreach}
	</ul>    
</ul>

<p>
	<a href="import_passport_file_reset.php">{t}load a new file{/t}</a>
</p>

</div>

{include file="../shared/admin-footer.tpl"}

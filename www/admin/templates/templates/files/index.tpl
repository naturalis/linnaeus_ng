{include file="../shared/admin-header.tpl"}

<style>
ul.paginator {
	margin-left:0;
	padding-left:0;
}
ul.paginator li {
	display:inline-block;
	padding:0 7px 0 7px;
}
ul.paginator li.current-page a {
	text-decoration:none;
	color:red;
	font-weight:bold;
}
th:not(:nth-child(1)) {
	min-width:250px;
}
th {
	border-bottom:1px solid #999;
}
input[type=text]  {
	border:1px solid #eee;
	width:350px;
	font-family:Consolas, Trebuchet MS, Courier, monospace;
	font-size:10px;
}
</style>

<div id="page-main">

	<form id="searchForm" method="post">
	<input type="text" id="file_search" name="file_search" value="{$file_search}" placeholder="{t}search for files{/t}"><input type="submit" value="{t}search{/t}">
	<a href="javascript:$('#file_search').val('');$('#searchForm').submit();" title="{t}clear search{/t}">&#10006;</a>
	</form>
	
    <form id="theForm" method="post">
    <input type="hidden" name="rnd" value="{$rnd}" />
    <input type="hidden" id="action" name="action" value="" />
    <p>
    <table>
    <tr>
    	<th>{t}<input class="all-select" type="checkbox">{/t}</th>
    	<th>{t}filename{/t}</th>
    	<th>{t}URL (click to copy){/t}</th>
	</tr>
	{foreach $paginated.items v k}
    <tr class="tr-highlight">
    	<td><input class="select" type="checkbox" name="delete[]" value="{$k}"></td>
    	<td><a href="{$basePath}/{$v.fileName}" target="_blank">{$v.fileName}</a></td>
    	<td>
        	<input type="text" onClick="this.select();document.execCommand('copy');$('#msg-{$k}').html(_('URL copied to clipboard')).fadeOut(3000);" value="{$basePath}{$v.fileName}" />
            <span id="msg-{$k}"></span>
        </td>
	</tr>
    {/foreach}
    </table>
    
	{$paginated.pager}

    </p>
    <p>
        <input class="multi-button" type="button" value="{t}delete selected{/t}" onclick="$('#action').val('delete');$('#theForm').submit();" />
        <input class="multi-button" type="button" value="{t}download selected{/t}" onclick="$('#action').val('download');$('#theForm').submit();" />
    </p>
    <p>
	    <a href="upload.php">{t}upload files{/t}</a>
	</p>    
    </form>

</div>

{include file="../shared/admin-messages.tpl"}

<script>
$(document).ready(function()
{
	$('.all-select').on('click', function()
	{
		$('.select').prop('checked',$(this).prop('checked'));
	});

	$('.select').on('click', function()
	{
		var d=true;
		$('.select').each(function()
		{
			if (d) d=$(this).prop('checked');
		});

		$('.all-select').prop('checked',d);
	});

});
</script>

{include file="../shared/admin-footer.tpl"}

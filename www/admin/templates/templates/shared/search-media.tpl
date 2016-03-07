<style>
.bar {
    height: 18px;
    background: green;
}
tr td:first-child {
	text-align: right;
}
tr:last-child td {
	padding-top: 15px;
}
input[type=text], textarea {
	width: 300px;
}

</style>

<div id="page-main">

	<form id="searchForm" method="post">
	<input type="hidden" name="action" value="search" />
	<input type="hidden" name="rnd" value="{$rnd}" />
	<input type="hidden" name="module_id" value="{$module_id}" />
	<input type="hidden" name="item_id" value="{$item_id}" />

	<h3>{$name}</h3>
	<p><img src="{$source}" alt="{$name}" /></p>
	<table>
    {if $languages|@count>1}
    <tr>
    <td>{t}language{/t}:</td>
    <td>
    <select id="language_id" name="language_id" onchange="$('#action').val('language_change');$('#theForm').submit();">
        {foreach from=$languages item=v}
        <option value="{$v.language_id}"{if $v.language_id==$language_id} selected="selected"{/if}>{$v.language}</option>
        {/foreach}
	</select>
    {else}
    <input type="hidden" id="language_id" name="language_id" value="{$defaultLanguage}" />
    {/if}
    </td>
	</tr>

	<tr>
		<td>{t}file name{/t}:</td>
		<td><input type="text" name="file_name" value="{$file_name}" /></td>
	</tr>

	{foreach from=$metadata key=field item=value}
	<tr>
		<td>{t}{$field}{/t}:</td>
		<td><input type="text" name="{$field}" value="{$value}" /></td>
	</tr>
	{/foreach}

	<tr>
		<td>{t}tags{/t}:</td>
		<td><textarea name="tags" placeholder="{t}enter multiple tags separated by comma's{/t}" rows="3">{$tags}</textarea></td>
	</tr>
	<tr>
		<td></td>
		<td><input type="submit" name="save" value="{t}search{/t}" /></td>
	</tr>
	</table>
    </form>
</div>

{literal}
<script></script>
{/literal}
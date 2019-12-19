{include file="../shared/admin-header.tpl"}
{include file="../shared/admin-messages.tpl"}

<div id="page-main">
<form enctype="multipart/form-data" action="" method="post">
{if $imported}
<a href="contents.php">Contents</a>
{elseif $data}
<p>
The following pages will be imported:
<ul>
{foreach from=$data item=v key=k}
{if $has_titles && $k>0 || !$has_titles}
	<li><b>{$v[0]|@htmlentities}</b> {$v[1]|@strip_tags|truncate:75|@trim|@htmlentities}</li>
{/if}
{/foreach}
</ul>
</p>
<p>
    <input type="hidden" name="action" value="import" />
    <input type="submit" value="{t}import{/t}" />
</p>
<a href="?reset">Back</a>
{else}
CSV-file, comma-separated, optionally enclosed by double-quote, two columns, first column title, second column text (may include html). Indicate language and whether first row has titles.
<p>
    <input type="hidden" name="rnd" value="{$rnd}" />
    <input type="hidden" name="delimiter" value="comma" />
    {t}Choose a file to upload:{/t} <input name="uploadedfile" type="file" />
</p>
<p>
    This file is in: 
    {if $session.admin.project.languages|@count==1}
    <input name="language" type="hidden" value="{$session.admin.project.languages[0].language_id}" />{$session.admin.project.languages[0].language}
    {else}
    <select name="language">
    {foreach from=$session.admin.project.languages item=v}
    <option value="{$v.language_id}"{if $v.language_id==$session.admin.project.default_language_id} selected="selected"{/if}>{$v.language}</option>
    {/foreach}
    </select>
    {/if}
</p>
<p>
	<label><input type="checkbox" name="has_titles" value="1" />First row has titles</label><br />
</p>
<p>
    <input type="submit" value="{t}upload and parse{/t}" />
</p>
{/if}
</div>
</form>

{include file="../shared/admin-footer.tpl"}
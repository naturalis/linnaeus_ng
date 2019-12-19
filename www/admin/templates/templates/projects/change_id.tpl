{include file="../shared/admin-header.tpl"}
{include file="../shared/admin-messages.tpl"}

<div id="page-main">
<form method="post" name="theForm">
{if $projects && !$newId && !$done}
<p>
	{t}Select the project of which you wish to change the ID and enter the new ID.{/t}
</p>
<p>
    <table>
        <tr>
            <td>Project:</td>
            <td>
                <select name="p">
                    <option value=""></option>
                    {foreach from=$projects item=v}
                    <option value="{$v.id}"{if $oldId==$v.id} selected="selected"{/if}>{if $v.title!=''}{$v.title}{else}[untitled]{/if} ({$v.id})</option>
                    {/foreach}
                </select>
            </td>
        </tr>
        <tr>
            <td>New ID:</td>
            <td>
                <input type="text" name="newId" value="{$newId}" style="width:25px;text-align:right" />
            </td>
        </tr>
    </table>
</p>
<p>
    <input type="submit" value="{t}select{/t}" />
</p>
<p>
	<a href="../users/choose_project.php">{t}Back{/t}</a>
</p>
{elseif $newId}
<p>
    You are about to change the ID of the project "{$projects[0].title}" from {$projects[0].id} to {$newId}.<br />
    <span class="message-error">This change is irreversible.</span><br />
    Are you sure you wish to continue?
</p>
<p>
    <input type="hidden" name="p" value="{$projects[0].id}" />
    <input type="hidden" name="newId" value="{$newId}" />
    <input type="hidden" name="action" value="change" />
    <input type="submit" value="change ID" />
</p>
{else}
<p>
	<a href="../users/choose_project.php">Back</a>
</p>
{/if}
</form>
</div>
{include file="../shared/admin-footer.tpl"}

{include file="../shared/admin-header.tpl"}

<div id="page-main">
<p>
<h2>{$reference.label}</h2>
<h3>{$reference.author_or_verbatim}</h3>
</p>
<p>

<form method="post" id="theForm">
<input type="hidden" name="id" value="{$reference.id}">
<input type="hidden" name="action" id="action" value="save">
<input type="hidden" name="rnd" value="{$rnd}">
<table>
	<tr>
		<th>{t}language{/t}:</th>
		<td>
			<select id="language_id" name="language_id">
				{assign var=first value=true}
				<option value="" {if !$reference.language_id} selected="selected"{/if}>{t}unknown{/t}</option>
				{foreach from=$languages item=v key=k}
				{if $v.sort_criterium==0 && $first==true}
				<option value="" disabled="disabled">&nbsp;</option>
				{assign var=first value=false}
				{/if}
				{if $v.id!=$smarty.const.LANGUAGE_ID_SCIENTIFIC}
				<option value="{$v.id}" {if $v.id==$reference.language_id} selected="selected"{/if}>{$v.label}</option>
				{/if}
				{/foreach}
			</select>
		</td>
	</tr>
	<tr><th>{t}title{/t}:</th><td><input class="large" type="text" name="label" value="{$reference.label|@escape}" /></td></tr>

	{if $reference.alt_label}
	<tr>
    	<th>alt. label:</th>
        <td>
        	<input class="large" type="text" name="alt_label" value="{$reference.alt_label|@escape}" /><br />
            <span class="small-warning">
                De alternatieve titel is een overerving uit de oude Soortenregister-database. Dit veld wordt
                nergens gebruikt, en kan leeggemaakt worden.
            </span>
		</td>
	</tr>
	{/if}

	<tr>
		<th>{t}type of publication{/t}:</th>
		<td>
			<select id="publication_type_id" name="publication_type_id">
				<option value="" {if $reference.publication_type_id==''} selected="selected"{/if}>-</option>
			{foreach from=$publicationTypes item=v}
				<option value="{$v.id}" {if $v.id==$reference.publication_type_id} selected="selected"{/if}>{$v.label}</option>
			{/foreach}
			</select>
            <!-- verbatim: {$reference.publication_type} -->
		</td>
	</tr>

	<tr><th>{t}publisher{/t}:</th><td><input class="" type="text" name="publisher" value="{$reference.publisher}" /></td></tr>

	<tr>
    	<th>{t}link{/t}:</th>
        <td><input class="large" type="text" name="external_link" value="{$reference.external_link}" /></td>
	</tr>

	<tr>
    	<th><input type="button" value="save" onclick="saveLitForm();" /></th>
        <td></td>
	</tr>

{if $reference.id}
	<tr><td colspan="2" style="height:5px;"></td></tr>
	<tr><th><a href="#" onclick="doDelete('Are you sure you want to delete &quot;{$reference.label|replace:"'":"\'"}&quot;?\n{$links.presences|@count} statuses and {$links.names|@count} names are linked to this title.');return false;">{t}delete publication{/t}</a></th><td></td></tr>
{/if}

</table>

</form>
</p>

<p>
	<a href="index.php">{t}back{/t}</a>
</p>


</div>



{include file="../shared/admin-messages.tpl"}
{include file="../shared/admin-footer.tpl"}

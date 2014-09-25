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
		<th>taal:</th>
		<td>
			<select id="language_id" name="language_id">
				{assign var=first value=true}
				<option value="" {if !$reference.language_id} selected="selected"{/if}>onbekend</option>
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
	<tr><th>titel:</th><td><input class="large" type="text" name="label" value="{$reference.label|@escape}" /></td></tr>
	<tr><th>datum:</th><td><input class="small" type="text" name="date" value="{$reference.date}" /></td></tr>
	{*if $reference.author*}
	<tr><th>auteur (verbatim):</th><td><input class="large" type="text" name="author" value="{$reference.author}" /></td></tr>
	{*/if*}
	<tr>
		<th>auteur(s):</th>
		<td>
        	<span id="authors">
			{foreach from=$reference.authors item=author key=kk}
            	<span id="actor_id-{$kk}">
                <select name="actor_id[]">
                    <option value="" {if !$reference.actor_id} selected="selected"{/if}>-</option>
                {foreach from=$actors item=v key=k}
                {if $v.is_company=='0'}
                    <option value="{$v.id}" {if $v.id==$author.actor_id} selected="selected"{/if}>{$v.label}</option>
                {/if}
                {/foreach}
                </select><a class="edit" href="#" onclick="removeAuthorField({$kk});return false;">verwijderen</a>
                <br />
                </span>
			{/foreach}
            </span>
            <a class="edit" style="margin-left:0px;" href="#" onclick="addAuthorField();return false;">auteur toevoegen</a>
		</td>
	</tr>
	<tr>
		<th>type publicatie:</th>
		<td>
			<select id="publication_type" name="publication_type">
				<option value="" {if $reference.publication_type==''} selected="selected"{/if}>-</option>
			{foreach from=$publicationTypes item=v}
				<option value="{$v.publication_type}" {if $v.publication_type==$reference.publication_type} selected="selected"{/if}>{$v.publication_type}</option>
			{/foreach}
			</select> 
		</td>
	</tr>
	<tr><th>citatie:</th><td><input class="large" type="text" name="citation" value="{$reference.citation}" /></td></tr>
	<tr><th>bron:</th><td><input class="medium" type="text" name="source" value="{$reference.source}" /></td></tr>
	{if $reference.publishedin}
	<tr><th>gepubliceerd in (verbatim):</th><td><input class="large" type="text" name="publishedin" value="{$reference.publishedin}" /></td></tr>
	{/if}
	<tr>
		<th>gepubliceerd in:</th>
		<td>
			<span id="publishedin">{if $reference.publishedin_label}{$reference.publishedin_label}{else}-{/if}</span>
			<a class="edit" href="#" onclick="dropListDialog(this,'Publicatie');return false;" rel="publishedin_id">edit</a>
			<input type="hidden" id="publishedin_id" name="publishedin_id" value="{$reference.publishedin_id}" />
		</td>
	</tr>
	<tr><th>pagina(s):</th><td><input class="small" type="text" name="pages" value="{$reference.pages}" /></td></tr>
	<tr><th>volume:</th><td><input class="small" type="text" name="volume" value="{$reference.volume}" /></td></tr>
	{if $reference.periodical}
	<tr><th>periodiek (verbatim):</th><td><input type="text" name="periodical" value="{$reference.periodical}" /></td></tr>
	{/if}
	<tr>
		<th>periodiek:</th>
		<td>
				<span id="periodical">{if $reference.periodical_label}{$reference.periodical_label}{else}-{/if}</span>
				<a class="edit" href="#" onclick="dropListDialog(this,'Periodiek');return false;" rel="periodical_id">edit</a>
				<input type="hidden" id="periodical_id" name="periodical_id" value="{$reference.periodical_id}" />
		</td>
	</tr>
	<tr><th>link:</th><td><input class="large" type="text" name="external_link" value="{$reference.external_link}" /></td></tr>
	<tr><th><input type="submit" value="save" /></th><td></td></tr>

{if $reference.id}
	<tr><td colspan="2" style="height:5px;"></td></tr>
	<tr><th><a href="#" onclick="doDelete('Weet u zeker dat u &quot;{$reference.label|@escape}&quot; wilt verwijderen?\nEr zijn {$links.presences|@count} statussen en {$links.names|@count} namen aan deze titel gekoppeld.');return false;">referentie verwijderen</a></th><td></td></tr>
{/if}

</table>

</form>
</p>
<p>
<div>
	<b>Koppelingen</b><br />
	{if $links.presences|@count==0 && $links.names|@count==0}
	(geen koppelingen)
	{/if}
	{if $links.names|@count > 0}
	<a href="#" onclick="$('#links-names').toggle();return false;">Gekoppelde namen: {$links.names|@count}</a>
	<div id="links-names" style="display:none">
		<ul class="small">
			{foreach from=$links.names item=v}
			<li><a href="../nsr/taxon.php?id={$v.taxon_id}">{$v.name}{if $v.nametype=='isValidNameOf'} ({$v.nametype_label}){else} ({$v.nametype_label} van {$v.taxon}){/if}</a></li>
			{/foreach}
		</ul>
	</div>
	<br />
	{/if}
	{if $links.presences|@count > 0}
	<a href="#" onclick="$('#links-presences').toggle();return false;">Gekoppelde voorkomensstatussen: {$links.presences|@count}</a>
	<div id="links-presences" style="display:none">
		<ul class="small">
			{foreach from=$links.presences item=v}
			<li><a href="../nsr/taxon.php?id={$v.taxon_id}">{$v.taxon}</a>, {$v.presence_label}</li>
			{/foreach}
		</ul>
	</div>
	{/if}
</div>
</p>

<p>
	<a href="index.php">terug</a>
</p>


</div>

<script>
$(document).ready(function()
{
	{foreach from=$actors item=v key=k}
	{if $v.is_company!='1'}
	storeAuthor({ id: {$v.id},name:'{$v.label|@escape}'});
	{/if}
	{/foreach}
});
</script>

{include file="../shared/admin-messages.tpl"}
{include file="../shared/admin-footer.tpl"}

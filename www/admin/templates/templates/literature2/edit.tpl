{include file="../shared/admin-header.tpl"}

<script type="text/JavaScript">

var new_taxa=Array();

function add_taxon()
{
	var new_id=$('#taxon_id').val();
	var new_label=$('#taxon').val();

	for (var i=0;i<new_taxa.length;i++)
	{
		if (new_taxa[i].id==new_id) return;
	}

	new_taxa.push( { id:new_id, label:new_label } )
}

function remove_taxon( id )
{
	for (var i=0;i<new_taxa.length;i++)
	{
		if (new_taxa[i].id==id)
		{
			new_taxa.splice(i,1);
			return;
		}
	}
}

function print_taxa()
{
	$('#new_taxa').html('');
	for (var i=0;i<new_taxa.length;i++)
	{
		$('#new_taxa').append(
			'<li>' + new_taxa[i].label + '<a href="#" onclick="remove_taxon('+new_taxa[i].id+');print_taxa();return false;" style="padding:0 5px 0 5px"> x </a></li>' );
	}
}

function saveLitForm()
{
	var form=$('#theForm');

	for (var i=0;i<new_taxa.length;i++)
	{
		form.append('<input type=hidden name=new_taxa[] value="'+new_taxa[i].id+'" />');
	}

	form.submit();
}

</script>

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




	<tr><th>{t}date{/t}:</th><td><input class="small" type="text" name="date" value="{$reference.date}" /></td></tr>
	{*if $reference.author*}
	<tr><th title="{t}Field for full string of author(s) provided on the publication itself, including additions such as (eds.). Use separate author fields to link literature reference to persons in the Actors module. If both verbatim and separate author fields have been filled for a single reference, the value in the verbatim field will show on the front-end, but the reference will be found in searches on both values.{/t}">{t}author (verbatim){/t}:</th><td><input class="large" type="text" name="author" value="{$reference.author|@escape}" /></td></tr>
	{*/if*}
	<tr>
		<th>{t}author(s){/t}:</th>
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
                </select><a class="edit" href="#" onclick="removeAuthorField({$kk});return false;">{t}delete{/t}</a>
                <br />
                </span>
			{/foreach}
            </span>
            <a class="edit" style="margin-left:0px;" href="#" onclick="addAuthorField();return false;">{t}add author{/t}</a>
		</td>
	</tr>
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
	{* <tr><th>{t}citation{/t}:</th><td><input class="large" type="text" name="citation" value="{$reference.citation|@escape}" /></td></tr> *}
	{* <tr><th>{t}source{/t}:</th><td><input class="medium" type="text" name="source" value="{$reference.source|@escape}" /></td></tr> *}

	<tr>
		<th title="{t}use this field for parts/chapters of books and sections of websites.{/t}">{t}published in{/t}:</th>
		<td>
			<span id="publishedin">{if $reference.publishedin_label}{$reference.publishedin_label}{else}-{/if}</span>

			<a class="edit" href="#" onclick="dropListDialog(this,'{t}Publication{/t}', { publication_type:[{$gepubliceerd_in_ids|@implode:','}] });return false;" rel="publishedin_id">edit</a>
			<input type="hidden" id="publishedin_id" name="publishedin_id" value="{$reference.publishedin_id}" />
		</td>
	</tr>

	<tr><th>{t}publisher{/t}:</th><td><input class="" type="text" name="publisher" value="{$reference.publisher}" /></td></tr>

	{if $reference.publishedin}
	<tr>
    	<th>{t}published in (verbatim):{/t}</th>
        <td>
        	<input class="large" type="text" name="publishedin" value="{$reference.publishedin}" /><br />
            <span class="small-warning">
            	This field is a leftover from the original Soortenbank database.<br />
            	If possible, please use the "published in" field instead.
            </span>
        </td>
	</tr>
	{/if}

	<tr>
		<th title="{t}use this field for the journal or periodical in which the reference was published.{/t}">{t}periodical{/t}:</th>
		<td>
            <span id="periodical">{if $reference.periodical_label}{$reference.periodical_label}{else}-{/if}</span>
            <a class="edit" href="#" onclick="dropListDialog(this,'{t}Periodical{/t}', { publication_type:[{$periodiek_ids|@implode:','}] });return false;"
                rel="periodical_id">edit</a>
            <input type="hidden" id="periodical_id" name="periodical_id" value="{$reference.periodical_id}" />
		</td>
	</tr>
	{if $reference.periodical}
	<tr>
    	<th>{t}periodical (verbatim):{/t}</th>
        <td>
        	<input type="text" name="periodical" value="{$reference.periodical}" /><br />
            <span class="small-warning">
            	This field is a leftover from the original Soortenbank database.<br />
            	If possible, please use the "periodical" field instead.
            </span>
		</td>
	</tr>
	{/if}


	<tr>
    	<th title="{t}volume, with issue number between brackets, e.g. 53(1){/t}">{t}volume:{/t}</th>
        <td><input class="small" type="text" name="volume" value="{$reference.volume}" /></td>
	</tr>
	<tr>
    	<th>{t}pages(s){/t}:</th>
        <td><input class="small" type="text" name="pages" value="{$reference.pages}" /></td>
	</tr>
	<tr>
    	<th>{t}link{/t}:</th>
        <td><input class="large" type="text" name="external_link" value="{$reference.external_link}" /></td>
	</tr>

	{if $reference.id}
	<tr style="height: 15px;"><th></th><td></td></tr>
	<tr>
		<th>{t}preview{/t}:</th>
		<td style="max-width: 450px;">{$reference.formatted}</td>
	</tr>
	{/if}

	<tr style="height: 15px;"><th></th><td></td></tr>
	<tr>
    	<th>
        	{t}link to taxa{/t}:
        </th>
    	<td>
            <a class="edit" style="margin-left:0" href="#" onclick="dropListDialog(this,'{t}Taxon{/t}', { closeDialogAfterSelect: false } );return false;" rel="taxon_id">{t}add{/t}</a>
            <input type="hidden" id="taxon_id" value="" onchange="add_taxon();print_taxa();" />
            <input type="hidden" id="taxon" value="" />
            <ul id="new_taxa">
            </ul>
		</td>
	</tr>

	<tr>
    	<th><input type="button" value="save" onclick="saveLitForm();" /></th>
        <td></td>
	</tr>

{if $reference.id}
	<tr><td colspan="2" style="height:5px;"></td></tr>
	<tr><th><a href="#" onclick="doDelete('Are you sure you want to delete &quot;{$reference.label|replace:"'":"\'"}&quot;?\n{$links.presences|@count} statuses and {$links.names|@count} names are linked to this title.');return false;">{t}delete reference{/t}</a></th><td></td></tr>
{/if}

</table>

</form>
</p>
<p>
<div>
	<b>{t}Links{/t}</b><br />

	{if $links.presences|@count==0 && $links.names|@count==0 && $links.traits|@count==0 && $links.passports|@count==0 && $links.taxa|@count==0}
	({t}no links{/t})
	{/if}

	{if $links.names|@count > 0}
    <div>
	<a href="#" onclick="$('#links-names').toggle();return false;">{t}Linked names{/t} ({$links.names|@count})</a>
	<div id="links-names" style="display:none">
		<ul class="small">
			{foreach from=$links.names item=v}
			<li><a href="../nsr/taxon.php?id={$v.taxon_id}">{$v.name}{if $v.nametype=='isValidNameOf'} ({$v.nametype_label}){else} ({$v.nametype_label} of {$v.taxon}){/if}</a></li>
			{/foreach}
		</ul>
	</div>
    </div>
	{/if}

	{if $links.presences|@count > 0}
    <div>
	<a href="#" onclick="$('#links-presences').toggle();return false;">{t}Linked presence statuses{/t} ({$links.presences|@count})</a>
	<div id="links-presences" style="display:none">
		<ul class="small">
			{foreach from=$links.presences item=v}
			<li><a href="../nsr/taxon.php?id={$v.taxon_id}">{$v.taxon}</a>, {$v.presence_label}</li>
			{/foreach}
		</ul>
	</div>
    </div>
	{/if}

	{if $links.passports|@count > 0}
    <div>
	<a href="#" onclick="$('#links-passports').toggle();return false;">{t}Linked passports{/t} ({$links.passports|@count})</a>
	<div id="links-passports" style="display:none">
		<ul class="small">
			{foreach from=$links.passports key=k item=v}{if $v.taxon_id!=$links.passports[$k-1].taxon_id}
            {if $k>0}</li>{/if}
			<li><a href="../nsr/taxon.php?id={$v.taxon_id}">{$v.taxon}</a>: {/if}{if $v.taxon_id==$links.passports[$k-1].taxon_id}, {/if}{$v.title}{/foreach}</li>
		</ul>
	</div>
   	</div>
	{/if}

	{if $links.taxa|@count > 0}
    <div>
	<a href="#" onclick="$('#links-taxa').toggle();return false;">{t}Linked taxa{/t} ({$links.taxa|@count})</a>
	<div id="links-taxa" style="display:none">
		<ul class="small">
			{foreach from=$links.taxa item=v}
			<li><a href="../nsr/literature.php?id={$v.id}">{$v.taxon} [{$v.rank}]</a></li>
			{/foreach}
		</ul>
	</div>
    </div>
	{/if}

	{if $links.traits|@count > 0}
    <div>
	<a href="#" onclick="$('#links-traits').toggle();return false;">{t}Linked traits{/t} (<span id="trait-total">0</span>)</a>
	<div id="links-traits" style="display:none">
    	<ul>
            {foreach from=$links.traits item=vv key=trait}
        	<li>
            	<a href="#" onclick="$('#links-traits-{$vv.id}').toggle();return false;">{$trait} ({$vv|@count})</a>
                <ul class="small" id="links-traits-{$vv.id}" style="display:none">
                    {foreach from=$vv item=v}
                    <li><a href="../nsr/taxon.php?id={$v.taxon_id}">{$v.taxon}</a></li>
                    {/foreach}
                </ul>
			</li>
            <script>
			$( '#trait-total' ).html( parseInt($( '#trait-total' ).html())+{$vv|@count} );
            </script>
            {/foreach}
		</ul>
	</div>
    </div>
	{/if}

</div>
</p>
<p>
	<a href="index.php">{t}back{/t}</a>
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

	$('th[title]').each(function(key,value)
	{
		$(this).html('<span class="tooltip">'+$(this).html()+'</span>');
	});
});
</script>

{include file="../shared/admin-messages.tpl"}
{include file="../shared/admin-footer.tpl"}

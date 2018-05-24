{assign names $content}

<h2 id="name-header"><b>{t}Naamgeving{/t}</b></h2>

<table id="names-table">
	{foreach from=$names.list item=v}
		{if $v.expert.name}{assign var=expert value=$v.expert.name}{/if}
		{if $v.organisation.name}{assign var=organisation value=$v.organisation.name}{/if}
		{if $v.nametype=='isValidNameOf' && $taxon.base_rank_id<$smarty.const.SPECIES_RANK_ID}
			<tr><td>{$v.nametype_label|@ucfirst}</td><td><b>{$v.name}</b></td></tr>
		{else}
			{if $v.language_id==$smarty.const.LANGUAGE_ID_SCIENTIFIC && $v.nametype!='isValidNameOf'}
				{assign var=another_name value="`$v.uninomial` `$v.specific_epithet` `$v.infra_specific_epithet`"}
				{if $another_name!='' && $v.uninomial!=''}
					<tr><td>{$v.nametype_label|@ucfirst}</td><td><a href="name.php?id={$v.id}"><i>{$another_name}</i> {$v.authorship}</a></td></tr>
				{elseif $v.uninomial==''}
					{assign var=another_name value=$v.name|@replace:$v.authorship:''}
					<tr><td>{$v.nametype_label|@ucfirst}</td><td><a href="name.php?id={$v.id}"><i>{$another_name}</i> {$v.authorship}</a></td></tr>
				{else}
					<tr><td>{$v.nametype_label|@ucfirst}</td><td><a href="name.php?id={$v.id}">{$v.name}</a></td></tr>
				{/if}
			{else}
				<tr>
					<td>
            {if $v.nametype=='isAlternativeNameOf' && $names.language_has_preferredname[$v.language_id]!=true && $v.alt_alt_nametype_label}
              {$v.alt_alt_nametype_label|@ucfirst}
            {else}
	            {$v.nametype_label|@ucfirst}
            {/if}
          </td>
          <td>
          	<a href="name.php?id={$v.id}">{$v.name}</a>
            {if $v.addition[$currentLanguageId].addition}({$v.addition[$currentLanguageId].addition}){/if}
	        </td>
        </tr>
			{/if}
		{/if}
	{/foreach}
	{if $expert || $organisation}
		{if $expert}
		<tr><td>Expert</td><td colspan="2">{$expert}{if $organisation} ({$organisation}){/if}</td></tr>
		{else}
		<tr><td>Organisatie</td><td colspan="2">{$organisation}</td></tr>
		{/if}
	{/if}
</table>

{if $classification}
	<p>
		<h2>{t}Indeling{/t}</h2>
		<ul class="taxonoverzicht">
			<li class="root">
			{foreach from=$classification item=v key=x}
			{if $v.parent_id!=null}{* skipping top most level "life" *}
				<span class="classification-preffered-name"><a href="nsr_taxon.php?id={$v.id}">
				{if $v.uninomial == ''}
					{$v.taxon}
				{else}
					<span class='italics'>{$v.name}</span> {$v.authorship}
				{/if}
				</a>&nbsp;<span class="classification-rank">[{$v.rank_label}]</span></span>
				{if $v.common_name}
				<span class="classification-accepted-name">{$v.common_name}</span>{/if}
				{if $x < $classification|@count - 1}
				<ul class="taxonoverzicht">
					<li>
				{/if}
			{/if}
			{/foreach}
			{foreach from=$classification item=v key=x}
			{if $v.parent_id!=null}{* skipping top most level "life" *}
			</li></ul>
			{/if}
			{/foreach}
			</li>				
		</ul>
	</p>
{/if}

<script type="text/JavaScript">
$(document).ready(function()
{

	{if $taxon.nsr_id!=''}
	$('#name-header').on( 'click' , function(event) { 
	
		if ($('#nsr-id-row').html()==undefined)
		{
			if (event.altKey!==true) return;
			$('#names-table tr:last').after('<tr id="nsr-id-row"><td>NSR ID</td><td>{$taxon.nsr_id}</td></tr>');
		}
		else
		{
			$('#nsr-id-row').toggle();
		}
	});
	{/if}

} );
</script>

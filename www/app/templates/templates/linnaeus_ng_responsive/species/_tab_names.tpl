<h2 id="name-header">{t}Naamgeving{/t}</h2>

<table id="names-table">

	{foreach from=$names.list item=v}
        {if $v.expert.name}{assign var=expert value=$v.expert.name}{/if}
        {if $v.organisation.name}{assign var=organisation value=$v.organisation.name}{/if}
	    <tr>
            <td>

            {if $v.nametype=='isAlternativeNameOf' && $names.language_has_preferredname[$v.language_id]!=true && $v.alt_alt_nametype_label}
                {$v.alt_alt_nametype_label|@ucfirst}
            {else}
                {$v.nametype_label|@ucfirst}
            {/if}

            </td>
            <td>
    
            {if $v.nametype=='isPreferredNameOf' || $v.nametype=='isAlternativeNameOf'}
	            <a href="name.php?id={$v.id}">{$v.name}</a>
			{else if $v.nametype=='isValidNameOf'}
            	{if $taxon.base_rank_id<$smarty.const.SPECIES_RANK_ID}
		            <b>{$v.name}</b>
				{else}
					<a href="name.php?id={$v.id}">{$v.name}</a>
                {/if}
            {else}
                {if $v.nomen!=""}
                    <a href="name.php?id={$v.id}"><i>{$v.nomen}</i> {$v.authorship}</a>
                {else if $v.uninomial|cat:$v.specific_epithet|cat:$v.infra_specific_epithet!=""}
                    <a href="name.php?id={$v.id}"><i>{$v.uninomial} {$v.specific_epithet} {$v.infra_specific_epithet}</i> {$v.authorship}</a>
                {else}
                    <a href="name.php?id={$v.id}">{$v.name}</a>
                {/if}
            {/if}
            
            {if $v.addition[$currentLanguageId].addition}({$v.addition[$currentLanguageId].addition}){/if}

			</td>
		</tr>        
	{/foreach}
    
	{if $expert || $organisation}
    <tr>
        {if $expert}
            <td>Expert</td><td colspan="2">{$expert}{if $organisation} ({$organisation}){/if}</td>
        {else}
            <td>Organisatie</td><td colspan="2">{$organisation}</td>
        {/if}
	{/if}
	</tr>

</table>

<p>
    <h2>{t}Indeling{/t}</h2>

    <ul class="taxonoverzicht">

        <li class="root">

        {foreach from=$classification item=v key=x}
        {if $v.parent_id!=null}{* skipping top most level "life" *}
            <span class="classification-preffered-name"><a href="nsr_taxon.php?id={$v.id}">{$v.taxon}</a>&nbsp;<span class="classification-rank">[{$v.rank_label}]</span></span>
            {if $v.common_name}
            <span class="classification-accepted-name">{$v.common_name}</span>{/if}
            <ul class="taxonoverzicht">
                <li>
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

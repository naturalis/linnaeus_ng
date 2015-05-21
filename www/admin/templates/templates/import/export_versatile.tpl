{include file="../shared/admin-header.tpl"}

<style>
fieldset {
	margin-bottom:20px;
}

div.fieldsubset {
	border-bottom:1px solid #ddd;
	padding-bottom:5px;
}
.selected_ranks {
	cursor:default;
}
.remark {
	font-size:0.85em;
	color:#444;
}
</style>

<script>

var lastop='ge';

function addEstablishedOrNot(state)
{
	$( '.presence_labels' ).each(function(index, element)
	{
		$(this).prop('checked',false);
		if ($(this).attr('data-established')===state)
		{
			$(this).prop('checked',true);
		}
    });
}

function addRank( rank )
{
	if ( rank )
	{
		$( '#selected_ranks li' ).each(function()
		{
			if (rank.id==$(this).data('id'))
				return;
		});
		$( '#selected_ranks' ).append( '<li class=selected_ranks data-id='+rank.id+'>'+rank.label+'</li>' )
	}
	else
	{
		var candidate;
		
		$( '.ranks' ).each(function(index, element)
		{
			if ($(this).prop('selected'))
			{
				candidate = { id:$(this).attr('id'),label:$(this).text(),add:true };
	
				$( '#selected_ranks li' ).each(function()
				{
					if (candidate.id==$(this).data('id'))
						candidate.add=false;
				});
	
				if (candidate.add)
					$( '#selected_ranks' ).append( '<li class=selected_ranks data-id='+candidate.id+'>'+candidate.label+'</li>' )
				
			}
		});
	}

	$( '.selected_ranks' ).on( 'dblclick' , function(index, element) { $(this).remove();checkRanksOp(); });

}

function checkRanksOp()
{
	if ( $( '#selected_ranks li' ).size() > 1 )
	{
		$( '.rank_operator[value=in]' ).prop( 'checked', true );
	}
	else
	if ( $( '#selected_ranks li' ).size() == 1 && $( '.rank_operator[value=in]' ).prop( 'checked' ))
	{
		if (lastop != 'in' )
			$( '.rank_operator[value='+lastop+']' ).prop( 'checked', true );
		else
			$( '.rank_operator[value=eq]' ).prop( 'checked', true );
	}

	lastop=$( '.rank_operator:checked' ).val();
}

function doSubmit()
{
	var m=[];

	if ( $( '#parent_taxon_id' ).val().length==0 )
	{
		m.push( 'selecteer een taxon.' );
	}
	if ( $( '#selected_ranks li' ).size()<1 )
	{
		m.push( 'selecteer tenminmste één rang.' );
	}
	if ( $( 'input[type=checkbox][name^=cols]:checked' ).length<1)
	{
		m.push( 'selecteer tenminmste één kolom.' );
	}
	
	if (m.length>0)
	{
		alert( m.join("\n") );
		return;
	}
	
	if ($( '#output_target_screen' ).prop('checked'))
	{
		$( '#theForm' ).attr('target','export_output');
	}
	else
	{
		$( '#theForm' ).attr('target','_self');
	}

	$( '.to_be_posted' ).remove();
	
	$( '#theForm' ).append( '<input type="hidden" class="to_be_posted" name="branch_top_label" value="'+$( '#parent_taxon' ).text()+'" />' );

	$( '#selected_ranks li' ).each(function()
	{
		$( '#theForm' ).append( '<input type="hidden" class="to_be_posted" name="selected_ranks[]" value="'+$(this).data('id')+'" />' );
	});

	$( '#theForm' ).submit();
}
		
</script>

<div id="page-main">

	<form id="theForm" method="post" target="_self">

    <input type="hidden" name="action" value="export"  />

    <fieldset>

		<legend>Selectiecriteria</legend>

        <div class="fieldsubset">
            <h4>Top van de te exporteren tak</h4>
            <span id="parent_taxon">-</span>
                <a class="edit" style="margin-left:0" href="#" onclick="dropListDialog(this,'Branch top');return false;" rel="parent_taxon_id">
                    kiezen
                </a>
                <input type="hidden" id="parent_taxon_id" value="" name="branch_top_id" mandatory="mandatory"  label="ouder" droplistminlength="3" />
        </div>
    
        <div class="fieldsubset">
            <h4>Voorkomensstatus</h4>
            <a onclick="addEstablishedOrNot('1');return false;" href="#">gevestigde soorten</a> /
            <a onclick="addEstablishedOrNot('0');return false;" href="#">niet gevestigde soorten</a> / 
            <a onclick="addEstablishedOrNot('2');return false;" href="#">niet filteren op voorkomensstatus</a>
            <table>
            {foreach $presence_labels v}
                <tr>
                    <td>
                        <input 
                            type=checkbox 
                            class=presence_labels 
                            name=presence_labels[]
                            id="presence-{$v.index_label}" 
                            value="{$v.index_label}"
                            data-established="{$v.established}"
                            >
                    </td>
                    <td style="text-align:right">
                        <label for="presence-{$v.index_label}">{$v.index_label}.</label></td>
                    <td>
                        <label for="presence-{$v.index_label}">{$v.label}</label></td>
                </tr>
            {/foreach}
            </table>
        </div>

        <h4>Taxonomische rangen</h4>
        <table>
            <tr>
                <td>
                	<input type="checkbox" name="all_ranks" />Taxa van alle rangen tonen
                </td>
			</tr>
<tbody style="background-color:red">              
            <tr>
                <td>
                    Beschikbaar:<br />
                    <span class=remark>(dubbelklik of klik op pijl om toe te voegen)</span>
                </td>
                <td>
                </td>
                <td style="vertical-align:top;">
                    Alleen taxa tonen met de volgende rang:<br />
                    <span class=remark>(dubbelklik om te verwijderen)</span>
                </td>
            </tr>
            <tr>
                <td>
                    <select size="10" multiple="multiple" style="width:200px">
                    {foreach $ranks v}
                        <option 
                            id={$v.id} 
                            class=ranks
                            ondblclick="addRank();checkRanksOp();"
                            {if $v.id==$smarty.const.SPECIES_RANK_ID} selected="selected"{/if}>{$v.rank}</option>
                    {/foreach}
                    </select>
                </td>
                <td>
                    <input type=button value="&#10140;" onclick="addRank();checkRanksOp();" />
                </td>
                <td style="vertical-align:top;">
                    <ul id=selected_ranks style="border:1px solid #ddd;width:200px;padding-left:5px;">
                    </ul>
                    <div style="font-size:0.9em">
                    Hoe toe te passen:<br />
                    <label>
                        <input type=radio class=rank_operator onchange=checkRanksOp() name=rank_operator value=eq />
                        alleen deze rang
                    </label> 
                    <label>
                        <input type=radio class=rank_operator onchange=checkRanksOp() name=rank_operator value=ge checked="checked" />
                        deze rang en lager
                    </label><br />
                    <label>
                        <input type=radio class=rank_operator onchange=checkRanksOp() name=rank_operator value=in />
                        deze rangen
                    </label>
                    </div>            
                </td>
            </tr>
</tbody>            
        </table>

	</fieldset>
    
	<fieldset>

		<legend>Te exporteren gegevens</legend>

        <div class="fieldsubset">
			<h4>Standaardkolommen</h4>
            <table>
                <tr>
                    <td><input id=col_sci_name type=checkbox name=cols[sci_name] checked="checked" /></td>
                    <td><label for=col_sci_name>wetenschappelijke naam</label></td>
                </tr>
                <tr>
                    <td><input id=col_dutch_name type=checkbox name=cols[dutch_name] checked="checked" /></td>
                    <td><label for=col_dutch_name>nederlandse naam</label></td>
                </tr>
                <tr>
                    <td><input id=col_rank type=checkbox name=cols[rank] checked="checked" /></td>
                    <td><label for=col_rank>rang</label></td>
                </tr>
                <tr>
                    <td><input id=col_presence_status type=checkbox name=cols[presence_status] checked="checked" /></td>
                    <td><label for=col_presence_status>voorkomensstatus</label></td>
                </tr>
                <tr>
                    <td><input id=col_nsr_id type=checkbox name=cols[nsr_id] checked="checked" /></td>
                    <td><label for=col_nsr_id>NSR ID</label></td>
                </tr>
            </table>
        </div>

        <div class="fieldsubset">
			<h4>Extra kolommen</h4>
            <table>
                <tr>
                    <td><input id=col_habitat type=checkbox name=cols[habitat] /></td>
                    <td><label for=col_habitat>habitat</label></td>
                </tr>
                <tr>
                    <td><input id=col_concept_url type=checkbox name=cols[concept_url]  /></td>
                    <td><label for=col_concept_url>URL naar NSR-pagina concept</label></td>
                </tr>
                <tr>
                    <td><input id=col_nameparts type=checkbox name=cols[name_parts] onclick="
                        $( '.namepart' ).prop( 'disabled' , !$(this).prop( 'checked' ) ).toggle( $(this).prop( 'checked' ) ) 
                    " /></td>
                    <td><label for=col_nameparts>losse naamdelen<span class=remark> (indien beschikbaar!)</span></label>
                    <div class=namepart style="display:none">
                    <label><input class=namepart disabled=disabled type=checkbox name=name_parts[uninomial] checked=checked>uninomial</label><br />
                    <label><input class=namepart disabled=disabled type=checkbox name=name_parts[specific_epithet] checked=checked>specific epithet</label><br />
                    <label><input class=namepart disabled=disabled type=checkbox name=name_parts[infra_specific_epithet] checked=checked>infra specific epithet</label><br />
                    <label><input class=namepart disabled=disabled type=checkbox name=name_parts[authorship] checked=checked>authorship</label><br />
                    <label><input class=namepart disabled=disabled type=checkbox name=name_parts[authorship_author]>authorship author</label><br />
                    <label><input class=namepart disabled=disabled type=checkbox name=name_parts[authorship_year]>authorship year</label><br />
                    <span class=remark>(geldt ook voor synoniemen, als die ook geëxporteerd worden)</span>
                    </div>
                    </td>
                </tr>
    
                <tr>
                    <td><input id=col_ancestry type=checkbox name=cols[ancestors] onclick="
                        $( '.ancestry' ).prop( 'disabled' , !$(this).prop( 'checked' ) ).toggle( $(this).prop( 'checked' ) ) 
                    " /></td>
                    <td><label for=col_ancestry>taxonomische ouders<span class=remark> (worden, indien van toepassing, opgenomen als extra cellen aan het eind van iedere regel)</span></label>
                    <div class=ancestry style="display:none">
                    <label><input class=ancestry disabled=disabled type=checkbox name=ancestors[rijk] checked=checked value="{$smarty.const.KINGDOM_RANK_ID}" />rijk</label><br />
                    <label><input class=ancestry disabled=disabled type=checkbox name=ancestors[phylum] checked=checked value="{$smarty.const.PHYLUM_RANK_ID}" />phylum</label><br />
                    <label><input class=ancestry disabled=disabled type=checkbox name=ancestors[klasse] checked=checked value="{$smarty.const.CLASS_RANK_ID}" />klasse</label><br />
                    <label><input class=ancestry disabled=disabled type=checkbox name=ancestors[orde] checked=checked value="{$smarty.const.ORDO_RANK_ID}" />orde</label><br />
                    <label><input class=ancestry disabled=disabled type=checkbox name=ancestors[familie] checked=checked value="{$smarty.const.FAMILY_RANK_ID}" />familie</label><br />
                    <label><input class=ancestry disabled=disabled type=checkbox name=ancestors[genus] checked=checked value="{$smarty.const.GENUS_RANK_ID}" />genus</label><br />
                    <label><input class=ancestry disabled=disabled type=checkbox name=ancestors[species] checked=checked value="{$smarty.const.SPECIES_RANK_ID}" />species</label><br />
                    </div>
                    </td>
                </tr>
                <tr>
                    <td><input id=col_database_id type=checkbox name=cols[database_id]  /></td>
                    <td><label for=col_database_id>database ID</label></td>
                </tr>
                
            </table>
		</div>

		<h4>Synoniemen</h4>
        <table><tr>
		<td><input type=checkbox name=synonyms id=synonyms onclick="
        		$( '.nametypes' ).prop( 'disabled' , !$(this).prop( 'checked' ) ).toggle( $(this).prop( 'checked' ) );
             " />
		</td>
        <td>
        	<label for=synonyms>ook synoniemen van taxa exporteren</label>

            <div class=nametypes style="display:none">
            {foreach $nametypes v}
            {if $v.id!=$smarty.const.PREDICATE_VALID_NAME && $v.id!=$smarty.const.PREDICATE_PREFERRED_NAME}
            <label>
            	<input 
                	class=nametypes
                    disabled=disabled
                    type=checkbox
                    name=nametypes[{$v.id}]
                    {if $v.id!=$smarty.const.PREDICATE_INVALID_NAME && $v.id!=$smarty.const.PREDICATE_MISSPELLED_NAME}
                    checked=checked 
                    {/if}
                    value="{$v.id}" />{$v.nametype_hr}
            </label><br />
            {/if}
            {/foreach}
            </div>

		</td>
		</tr></table>
        <span class=remark>(synoniemen worden getoond in een eigen sectie, onder de reguliere export)</span>
        
	</fieldset>

    <fieldset>

		<legend>CSV- en bestandsinstellingen</legend>
        
        <table>
        	<tr><td colspan="2">
                doel:
                    <label><input type="radio" name="output_target" value="download"  checked="checked" />download</label>&nbsp;&nbsp;
                    <label><input id="output_target_screen" type="radio" name="output_target" value="screen" />scherm (opent in tab)</label>
			</td></tr>
        	<tr><td colspan="2">
                veldscheider:
                    <label><input type="radio" name="field_sep" value="tab" checked="checked"/>tab</label>&nbsp;&nbsp;
                    <label><input type="radio" name="field_sep" value="comma" />komma</label>
			</td></tr>
        	<tr><td colspan="2">
                regeleinde:
                    <label><input type="radio" name="new_line" value="CrLf"/>CrLf</label>&nbsp;&nbsp;
                    <label><input type="radio" name="new_line" value="Lf"  checked="checked" />Lf</label>&nbsp;&nbsp;
                    <label><input type="radio" name="new_line" value="Cr" />Cr</label>
			</td></tr>
        	<tr>
            	<td><input type="checkbox" name="no_quotes" id="no_quotes" /></td>
                <td><label for="no_quotes">geen dubbele quotes om waarden</label></td>
			</tr>
        	<!-- tr>
            	<td><input type="checkbox" name="utf8_to_utf16" id="utf8_to_utf16" /></td>
                <td><label for="utf8_to_utf16">UTF8 naar UTF16 converteren</label></td>
			</tr -->
        	<tr>
            	<td><input type="checkbox" name="add_utf8_BOM" id="add_utf8_BOM" checked="checked" /></td>
                <td><label for="add_utf8_BOM">UTF8-BOM toevoegen aan download-bestand</label></td>
			</tr>
        	<tr>
            	<td><input type="checkbox" name="replace_underscores_in_headers" id="replace_underscores_in_headers"/></td>
                <td><label for="replace_underscores_in_headers"><i>underscores</i> in kolom-headers vervangen door spaties</label></td>
			</tr>
        	<tr>
            	<td><input type="checkbox" name="print_query_parameters" id="print_query_parameters" checked="checked" /></td>
                <td><label for="print_query_parameters">query parameters afdrukken</label></td>
			</tr>
		</table>
        
        <span class=remark>
        	Met deze default-waarden wordt een CSV	gegenereerd die goed te openen is in Excel.<br />
            Open het te downloaden bestand niet direct in Excel, maar sla het eerst op en importeer het 
            vervolgend in een Excel-leeg sheet via 'Data' > 'From text'.
        </span>

	</fieldset>
    
    <input type="button" value="exporteren" onclick="doSubmit();" />
    
    </form>
    
</div>

<script>

$(document).ready(function()
{
	addRank( { id:{$smarty.const.SPECIES_RANK_ID}, label:'species' } 	);
	{if $branch_top}
	$( '#parent_taxon_id' ).val( {$branch_top.id} );
	$( '#parent_taxon' ).text( '{$branch_top.label|@escape}' );
	{/if}
});
</script>

{include file="../shared/admin-footer.tpl"}

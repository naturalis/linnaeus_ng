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
.preset-label {
	display:inline-block;
	width:175px;
	text-align:right;
	padding:0px;
}
.admin-warnings {
	margin:5px 0 5px 0;
}

.update-ready {
    color: red;
    font-weight: bold;
}

/* tree */
#dialog_tree {
	font-size:0.9em;
}
.child-count {
	display:none;
}
</style>

<script>



var lastop='ge';

function updateTraitIndex ()
{
    $('#update_link').html('Updating the index...');
    allShowLoadingDiv();

    $.ajax({
        url: "ajax_interface_index.php" ,
        type: "GET",
        data: ({
            'action': 'update'
        }),
        success: function (data) {
            allHideLoadingDiv();
            $('#update_message').css("display", "inline")
            $('#update_date').html(data);
            $('#update_link').html("Update ready!").addClass("update-ready");
        }
    });
}

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

function appendValues()
{
	// reset
	$( '.to_be_posted' ).remove();

	$( '#theForm' ).append( '<input type="hidden" class="to_be_posted" name="branch_top_label" value="'+$( '#parent_taxon' ).text()+'" />' );

	if( !$( 'input[name="all_ranks"]' ).prop( 'checked' ) )
	{
		$( '#selected_ranks li' ).each(function()
		{
			$( '#theForm' ).append( '<input type="hidden" class="to_be_posted" name="selected_ranks[]" value="'+$(this).data('id')+'" />' );
		});
	}
}

function setTraits()
{
	if ($('#all_traits').prop('checked')) {
		$('#trait-selector input[type=checkbox]').each(function() { 
			$(this).prop('checked', true); 
		});
	} else {
		$('#trait-selector input[type=checkbox]').each(function() { 
			$(this).prop('checked', false); 
		});
	}
	$('#trait-selector').toggle($(!this).prop('checked'));
}

function doSubmit()
{
	// check
	var m=[];

	if ( $( '#parent_taxon_id' ).val().length==0 )
	{
		m.push(_('select a taxon.'));
	}
	if ( $( '#selected_ranks li' ).length<1 )
	{
		m.push(_('select at least one rank.'));
	}
	if ( $( 'input[type=checkbox][name^=cols]:checked' ).length<1)
	{
		m.push(_('select at least one column.'));
	}

	if (m.length>0)
	{
		alert( m.join("\n") );
		return;
	}

	// aim
	if ($( '#output_target_screen' ).prop('checked'))
	{
		$( '#theForm' ).attr('target','_blank');
	}
	else
	{
		$( '#theForm' ).attr('target','_self');
	}

	// append
	appendValues();

	// submit
	$( '#theForm' ).submit();
}

</script>

<div id="page-main">

     <div id="trait-matrix" style="margin-bottom: 40px;">
        <p>The Multi-purpose export uses a pre-compiled index of the traits and their values to significantly speed up the export.
            <span id="update_message" style="display: {if $index_last_update}inline{else}none{/if};">
        This index was last updated on <span id="update_date">{$index_last_update}</span>.
        </span>
            If your export is intended to contain traits and traits have recently been added, modified or deleted,
            you should update the index by clicking the link below. Note that the update may take several minutes, so please be patient!
        </p>
        <p id="update_link">
            <a href="#" onclick=updateTraitIndex()>Update the trait index now</a>
        </p>
    </div>

    <form id="theForm" method="post" target="_self">

    <input type="hidden" name="action" value="export"  />

	{if $spoof_settings_warning}<div class="admin-warnings"><span class="message">{$spoof_settings_warning}</span></div>{/if}

    <fieldset>

		<legend>{t}Selection criteria{/t}</legend>

        <div class="fieldsubset">
            <h4>{t}Top of branch to be exported{/t}</h4>
            <span id="parent_taxon">-</span>
                <a class="edit" style="margin:0 2px 0 5px" href="#" onclick="dropListDialog(this,'{t}Top of branch to be exported{/t}');return false;" rel="parent_taxon_id">
                    {t}find{/t}
                </a>
                /
                <a class="edit" style="margin-left:0" href="#" onclick="treeDialog(this,'{t}Top of branch to be exported{/t}');return false;" rel="parent_taxon_id">
                    {t}browse{/t}
                </a>
                <input type="hidden" id="parent_taxon_id" value="" name="branch_top_id" mandatory="mandatory"  label="ouder" droplistminlength="3" />
        </div>

{if $is_nsr}
        <div class="fieldsubset">
            <h4>{t}Presence status{/t}</h4>
            <a onclick="addEstablishedOrNot('1');return false;" href="#">{t}established species{/t}</a> /
            <a onclick="addEstablishedOrNot('0');return false;" href="#">{t}non-established species{/t}</a> /
            <a onclick="addEstablishedOrNot('2');return false;" href="#">{t}do not filter on presence status{/t}</a>
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
{/if}

        <h4>{t}Taxonomic ranks{/t}</h4>
        <table>
            <tr>
                <td>
                	<label>
                    	<input
                        	onchange="$('.rank-selector').toggle(!$(this).prop('checked'));"
                            type="checkbox"
                            name="all_ranks" />{t}Show taxa of all ranks{/t}</label>
                </td>
			</tr>

			<tbody class="rank-selector">
                <tr>
                    <td>
                        {t}Available{/t}:<br />
                        <span class=remark>({t}double-click or click arrow to add{/t})</span>
                    </td>
                    <td>
                    </td>
                    <td style="vertical-align:top;">
                        {t}Display only taxa with the following rank{/t}:<br />
                        <span class=remark>({t}double-click to remove{/t})</span>
                        <span class=remark><a href="#"
                        	onclick="$( '.selected_ranks' ).each(function(index, element) {
                            $(this).remove();checkRanksOp(); });return false;">{t}remove all{/t}</a></span>
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
                                {if $v.id==$smarty.const.SPECIES_RANK_ID} selected="selected"{/if}>{$v.label}</option>
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
                        {t}How to use{/t}:<br />
                        <label>
                            <input type=radio class=rank_operator onchange=checkRanksOp() name=rank_operator value=eq />
                            {t}just this rank{/t}
                        </label>
                        <label>
                            <input type=radio class=rank_operator onchange=checkRanksOp() name=rank_operator value=ge checked="checked" />
                            {t}this rank and lower{/t}
                        </label><br />
                        <label>
                            <input type=radio class=rank_operator onchange=checkRanksOp() name=rank_operator value=in />
                            {t}these ranks{/t}
                        </label>
                        </div>
                    </td>
                </tr>
			</tbody>
        </table>

	</fieldset>

	<fieldset>

		<legend>{t}Data to export{/t}</legend>

        <div class="fieldsubset">
			<h4>{t}Standard columns{/t}</h4>
            <table>
                <tr>
                    <td><input class=col id=col_sci_name type=checkbox name=cols[sci_name] checked="checked" onclick="
                        $( '.hybrid_options' ).prop( 'disabled' , !$(this).prop( 'checked' ) ).toggle( $(this).prop( 'checked' ) )
                    "/></td>
                    <td><label for=col_sci_name>{t}scientific name{/t}</label>
                    <div class=hybrid_options style="">
                    <label><input class=hybrid_options id=add_hybrid_marker type=checkbox name=add_hybrid_marker checked="checked" /> {t}add × to hybrids & infixes (subsp., f., var.) to infraspecies{/t}</label><br />
                    </div>
                    </td>
                </tr>
                <tr>
                    <td><input class=col id=col_dutch_name type=checkbox name=cols[dutch_name] checked="checked" /></td>
                    <td><label for=col_dutch_name>{t}common name{/t}</label></td>
                </tr>
                <tr>
                    <td><input class=col id=col_rank type=checkbox name=cols[rank] checked="checked" /></td>
                    <td><label for=col_rank>{t}rank{/t}</label></td>
                </tr>
{if $is_nsr}
                <tr>
                    <td><input class=col id=col_presence_status type=checkbox name=cols[presence_status] checked="checked" /></td>
                    <td><label for=col_presence_status>{t}presence status{/t}</label></td>
                </tr>
                <tr>
                    <td><input class=col id=col_nsr_id type=checkbox name=cols[nsr_id] checked="checked" /></td>
                    <td><label for=col_nsr_id>NSR ID</label></td>
                </tr>
{/if}
            </table>
        </div>

        <div class="fieldsubset">
			<h4>{t}Extra columns{/t}</h4>
            <table>
{if $is_nsr}
                <tr>
                    <td><input id=col_habitat type=checkbox name=cols[habitat] /></td>
                    <td><label for=col_habitat>{t}habitat{/t}</label></td>
                </tr>
                <tr>
                    <td><input id=col_concept_url type=checkbox name=cols[concept_url]  /></td>
                    <td><label for=col_concept_url>{t}URL to NSR page concept{/t}</label></td>
                </tr>
{/if}
                <tr>
                    <td><input id=col_nameparts type=checkbox name=cols[name_parts] onclick="
                        $( '.namepart' ).prop( 'disabled' , !$(this).prop( 'checked' ) ).toggle( $(this).prop( 'checked' ) )
                    " /></td>
                    <td><label for=col_nameparts>{t}separate name elements{/t}<span class=remark> ({t}when present{/t}!)</span></label>
                    <div class=namepart style="display:none">
                    <label><input class=namepart disabled=disabled type=checkbox name=name_parts[uninomial] checked=checked> {t}uninomial{/t}</label><br />
                    <label><input class=namepart disabled=disabled type=checkbox name=name_parts[specific_epithet] checked=checked> {t}specific epithet{/t}</label><br />
                    <label><input class=namepart disabled=disabled type=checkbox name=name_parts[infra_specific_epithet] checked=checked> {t}infra specific epithet{/t}</label><br />
                    <label><input class=namepart disabled=disabled type=checkbox name=name_parts[authorship] checked=checked> {t}authorship{/t}</label><br />
                    <label><input class=namepart disabled=disabled type=checkbox name=name_parts[name_author]> {t}authorship author{/t}</label><br />
                    <label><input class=namepart disabled=disabled type=checkbox name=name_parts[authorship_year]> {t}authorship year{/t}</label><br />
                    <span class=remark>({t}also applies to synonyms if these are part of the export{/t})</span>
                    </div>
                    </td>
                </tr>

                <tr>
                    <td><input id=col_parent_taxon type=checkbox name=cols[parent_taxon]  /></td>
                    <td><label for=col_parent_taxon>{t}direct parent (name){/t}</label></td>
                </tr>
                <tr>
                    <td><input id=col_parent_taxon_nsr_id type=checkbox name=cols[parent_taxon_nsr_id]  /></td>
                    <td><label for=col_parent_taxon_nsr_id>{t}direct parent NSR ID{/t}</label></td>
                </tr>
                <tr>
                    <td><input id=col_database_id type=checkbox name=cols[database_id]  /></td>
                    <td><label for=col_database_id>{t}database ID{/t}</label></td>
                </tr>
                <tr>
                    <td><input id=col_presence_status_publication type=checkbox name=cols[presence_status_publication]  /></td>
                    <td><label for=col_presence_status_publication>{t}presence status publication{/t}</label></td>
                </tr>
                <tr>
                    <td><input id=col_ancestry type=checkbox name=cols[ancestors] onclick="
                        $( '.ancestry' ).prop( 'disabled' , !$(this).prop( 'checked' ) ).toggle( $(this).prop( 'checked' ) )
                    " /></td>
                    <td><label for=col_ancestry>{t}taxonomic parents{/t}</label>
                    <div class=ancestry style="display:none">
                    <label><input class=ancestry disabled=disabled type=checkbox name=ancestors[rijk] checked=checked value="{$smarty.const.REGNUM_RANK_ID}" /> {t}kingdom{/t}</label><br />
                    <label><input class=ancestry disabled=disabled type=checkbox name=ancestors[phylum] checked=checked value="{$smarty.const.PHYLUM_RANK_ID}" /> {t}phylum{/t}</label><br />
                    <label><input class=ancestry disabled=disabled type=checkbox name=ancestors[klasse] checked=checked value="{$smarty.const.CLASSIS_RANK_ID}" /> {t}class{/t}</label><br />
                    <label><input class=ancestry disabled=disabled type=checkbox name=ancestors[orde] checked=checked value="{$smarty.const.ORDO_RANK_ID}" /> {t}order{/t}</label><br />
                    <label><input class=ancestry disabled=disabled type=checkbox name=ancestors[familie] checked=checked value="{$smarty.const.FAMILIA_RANK_ID}" /> {t}family{/t}</label><br />
                    <label><input class=ancestry disabled=disabled type=checkbox name=ancestors[genus] checked=checked value="{$smarty.const.GENUS_RANK_ID}" /> {t}genus{/t}</label><br />
                    <label><input class=ancestry disabled=disabled type=checkbox name=ancestors[species] checked=checked value="{$smarty.const.SPECIES_RANK_ID}" /> {t}species{/t}</label><br />
                    <span class=remark> ({t}will be appended as extra cells if applicable{/t})</span>
                    </div>
                    </td>
                </tr>
            </table>
		</div>

		
		{if $traits}
		<div class="fieldsubset">
            <h4>{t}Traits{/t} (is the <a href="#top">index up-to-date</a>?)</h4>


            <table>
        <tbody id="trait-selector">
		{foreach $traits group}
        	<tr><td>
			<input id="traitgroup_{$group.id}" type="checkbox" onclick="
            	$('.traitgroup_{$group.id}').prop('disabled', !$(this).prop('checked')).toggle($(this).prop('checked'))
            " /></td>
 			<td><label for="traitgroup_{$group.id}">{$group.name}</label>
           	<div class="traitgroup_{$group.id}" style="display: none;">
		    {foreach $group.traits trait}
           		<input class="traitgroup_{$group.id}" checked="checked" disabled="disabled" id="trait_{$group.id}_{$trait.id}" type="checkbox" name="traits[{$group.id}][]" value="{$trait.id}">
        		<label for="trait_{$group.id}_{$trait.id}">{$trait.name}</label></br>
            {/foreach}
            </div>
			</td></tr>
        {/foreach}
        </tbody>
		</table>
		</div>
		{/if}

		<h4>{t}Synonyms{/t}</h4>
        <table><tr>
		<td><input type=checkbox name=synonyms id=synonyms onclick="
        		$( '.nametypes' ).prop( 'disabled' , !$(this).prop( 'checked' ) ).toggle( $(this).prop( 'checked' ) );
             " />
		</td>
        <td>
        	<label for=synonyms>{t}export synonyms{/t}</label>

            <div class=nametypes style="display:none">
            {foreach $nametypes v}

            {if $v.nametype!=$smarty.const.PREDICATE_VALID_NAME} {* && $v.nametype!=$smarty.const.PREDICATE_PREFERRED_NAME *}
            <label>
            	<input
                	class=nametypes
                    disabled=disabled
                    type=checkbox
                    name=nametypes[{$v.id}]
                    {if $v.nametype==$smarty.const.PREDICATE_SYNONYM || $v.nametype==$smarty.const.PREDICATE_SYNONYM_SL}
                    checked=checked
                    {/if}
                    value="{$v.id}" />{t}{$v.nametype_hr}{/t}
            </label><br />
            {/if}
            {/foreach}

             <span class=remark>({t}synonyms are included in a separate section, below the regular export{/t})</span>

            </div>

		</td>
		</tr></table>

	</fieldset>

    <script>

		$('.col').on('change',function()
		{
			$( '.' + $(this).attr('id').replace( 'col_','ord_') ).prop( 'disabled' , !$(this).prop('checked') );
		});

	</script>

    <fieldset>

		<legend>{t}Sorting{/t}</legend>

        <table>
        	<tr><td colspan="2">
                <label><input class="ord_rank ord_sci_name" type="radio" name="order_by" value="rank-sci_name" checked="checked" />
                	{t}rank{/t} &#9656; {t}scientific name{/t}</label><br />
                <label><input class="ord_rank ord_dutch_name" type="radio" name="order_by" value="rank-dutch_name" />
                	{t}rank{/t} &#9656; {t}common name{/t}</label><br />
                <label><input class="ord_sci_name" type="radio" name="order_by" value="sci_name" />
                	{t}scientific name{/t}</label><br />
                <label><input class="ord_dutch_name" type="radio" name="order_by" value="dutch_name" />
                	{t}common name{/t}</label><br />
                <label><input class="ord_presence_status ord_sci_name" type="radio" name="order_by" value="presence_status-sci_name" />
                	{t}presence status{/t} &#9656; {t}scientific name{/t}</label><br />
                <label><input class="ord_presence_status ord_dutch_name" type="radio" name="order_by" value="presence_status-dutch_name" />
                	{t}presence status{/t} &#9656; {t}common name{/t}</label><br />
			</td></tr>
		</table>

	</fieldset>

    <fieldset>

		<legend>CSV and file settings</legend>

        <table>
        	<tr><td colspan="2">
                {t}output{/t}:
                    <label><input type="radio" name="output_target" value="download"  checked="checked" />{t}download{/t}</label>&nbsp;&nbsp;
                    <label><input id="output_target_screen" type="radio" name="output_target" value="screen" />{t}screen (opens in tab){/t}</label>
			</td></tr>
        	<tr><td colspan="2">
                {t}separator{/t}:
                    <label><input type="radio" name="field_sep" value="tab" checked="checked"/>{t}tab{/t}</label>&nbsp;&nbsp;
                    <label><input type="radio" name="field_sep" value="comma" />{t}comma{/t}</label>
			</td></tr>
        	<tr><td colspan="2">
                {t}line ending{/t}:
                    <label><input type="radio" name="new_line" value="CrLf"/>CrLf</label>&nbsp;&nbsp;
                    <label><input type="radio" name="new_line" value="Lf"  checked="checked" />Lf</label>&nbsp;&nbsp;
                    <label><input type="radio" name="new_line" value="Cr" />Cr</label>
			</td></tr>
        	<tr>
            	<td><input type="checkbox" name="keep_tags" id="keep_tags" /></td>
                <td><label for="keep_tags">{t}preserve html tags in scientific names (for infixes){/t}</label></td>
			</tr>
        	<tr>
            	<td><input type="checkbox" name="no_quotes" id="no_quotes" /></td>
                <td><label for="no_quotes">{t}do not enclose values with double quotes{/t}</label></td>
			</tr>
        	<!-- tr>
            	<td><input type="checkbox" name="utf8_to_utf16" id="utf8_to_utf16" /></td>
                <td><label for="utf8_to_utf16">{t}convert UTF8 to UTF16{/t}</label></td>
			</tr -->
        	<tr>
            	<td><input type="checkbox" name="add_utf8_BOM" id="add_utf8_BOM" checked="checked" /></td>
                <td><label for="add_utf8_BOM">{t}add UTF8-BOM to file{/t}</label></td>
			</tr>
        	<tr>
            	<td><input type="checkbox" name="replace_underscores_in_headers" id="replace_underscores_in_headers"/></td>
                <td><label for="replace_underscores_in_headers">{t}replace underscores in headers with spaces{/t}</label></td>
			</tr>
        	<tr>
            	<td><input type="checkbox" name="print_query_parameters" id="print_query_parameters" checked="checked" /></td>
                <td><label for="print_query_parameters">{t}print query parameters{/t}</label></td>
			</tr>
        	<tr>
            	<td><input type="checkbox" name="print_eof_marker" id="print_eof_marker" /></td>
                <td><label for="print_eof_marker">{t}add end of file-marker (to check complete download){/t}</label></td>
			</tr>
		</table>

        <span class=remark>
        	{t}The default values generate a CSV file that can be opened in Excel.{/t}<br />
            {t}Do not open de file directly in Excel, but first save it and subsequently open it in an empty Excel sheet through 'Data' > 'From text'.{/t}
        </span>

	</fieldset>

    <p>

    	<input type="button" value="{t}export{/t}" onclick="doSubmit();" />

    </p>

	{if $spoof_settings_warning}<div class="admin-warnings"><span class="message">{$spoof_settings_warning}</span></div>{/if}

    </form>

</div>

<script>

$(document).ready(function()
{
	addRank( { id:{$smarty.const.SPECIES_RANK_ID}, label:'{$ranks[$smarty.const.SPECIES_RANK_ID].label}' } );
	{if $branch_top}
	$( '#parent_taxon_id' ).val( {$branch_top.id} );
	$( '#parent_taxon' ).text( '{$branch_top.label|@escape}' );
	{/if}
	setDropListCloseLabel('close');
});
</script>

{include file="../shared/admin-messages.tpl"}
{include file="../shared/admin-footer.tpl"}

{include file="../shared/header.tpl"}

<div id="dialogRidge">

	<div id="left">
	
	{include file="_toolbox.tpl"}

	</div>

    <form method="get" action="" id="formSearchFacetsSpecies" name="formSearchFacetsSpecies">

	<div id="content" class="simple-search">

		<div>

            <input type="hidden" id="group_id" name="group_id" value="{$search.group_id}" />
            {*<input type="hidden" id="author_id" name="author_id" value="{$search.author_id}" />*}

			<h1 style="color:#FA7001;font-size:30px;font-weight:normal;margin-top:4px;border-bottom:1px solid #666666;margin-bottom:5px;">
            	{if $search.header}{$search.header}{else}{t}Uitgebreid zoeken naar soorten{/t}{/if}
			</h1>
			
			<div{if $search.display=='plain'} style="display:none;"{/if}>
			<fieldset class="block">
				<div class="formrow">
					<label id="ext_search_soortgroep_label" style="display:inline-block;margin-left:13px;margin-top:2px" accesskey="g" for="search">{t}Soortgroep{/t}</label>
					<input style="width:370px" type="text" size="60" class="field" id="group" name="group" autocomplete="off" value="{$search.group}">
					<div id="group_suggestion" match="start" class="auto_complete" style="display:none;"></div>
				</div>
				{*<div class="formrow">
					<label accesskey="g" for="author">{t}Auteur{/t}</label>
					<input type="text" size="60" class="field" id="author" name="author" autocomplete="off" value="{$search.author}">
					<div id="author_suggestion" match="start" class="auto_complete" style="display:none;"></div>
				</div>*}
			</fieldset>
            
            <span id="all-panel-toggle" style="float:right;margin-top:3px;display:none;">
            <div style="float:right;margin-top:3px;">
                <a href="#" onclick="toggle_all_panels();return false;">{t}alles in-/uitklappen{/t}</a>&nbsp;&nbsp;
                <span onmouseout="hintHide()" onmouseover="hint(this,'&lt;p&gt;{t}Met dit zoekscherm maak je uiteenlopende selecties (onder)soorten. Verruim je selectie door meer dan 1 waarde binnen een kenmerk te selecteren (bijv. soorten met Status voorkomen 1a &lt;b&gt;of&lt;/b&gt; 1b). Vernauw je selectie door een waarde binnen een ander kenmerk te selecteren (bijv. soorten met Status voorkomen 1a &lt;b&gt;en&lt;/b&gt; met foto\'s). Druk op > om een kenmerkwaarde te selecteren.{/t}&lt;/p&gt;');" class="link">{t}hulp bij zoeken{/t}</span>
            </div>
            </span>

			<fieldset class="selectable-parameters" style="width:494px;margin-top:4px;">

				{if $automatic_tabs['CTAB_PRESENCE_STATUS'].suppress!==true}

				<div class="formrow">

	                <span class="panel-toggle-placeholder"></span>
    
                	<label
                    	for="presenceStatusList" 
                        panel="presence-options-panel"
                        class="clickable" 
                        onmouseover="hover_panel_toggle(this);"
                        onmouseout="hover_panel_toggle(this,true);"
                        onclick="toggle_panel(this);">
						<div class="arrow-container"><div class="arrow arrow-e"></div></div>
	                    <strong>{t}Status voorkomen{/t}</strong>
                    </label>&nbsp;
                    {if $search_presence_help_url}
                    <a href="{$search_presence_help_url}" target="_blank"  title="{t}klik voor help over dit onderdeel{/t}" class="help">&nbsp;</a>
                    {/if}
                    <p class="options-panel" id="presence-options-panel" style="display:none">
                        <select id="presenceStatusList" name="presenceStatusList" style="width:250px;margin-bottom:10px">
                            <option value="">{t}maak een keuze{/t}</option>
                            {foreach $presence_statuses v k}
                            <option 
                                id="established{$v.id}" 
                                value="presence[{$v.id}]" 
                                established="{$v.established}"
                                >
                                <div class="presenceStatusCode">{$v.index_label}</div>
                                <div class="presenceStatusDescription">{$v.information_short}</div>
                            </option>
                            {/foreach}
                        </select>
                        <input type="button" value=" > " onclick="addSearchParameter('presenceStatusList');" />
                        <br />
                        <a href="#" onclick="addEstablished();submitSearchParams();return false;">{t}gevestigde soorten{/t}</a> / 
                        <a href="#" onclick="addNonEstablished();submitSearchParams();return false;">{t}niet gevestigde soorten{/t}</a>
					</p>
				</div>

				{/if}

				{* if $automatic_tabs['CTAB_MEDIA'].suppress!==true *}

				<div class="formrow">
                
                	<span class="panel-toggle-placeholder"></span>

                	<label
                    	for="multimedia-options" 
                        panel="multimedia-options-panel"
                        class="clickable" 
                        onmouseover="hover_panel_toggle(this);"
                        onmouseout="hover_panel_toggle(this,true);"
                        onclick="toggle_panel(this);">
						<div class="arrow-container"><div class="arrow arrow-e"></div></div>
	                    <strong>{t}Multimedia{/t}</strong>
                    </label>
                    <table class="options-panel" id="multimedia-options-panel" style="display:none">
                    	<tr>
                        	<td class="traits-legend-cell"><label for="multimedia-images">{t}Foto('s){/t}</label></td>
                            <td>
                                <select id="multimedia-images" style="width:250px;">
                                    <option value="">{t}maak een keuze{/t}</option>
                                    <option value="images_on">{t}met foto('s){/t}</option>
                                    <option value="images_off">{t}zonder foto's{/t}</option>
                                </select>
                                <input type="button" value=" > " onclick="addSearchParameter('multimedia-images');" />
							</td>
						</tr>
                    	<tr>
                        	<td class="traits-legend-cell"><label for="multimedia-distribution">{t}Verspreidingskaart(en){/t}</label></td>
                            <td>
                                <select id="multimedia-distribution" style="width:250px;">
                                    <option value="">{t}maak een keuze{/t}</option>
                                    <option value="distribution_on">{t}met verspreidingskaart(en){/t}</option>
                                    <option value="distribution_off">{t}zonder verspreidingskaarten{/t}</option>
                                </select>
                                <input type="button" value=" > " onclick="addSearchParameter('multimedia-distribution');" />
							</td>
						</tr>
                    	<tr>
                        	<td class="traits-legend-cell"><label for="multimedia-trend">{t}Trendgrafiek{/t}</label></td>
                            <td>
                                <select id="multimedia-trend" style="width:250px;">
                                    <option value="">{t}maak een keuze{/t}</option>
                                    <option value="trend_on">{t}met trendgrafiek{/t}</option>
                                    <option value="trend_off">{t}zonder trendgrafiek{/t}</option>
                                </select>
                                <input type="button" value=" > " onclick="addSearchParameter('multimedia-trend');" />
							</td>
						</tr>
					</table>
				</div>

				{* /if *}
                
               
				{if $automatic_tabs['CTAB_DNA_BARCODES'].suppress!==true}
                        
				<div class="formrow">

	                <span class="panel-toggle-placeholder"></span>

                	<label 
                    	for="dna-options"
                        panel="dna-options-panel"
                        class="clickable" 
                        onmouseover="hover_panel_toggle(this);"
                        onmouseout="hover_panel_toggle(this,true);"
                        onclick="toggle_panel(this);">
						<div class="arrow-container"><div class="arrow arrow-e"></div></div>
	                    <strong>{t}DNA barcoding{/t}</strong>
                    </label>&nbsp;
                    <a href="http://www.nederlandsesoorten.nl/nlsr/nlsr/dnabarcoding.html" 
                        target="_blank" 
                        title="{t}klik voor help over dit onderdeel{/t}" 
                        class="help">&nbsp;</a>
                    <p class="options-panel" id="dna-options-panel" style="display:none">
                        <select id="dna-options" name="dna-options" style="width:250px;">
                            <option value="">{t}maak een keuze{/t}</option>
                            <option value="dna">{t}met een of meer exemplaren verzameld{/t}</option>
                            <option value="dna_insuff">{t}minder dan drie exemplaren verzameld{/t}</option>
                        </select>
                        <input type="button" value=" > " onclick="addSearchParameter('dna-options');" />
                    </p>
				</div>
                
                {/if}

				{foreach $traits t k1}
				<div class="formrow">
                
	                <span class="panel-toggle-placeholder"></span>
    
					<label
                        class="clickable" 
                        panel="traits{$k1}-options"
                        onmouseover="hover_panel_toggle(this);"
                        onmouseout="hover_panel_toggle(this,true);"
                        onclick="toggle_panel(this);">
						<div class="arrow-container"><div class="arrow arrow-e"></div></div>
						<strong>{$t.name}</strong>
					</label>&nbsp;
                    
                    {if $t.help_link_url}
					<a href="{$t.help_link_url}" target="_blank"  title="{t}klik voor help over dit onderdeel{/t}" class="help">&nbsp;</a>
                    {/if}
					
                    <table class="options-panel" id="traits{$k1}-options" style="display:none">
                    {if $t.description}
                    	<tr>
                        	<td colspan="2"><p>{$t.description}</p></td>
                        </tr>
                    {/if}
					{foreach $t.data d k2}
                    {if $d.type_sysname!=='stringfree'}
                    	<tr>
                        	<td class="traits-legend-cell"><label for="trait-{$k1}{$k2}">{$d.name}</label></td>
                            <td>
                                {if $d.type_allow_values==1 && $d.value_count>0}
                                <select trait-id="{$d.id}" id="trait-{$k1}{$k2}" style="width:250px;">
                                    <option value="">{t}maak een keuze{/t}</option>
	                                {foreach $d.values v k}
                                    <option value="{$v.id}">{$v.string_value}</option>
    	                            {/foreach}
                                </select>
                                {else if $d.type_allow_values==0}
                                
                                <select
                                	class="operator" 
                                    trait-id="{$d.id}" 
                                    id="operator-{$k1}{$k2}" 
                                    style="width:150px" 
                                    onchange="$('#trait-{$k1}{$k2}-2').toggle($('option:selected',this).attr('range')==1);"
								>
	                                {foreach $operators v k}
	                                <option value="{$k}"{if $v.range} range="1"{/if}>{t}{$v.label}{/t}</option>
    	                            {/foreach}
                                </select>
                                
                                <input
                                	type="text"
                                	id="trait-{$k1}{$k2}" 
                                    trait-id="{$d.id}"
                                    placeholder="{$d.date_format_format_hr}" 
                                    maxlength="{$d.date_format_format_hr|@strlen}" 
                                    style="width:45px;"
                                    />
                                <input
                                	id="trait-{$k1}{$k2}-2"
                                	type="text"
                                    trait-id="{$d.id}"
                                    second-value="1"
                                    placeholder="{$d.date_format_format_hr}" 
                                    maxlength="{$d.date_format_format_hr|@strlen}" 
                                    style="width:45px;display:none;"
                                    />
                                {/if}
                                <input
                                	type="button" 
                                    value=" > " 
                                    trait-id="{$d.id}" 
                                    class="add-trait" 
                                    onclick="addSearchParameter('trait-{$k1}{$k2}');" />
							</td>
						</tr>
					{/if}
					{/foreach}
                    	{if $t.show_show_all_link}
                    	<tr>
                        	<td colspan="2" style="padding-top:0.5em"><a href="#" onclick="setTraitGroup({$t.group_id});submitSearchParams();return;">
                            {if $t.all_link_text}{$t.all_link_text}{else}{t _s1=$t.name}Alle taxa met %s tonen{/t}{/if}
                            </a></td>
                        </tr>
                        {/if}
                    </table>
				</div>
				{/foreach}

				<div class="formrow selected-parameters" style="display:none">
					<strong>{t}Geselecteerde kenmerken{/t}</strong>
                    <span id="remove-all" style="display:none">&nbsp;
                    	<a href="#" onclick="removeAllSearchParameters();submitSearchParams();return;">{t}alles verwijderen{/t}</a>
                    	<!-- a href="nsr_search_extended.php">{t}alles verwijderen{/t}</a -->
					</span>
                    <ul id="search-parameters">
                    </ul>
				</div>
                
			</fieldset>

			</div>

		</div>

		<div id="results"> 
            <h4 style="display:inline-block">
            	<span id="resultcount-header">{$results.count}</span>
	            {* if $searchHR || $searchTraitsHR} {t}voor{/t} '{if $searchHR}{$searchHR}{/if}{if $searchTraitsHR}{$searchTraitsHR}{/if}'{/if *}
            </h4>
			<a href="#" id="just-species-toggle" style="padding-left:10px;" onclick="toggleJustSpeciesToggle();submitSearchParams();return false;">
			{t}alleen soorten tonen{/t}
            </a>

            <div class="formrow" style="margin-bottom:15px">
                {t}Resultaten sorteren op:{/t}
                <select name="sort" id="sort" onchange="submitSearchParams();">
                    <option value="name-valid"{if $search.sort!='name-valid'} selected="selected"{/if}>{t}Wetenschappelijk naam{/t}</option>
                    <option value="name-pref-nl"{if $search.sort=='name-pref-nl'} selected="selected"{/if}>{t}Nederlandse naam{/t}</option>
                </select>
            </div>

			{foreach $results.data v k}
            <div class="result">
                {if $v.overview_image}
                <img src="{$taxon_base_url_images_thumb_s}{$v.overview_image}" />
                {/if}
                <strong><a href="../species/nsr_taxon.php?id={$v.id}">{$v.taxon}</a></strong><br />

				{if $show_all_preferred_names_in_results}
					{foreach $v.common_names n nk}
                    {$n.name}
                    {if $nk<$v.common_names|@count}<br />{/if}
					{/foreach}
                {else}
					{if $v.common_name}{$v.common_name}<br />{/if}
				{/if}

                {if $show_presence_in_results}
                    {if $v.presence_information_index_label || $v.presence_information_title}
                    {t}Status voorkomen:{/t} {$v.presence_information_index_label} {$v.presence_information_title}
                    {/if}
                {/if}
            </div>
			{/foreach}
		</div>
        
 		</form>

        {if $search.just_species==1}
        {capture A}{t}soort{/t}{/capture}
        {capture B}{t}soorten{/t}{/capture}
        {else}
        {capture A}{t}soort (of lager taxon){/t}{/capture}
        {capture B}{t}soorten (en lagere taxa){/t}{/capture}
        {/if}

		{assign var=pgnEntityNames value=[$smarty.capture.A,$smarty.capture.B]}
		{assign var=pgnResultCount value=$results.count}
		{assign var=pgnResultsPerPage value=$results.perpage}
		{assign var=pgnCurrPage value=$search.page}
		{assign var=pgnURL value=$smarty.server.PHP_SELF}
		{assign var=pgnQuerystring value=$querystring}
		{include file="../shared/_paginator.tpl"}

	</div>

	{include file="../shared/_right_column.tpl"}

</div>


<script>
$(document).ready(function()
{
	$('.panel-toggle-placeholder').first().replaceWith($('#all-panel-toggle').html());
	
	{if $search}
	{foreach $search.presence v k}
	$("#presenceStatusList").val('presence[{$k}]');
	addSearchParameter('presenceStatusList');
	{/foreach}

	{foreach $search v k}
	{if $k=='images' || $k=='distribution' || $k=='trend'}
	$("#multimedia-options").val('{$k}');
	addSearchParameter('multimedia-options');
	{else if $k=='dna' || $k=='dna_insuff'}
	$("#dna-options").val('{$k}');
	addSearchParameter('dna-options');
	{else if $k=='images_on' || $k=='images_off'}
	$("#multimedia-images").val('{$k}');
	addSearchParameter('multimedia-images');
	{else if $k=='distribution_on' || $k=='distribution_off'}
	$("#multimedia-distribution").val('{$k}');
	addSearchParameter('multimedia-distribution');
	{else if $k=='trend_on' || $k=='trend_off'}
	$("#multimedia-trend").val('{$k}');
	addSearchParameter('multimedia-trend');
	{/if}
	{/foreach}
	
	{if $search.traits}

	var h=$.parseJSON(decodeURIComponent('{$search.traits}'));

	for (var i in h)
	{
		var d=h[i];
		
		if (d.valueid)
		{
			$('select[trait-id='+d.traitid+']').val(d.valueid);
		}
		else
		{
			$('select.operator[trait-id='+d.traitid+']').val(d.operator).trigger('change');
			$('input[type=text][trait-id='+d.traitid+']').val(d.value);

			if (d.value2)
			{
				$('input[trait-id='+d.traitid+'][second-value=1]').val(d.value2);
			}
			else
			{
				$('input[trait-id='+d.traitid+'][second-value=1]').val('');
			}

		}

		$('input.add-trait[trait-id='+d.traitid+']').trigger('click');
	}

	{if $search.trait_group!=''}
		setTraitGroup({$search.trait_group});
		setTraitGroupName('{$trait_group_name}');
		printParameters();
	{/if}


	{/if}
	{/if}

	{if $search.panels}

	var h=$.parseJSON(decodeURIComponent('{$search.panels}'));

	$.each(h, function(i,v)
	{
		if (v.visible)
		{
			$('label[panel='+v.id+']').trigger('click').trigger('mouseout');
		}
	});
	
	{else}
	
		$('label[for=presenceStatusList]').trigger('click').trigger('mouseout');

	{/if}
	
	{if $search.just_species}
	setJustSpeciesToggle({$search.just_species});
	{/if}
	
	$('#just-species-toggle').html(getJustSpeciesToggle()==0 ? '{t}alleen soorten tonen{/t}' : '{t}soorten en lagere taxa tonen{/t}' );

	$('title').html('{t}Uitgebreid zoeken naar soorten{/t} - '+$('title').html());

	bindKeys();

	$("#group, #author").keyup(function(e)
	{ 
		var code = e.which;
		if(code==13)
		{
			submitSearchParams();
		}
	});
	
	init=false;
	
});
</script>

{include file="../shared/footer.tpl"}
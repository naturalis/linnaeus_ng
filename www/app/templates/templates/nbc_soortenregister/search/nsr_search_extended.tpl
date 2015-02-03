{include file="../shared/header.tpl"}

<style>
.formrow select {
	font-size:1em;
	margin-left:0px;
	width:200px;
	margin-top:2px;
}
label.clickable {
	cursor:pointer;
}
label.clickable:hover {
	text-decoration:underline;
}
</style>


<div id="dialogRidge">

	<div id="left">
	
	{include file="_toolbox.tpl"}
	
	</div>

	<div id="content" class="simple-search">


		<div>

            <form method="get" action="" id="formSearchFacetsSpecies" name="formSearchFacetsSpecies">
            
            <input type="hidden" id="group_id" name="group_id" value="{$search.group_id}">
            <input type="hidden" id="author_id" name="author_id" value="{$search.author_id}">

			<h1 style="color:#FA7001;font-size:30px;font-weight:normal;margin-top:4px;border-bottom:1px solid #666666;margin-bottom:5px;">
            	{if $search.header}{$search.header}{else}Uitgebreid zoeken naar soorten{/if}
			</h1>
			

			<div{if $search.display=='plain'} style="display:none;"{/if}>
			<fieldset class="block">
				<div class="formrow">
					<label accesskey="g" for="search">Soortgroep</label>
					<input type="text" size="60" class="field" id="group" name="group" autocomplete="off" value="{$search.group}">
					<div id="group_suggestion" match="start" class="auto_complete" style="display:none;"></div>
				</div>
				<div class="formrow">
					<label accesskey="g" for="author">Auteur</label>
					<input type="text" size="60" class="field" id="author" name="author" autocomplete="off" value="{$search.author}">
					<div id="author_suggestion" match="start" class="auto_complete" style="display:none;"></div>
				</div>
			</fieldset>

			<fieldset>
				<div class="formrow">
					<label>
						<strong>Status voorkomen</strong>
						&nbsp;<a href="http://www.nederlandsesoorten.nl/node/15" target="_blank" title="klik voor help over dit onderdeel" class="help">&nbsp;</a>
					</label>
					<span style="float:right">
						<a id="togglePresenceStatusGevestigd" href="#">gevestigde soorten</a> / 
						<a id="togglePresenceStatusNietGevestigd" href="#">niet gevestigde soorten</a>
					</span>
					<select id="presenceStatusList" name="presenceStatusList">
					{foreach from=$presence_statuses item=v}
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
                    
                    <input type="button" value=" >" />
				</div>
				
				<div class="formrow">
                	<label class="clickable" onclick="$(this).next().toggle();">
	                    <strong>Afbeeldingen</strong>
                    </label>
                    <p style="display:none">
					<select id="presenceStatusList" name="presenceStatusList">
                        <option value="images">met foto('s)</option>
                        <option value="distribution">met verspreidingskaart</option>
                        <option value="trend">met trendgrafiek</option>
					</select>
                    
                    <input type="button" value=" >" />

                    </p>
				</div>

				<div class="formrow">
                	<label class="clickable" onclick="$(this).next().toggle();">
	                    <strong>DNA barcoding</strong>
						&nbsp;<a href="http://www.nederlandsesoorten.nl/nlsr/nlsr/dnabarcoding.html" target="_blank" title="klik voor help over dit onderdeel" class="help">&nbsp;</a>
                    </label>
                    <p style="display:none">
					<select id="presenceStatusList" name="presenceStatusList">
                        <option value="dna">met exemplaren verzameld</option>
                        <option value="dna_insuff">minder dan drie exemplaren verzameld</option>
					</select>
                    
                    <input type="button" value=" >" />
                    </p>
				</div>

	
				{foreach from=$traits item=t}
				<div class="formrow">
					<label class="clickable" onclick="$(this).next().toggle();">
						<strong>{$t.name}</strong>
					</label>
                    <table style="display:none">
					{foreach from=$t.data item=d}
                    {if $d.type_sysname!=='stringfree'}
                    	<tr>
                        	<td>{$d.name}</td>
                            <td>
                                {if $d.type_allow_values==1 && $d.value_count>0}
                                <select id="presenceStatusList" name="presenceStatusList">
                                {foreach from=$d.values item=v}
                                    <option value="">{$v.string_value}</option>
                                {/foreach}
                                </select>
		                        {/if}
                                {if $d.type_allow_values==0}
                                <input type="text" placeholder="{$d.date_format_format_hr}" />
                                {/if}
                                <input type="button" value=" >" />
							</td>
						</tr>
					{/if}
					{/foreach}
                    </table>




                    
                    
				</div>
				{/foreach}
                
                
                
		



				<div class="formrow" style="margin-top:10px;">
					<strong>Geselecteerde zoekparameters:</strong>
                    <ul id="parameters">
                    </ul>
				</div>



				<div class="formrow">
					<strong>Resultaten sorteren op:</strong>
                    <select name="sort">
                        <option value="name-valid"{if $search.sort!='name-valid'} selected="selected"{/if}>Wetenschappelijk naam</option>
                        <option value="name-pref-nl"{if $search.sort=='name-pref-nl'} selected="selected"{/if}>Nederlandse naam</option>
                    </select>
				</div>

				<input type="submit" class="zoekknop" value="zoek">
			</fieldset>
			</div>
		</form>
		</div>



		<div id="results">
			<p>
				<h4><span id="resultcount-header">{$results.count}</span>{if $searchHR} voor '{$searchHR}'{/if}</h4>
			</p>
			{foreach from=$results.data item=v}
				<div class="result">
					{if $v.overview_image}
					<img src="http://images.naturalis.nl/120x75/{$v.overview_image}" />
					{/if}
					<strong><a href="../species/nsr_taxon.php?id={$v.id}">{$v.taxon}</a></strong><br />
					{if $v.common_name}{$v.common_name}<br />{/if}
					Status voorkomen: {$v.presence_information_index_label} {$v.presence_information_title}
				</div>
			{/foreach}
		</div>

		{assign var=pgnResultCount value=$results.count}
		{assign var=pgnResultsPerPage value=$results.perpage}
		{assign var=pgnCurrPage value=$search.page}
		{assign var=pgnURL value=$smarty.server.PHP_SELF}
		{assign var=pgnQuerystring value=$querystring}
		{include file="../shared/_paginator.tpl"}
				
	</div>

	{include file="../shared/_right_column.tpl"}

</div>


{*

{foreach from=$presence_statuses item=v}
{if $search.presence[$v.id]=='on'}{/if}
{/foreach}
<input type="checkbox" class="list" id="images" name="images"{if $search.images=='on'} checked="checked"{/if}>
<input type="checkbox" class="list" id="distribution" name="distribution"{if $search.distribution=='on'} checked="checked"{/if}>
<input type="checkbox" class="list" id="trend" name="trend"{if $search.trend=='on'} checked="checked"{/if}>
<input type="checkbox" class="list" id="dna" name="dna" {if $search.dna=='on'} checked="checked"{/if}>
<input type="checkbox" class="list" id="dna_insuff" name="dna_insuff" {if $search.dna_insuff=='on'} checked="checked"{/if}>
*}




	
    
{literal}
<script type="text/JavaScript">
$(document).ready(function(){

$('title').html('Uitgebreid zoeken naar soorten - '+$('title').html());
	
$('#togglePresenceStatusGevestigd').bind('click',function() {
	$('input:checkbox[established]').each(function() {
		$(this).prop('checked', ($(this).attr('established')=='1'));
	});
	$('#formSearchFacetsSpecies').submit();
})

$('#togglePresenceStatusNietGevestigd').bind('click',function() {
	$('input:checkbox[established]').each(function() {
		$(this).prop('checked', ($(this).attr('established')=='0'));
	});
	$('#formSearchFacetsSpecies').submit();
})


bindKeys();

	
});
</script>
{/literal}

{include file="../shared/footer.tpl"}
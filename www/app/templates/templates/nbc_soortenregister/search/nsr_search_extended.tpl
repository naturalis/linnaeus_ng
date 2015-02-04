{include file="../shared/header.tpl"}

<script>

var search_parameters=[];

function addSearchParameter(id)
{
	if (!id) return;
	
	var ele=$('#'+id);
	var tagtype=ele.prop('tagName');
	var varlabel=$('label[for='+id+']').text().trim();	
	var istrait=ele.attr('id').indexOf('trait-')===0;

	if (tagtype=='SELECT')
	{
		var valueid=$('#'+id+' :selected').val();
		var value='on';
		var valuetext=$('#'+id+' :selected').text().trim();
	}
	else
	if (tagtype=='INPUT')
	{
		var valueid=ele.attr('value_id');
		var value=ele.val();
		var valuetext=value;
	}
	
	for(var i=0;i<search_parameters.length;i++)
	{
		var e=search_parameters[i];
		if (e.valueid==valueid && e.value==value && e.istrait==istrait)
		{
			return;
		}
	}
		
	search_parameters.push( { valueid:valueid,value:value,valuetext:valuetext,varlabel:varlabel,istrait:istrait } );
	printParameters();
}

function printParameters()
{
	$('#search-parameters').empty();
	for(var i=0;i<search_parameters.length;i++)
	{
		var e=search_parameters[i];
		$('#search-parameters').append($('<li>'+e.varlabel+': '+e.valuetext+' <a href="#" onclick="removeSearchParameter('+i+');return false;"> X </a></li>'));
	}
}

function removeSearchParameter(i)
{
	search_parameters.splice(i,1);
	printParameters();
}

function addEstablished()
{
	addEstablishedOrNot('1');
	printParameters();
}

function addNonEstablished()
{
	addEstablishedOrNot('0');
	printParameters();
}

function addEstablishedOrNot(state)
{
	var varlabel=$('label[for=presenceStatusList]').text().trim();
	
	$( "#presenceStatusList option" ).each(function()
	{
		var valueid=$(this).val().trim();
		for(var i=0;i<search_parameters.length;i++)
		{
			if (search_parameters[i].valueid==valueid)
			{
				removeSearchParameter(i);
			}
		}
	
		if ($(this).attr('established')==state)
		{
			search_parameters.push( { valueid:valueid,value:'on',valuetext:$(this).text().trim(),varlabel:varlabel,istrait:false } );
		}
	});	
}

function submitSearchParams()
{
	var form=$('<form method="get"></form>').appendTo('body');

	form.append('<input type="hidden" name="group_id" value="'+$('#group_id').val()+'" />');
	form.append('<input type="hidden" name="author_id" value="'+$('#author_id').val()+'" />');
	form.append('<input type="hidden" name="group" value="'+$('#group').val()+'" />');
	form.append('<input type="hidden" name="author" value="'+$('#author').val()+'" />');
	form.append('<input type="hidden" name="sort" value="'+$('#sort').val()+'" />');

	for (var i=0;i<search_parameters.length;i++)
	{
		var param=search_parameters[i];

		if (param.istrait)
		{
			form.append('<input type="hidden" name="traits['+param.valueid+']" value="'+param.value+'" />');
		}
		else
		{
			form.append('<input type="hidden" name="'+param.valueid+'" value="'+param.value+'" />');
		}
	}
	
	form.submit();	
}


</script>

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
    
    <div id=fuck>
    </div>
	
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
					<label for="presenceStatusList">
						<strong>Status voorkomen</strong>
						&nbsp;<a href="http://www.nederlandsesoorten.nl/node/15" target="_blank" title="klik voor help over dit onderdeel" class="help">&nbsp;</a>
					</label>
					<span style="float:right">
						<a href="#" onclick="addEstablished();return false;">gevestigde soorten</a> / 
						<a href="#" onclick="addNonEstablished();return false;">niet gevestigde soorten</a>
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
                    
                    <input type="button" value=" > " onclick="addSearchParameter('presenceStatusList');" />
				</div>
				
				<div class="formrow">
                	<label for="photoOptions" class="clickable" onclick="$(this).next().toggle();">
	                    <strong>Afbeeldingen</strong>
                    </label>
                    <p style="display:none">
					<select id="photoOptions" name="photoOptions">
                        <option value="images">met foto('s)</option>
                        <option value="distribution">met verspreidingskaart</option>
                        <option value="trend">met trendgrafiek</option>
					</select>
                    
                    <input type="button" value=" > " onclick="addSearchParameter('photoOptions');" />

                    </p>
				</div>

				<div class="formrow">
                	<label for="dnaOptions" class="clickable" onclick="$(this).next().toggle();">
	                    <strong>DNA barcoding</strong>
						&nbsp;<a href="http://www.nederlandsesoorten.nl/nlsr/nlsr/dnabarcoding.html" target="_blank" title="klik voor help over dit onderdeel" class="help">&nbsp;</a>
                    </label>
                    <p style="display:none">
					<select id="dnaOptions" name="dnaOptions">
                        <option value="dna">met exemplaren verzameld</option>
                        <option value="dna_insuff">minder dan drie exemplaren verzameld</option>
					</select>
                    
                    <input type="button" value=" > " onclick="addSearchParameter('dnaOptions');" />
                    </p>
				</div>
	
				{foreach from=$traits item=t key=k1}
				<div class="formrow">
					<label class="clickable" onclick="$(this).next().toggle();">
						<strong>{$t.name}</strong>
					</label>
                    <table style="display:none">
					{foreach from=$t.data item=d key=k2}
                    {if $d.type_sysname!=='stringfree'}
                    	<tr>
                        	<td><label for="trait-{$k1}{$k2}">{$d.name}</label></td>
                            <td>
                                {if $d.type_allow_values==1 && $d.value_count>0}
                                <select id="trait-{$k1}{$k2}">
                                {foreach from=$d.values item=v}
                                    <option value="{$v.id}">{$v.string_value}</option>
                                {/foreach}
                                </select>
                                {else if $d.type_allow_values==0}
                                <input id="trait-{$k1}{$k2}" value_id="{$v.id}" placeholder="{$d.date_format_format_hr}" maxlength="{$d.date_format_format_hr|@strlen}" />
                                {/if}
                                <input type="button" value=" > " onclick="addSearchParameter('trait-{$k1}{$k2}');" />
							</td>
						</tr>
					{/if}
					{/foreach}
                    </table>
				</div>
				{/foreach}
                

				<div class="formrow" style="margin-top:10px;">
					<strong>Geselecteerde zoekparameters:</strong>
                    <ul id="search-parameters">
                    </ul>
				</div>



				<div class="formrow">
					<strong>Resultaten sorteren op:</strong>
                    <select name="sort" id="sort">
                        <option value="name-valid"{if $search.sort!='name-valid'} selected="selected"{/if}>Wetenschappelijk naam</option>
                        <option value="name-pref-nl"{if $search.sort=='name-pref-nl'} selected="selected"{/if}>Nederlandse naam</option>
                    </select>
				</div>

				<input type="button=" class="zoekknop" value="zoek" onclick="submitSearchParams()" />
                
			</fieldset>
			</div>
		</form>
		</div>

<script>

{foreach from=$search item=v}
{/


$('option').each(function () {

	if ($(this).val()=='presence[15]')
	{
		$("#presenceStatusList").val($(this).val());
		addSearchParameter('presenceStatusList');

	}

});

</script>



{*

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
        
*}
{*
		{assign var=pgnResultCount value=$results.count}
		{assign var=pgnResultsPerPage value=$results.perpage}
		{assign var=pgnCurrPage value=$search.page}
		{assign var=pgnURL value=$smarty.server.PHP_SELF}
		{assign var=pgnQuerystring value=$querystring}
		{include file="../shared/_paginator.tpl"}
*}				
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


<script type="text/JavaScript">
$(document).ready(function()
{
	

	
	
	$('title').html('Uitgebreid zoeken naar soorten - '+$('title').html());
	bindKeys();
});
</script>

{include file="../shared/footer.tpl"}
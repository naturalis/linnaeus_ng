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
.zoekknop {
	cursor:pointer;
}
.tumble-arrow-head:before {
    content:"▶";
} 
.tumble-arrow-head:hover:before {
    content:"◢ ";
} 
.down-arrow-head:before {
    content:"🔻";
} 
.traits-legend-cell {
	width:150px;
}	
</style>
                
<script>
var search_parameters=[];
var init=true;

function addSearchParameter(id)
{
	if (!id) return;
	
	var ele=$('#'+id);
	var tagtype=ele.prop('tagName');
	var varlabel=$('label[for='+id+']').text().trim();	
	var istrait=ele.attr('id').indexOf('trait-')===0;

	var value2=null;
	var valuetext2=null;
	var operator=null;
	var operatorlabel=null;

	if (tagtype=='SELECT')
	{
		var traitid=ele.attr('trait-id');
		var valueid=$('#'+id+' :selected').val();
		var value='on';
		var valuetext=$('#'+id+' :selected').text().trim();
	}
	else
	if (tagtype=='INPUT')
	{
		var traitid=ele.attr('trait-id');
		var valueid=null;
		var value=ele.val();
		var valuetext=value;

		var ele2=$('#'+id+'-2');
		if (ele2.is(':visible'))
		{
			value2=ele2.val();
			valuetext2=value2;
		}

		var d=$(':selected','#operator-'+id.replace('trait-','')).val();
		if (d)
		{
			operator=d;
			operatorlabel=$(':selected','#operator-'+id.replace('trait-','')).text();
		}
		
	}

	if (((!valueid || valueid.length==0) || value.length==0) && (!value2 || value2.length==0))
	{
		return;
	}
	
	for(var i=0;i<search_parameters.length;i++)
	{
		var e=search_parameters[i];
		if (e.valueid==valueid && e.value==value && e.value2==value2 && e.operator==operator && e.istrait==istrait)
		{
			return;
		}
	}
	
	search_parameters.push(
	{ 
		traitid:traitid,
		valueid:valueid,
		value:value,
		valuetext:valuetext,
		varlabel:varlabel,
		istrait:istrait,
		operator:operator,
		operatorlabel:operatorlabel,
		value2:value2,
		valuetext2:valuetext2
	} );
	
	//console.dir(search_parameters);
	
	printParameters();

	submitSearchParams();
}

function printParameters()
{
	$('#search-parameters').empty();

	for(var i=0;i<search_parameters.length;i++)
	{
		var e=search_parameters[i];
		$('#search-parameters').
			append(
				$(
					'<li>'+
						e.varlabel+': '+
						(e.operatorlabel ? e.operatorlabel+' ' : '' )+
						e.valuetext+
						(e.valuetext2 ? ' & ' + e.valuetext2 : '' )+
					' <a href="#" onclick="removeSearchParameter('+i+');submitSearchParams();return false;"> X </a></li>'));
	}

	$('#remove-all').toggle(search_parameters.length>0);
}

function removeSearchParameter(i)
{
	search_parameters.splice(i,1);
	printParameters();
}

function removeAllSearchParameters()
{
	search_parameters.splice(0);
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
	if (init) return;

/*

	$('.options-panel').each(function(){
		$(this).is(':visible');
		remember and re-set.

	});

*/
	
	var form=$('<form method="get"></form>').appendTo('body');

	form.append('<input type="hidden" name="group_id" value="'+$('#group_id').val()+'" />');
	form.append('<input type="hidden" name="author_id" value="'+$('#author_id').val()+'" />');
	form.append('<input type="hidden" name="group" value="'+$('#group').val()+'" />');
	form.append('<input type="hidden" name="author" value="'+$('#author').val()+'" />');
	form.append('<input type="hidden" name="sort" value="'+$('#sort').val()+'" />');

	var traits={};
	var j=0;

	for (var i=0;i<search_parameters.length;i++)
	{
		var param=search_parameters[i];

		if (param.istrait)
		{
			traits[j++]=param;
		}
		else
		{
			form.append('<input type="hidden" name="'+param.valueid+'" value="'+param.value+'" />');
		}
	}
	
	form.append('<input type="hidden" name="traits" value="'+ encodeURIComponent(JSON.stringify(traits))+'" />');
	
	form.submit();	
}
</script>

<div id="dialogRidge">

	<div id="left">
	
	{include file="_toolbox.tpl"}

	</div>

	<div id="content" class="simple-search">

		<div>

            <form method="get" action="" id="formSearchFacetsSpecies" name="formSearchFacetsSpecies">
            <input type="hidden" id="group_id" name="group_id" value="{$search.group_id}" />
            <input type="hidden" id="author_id" name="author_id" value="{$search.author_id}" />

			<h1 style="color:#FA7001;font-size:30px;font-weight:normal;margin-top:4px;border-bottom:1px solid #666666;margin-bottom:5px;">
            	{if $search.header}{$search.header}{else}{t}Uitgebreid zoeken naar soorten{/t}{/if}
			</h1>
			
			<div{if $search.display=='plain'} style="display:none;"{/if}>
			<fieldset class="block">
				<div class="formrow">
					<label accesskey="g" for="search">{t}Soortgroep{/t}</label>
					<input type="text" size="60" class="field" id="group" name="group" autocomplete="off" value="{$search.group}">
					<div id="group_suggestion" match="start" class="auto_complete" style="display:none;"></div>
				</div>
				<div class="formrow">
					<label accesskey="g" for="author">{t}Auteur{/t}</label>
					<input type="text" size="60" class="field" id="author" name="author" autocomplete="off" value="{$search.author}">
					<div id="author_suggestion" match="start" class="auto_complete" style="display:none;"></div>
				</div>
			</fieldset>

			<fieldset>
				<div class="formrow">
					<label for="presenceStatusList">
						<strong>{t}Status voorkomen{/t}</strong>&nbsp;
                    <a href="http://www.nederlandsesoorten.nl/node/15"
                        target="_blank" 
                        title="{t}klik voor help over dit onderdeel{/t}" 
                        class="help"
                     >&nbsp;</a>
					</label>
                    <br />
					<select id="presenceStatusList" name="presenceStatusList" style="width:250px;color:#666">
                        <option value="">maak een keuze</option>
                        <optgroup style="color:#000">
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
                    	</optgroup>
					</select>
					<input type="button" value=" > " onclick="addSearchParameter('presenceStatusList');" />
					<br />
                    <a href="#" onclick="addEstablished();submitSearchParams();return false;">{t}gevestigde soorten{/t}</a> / 
                    <a href="#" onclick="addNonEstablished();submitSearchParams();return false;">{t}niet gevestigde soorten{/t}</a>
				</div>

				<div class="formrow">
                	<label for="photoOptions" 
                        class="clickable tumble-arrow-head" 
                        onclick="
                        	$('#multimedia-options').toggle();
                            if ($('#multimedia-options').is(':visible'))
	                            $(this).removeClass('tumble-arrow-head').addClass('down-arrow-head')
							else
	                            $(this).removeClass('down-arrow-head').addClass('tumble-arrow-head')
							">
	                    <strong>{t}Multimedia{/t}</strong>
                    </label>
                    <p class="options-panel" id="multimedia-options" style="display:none">
					<select id="photoOptions" name="photoOptions" style="width:250px;color:#666">
                        <option value="">maak een keuze</option>
                        <optgroup style="color:#000">
                        <option value="images">{t}met foto('s){/t}</option>
                        <option value="distribution">{t}met verspreidingskaart{/t}</option>
                        <option value="trend">{t}met trendgrafiek{/t}</option>
                        </optgroup>
					</select>
                    <input type="button" value=" > " onclick="addSearchParameter('photoOptions');" />
                    </p>
				</div>

				<div class="formrow">
                	<label for="dnaOptions"
                        class="clickable tumble-arrow-head" 
                        onclick="
                        	$('#dna-options').toggle();
                            if ($('#dna-options').is(':visible'))
	                            $(this).removeClass('tumble-arrow-head').addClass('down-arrow-head')
							else
	                            $(this).removeClass('down-arrow-head').addClass('tumble-arrow-head')
							">
	                    <strong>{t}DNA barcoding{/t}</strong>&nbsp;
                        <a href="http://www.nederlandsesoorten.nl/nlsr/nlsr/dnabarcoding.html" 
                        	target="_blank" 
                            title="klik voor help over dit onderdeel" 
                            class="help">&nbsp;</a>
                    </label>
                    <p class="options-panel" id="dna-options" style="display:none">
                    <select id="dnaOptions" name="dnaOptions"style="width:250px;color:#666">
                        <option value="">maak een keuze</option>
                        <optgroup style="color:#000">
                        <option value="dna">{t}met exemplaren verzameld{/t}</option>
                        <option value="dna_insuff">{t}minder dan drie exemplaren verzameld{/t}</option>
                        </optgroup>
                    </select>
                    <input type="button" value=" > " onclick="addSearchParameter('dnaOptions');" />
                    </p>
				</div>
	
    
				{foreach from=$traits item=t key=k1}
				<div class="formrow">
					<label for="traits"
                        class="clickable tumble-arrow-head" 
                        onclick="
                        	$('#traits{$k1}-options').toggle();
                            if ($('#traits{$k1}-options').is(':visible'))
	                            $(this).removeClass('tumble-arrow-head').addClass('down-arrow-head')
							else
	                            $(this).removeClass('down-arrow-head').addClass('tumble-arrow-head')
							">                    
						<strong>{$t.name}</strong>
					</label>
                    <table class="options-panel" id="traits{$k1}-options" style="display:none">
					{foreach from=$t.data item=d key=k2}
                    {if $d.type_sysname!=='stringfree'}
                    	<tr>
                        	<td class="traits-legend-cell"><label for="trait-{$k1}{$k2}">{$d.name}</label></td>
                            <td>
                                {if $d.type_allow_values==1 && $d.value_count>0}
                                <select trait-id="{$d.id}" id="trait-{$k1}{$k2}" style="width:250px;color:#666">
                                    <option value="">maak een keuze</option>
                                    <optgroup style="color:#000">
	                                {foreach from=$d.values item=v}
                                    <option value="{$v.id}">{$v.string_value}</option>
    	                            {/foreach}
                                    </optgroup>
                                </select>
                                {else if $d.type_allow_values==0}
                                
                                <select
                                	class="operator" 
                                    trait-id="{$d.id}" 
                                    id="operator-{$k1}{$k2}" 
                                    style="width:150px" 
                                    onchange="$('#trait-{$k1}{$k2}-2').toggle($('option:selected',this).attr('has-second-value')==1);"
								>
                                    <option value="==">{t}is gelijk aan{/t}</option>
                                    <option value="!=">{t}is ongelijk aan{/t}</option>
                                    <option value=">">{t}groter dan{/t}</option>
                                    <option value="<">{t}kleiner dan{/t}</option>
                                    <option value=">=">{t}groter dan of gelijk aan{/t}</option>
                                    <option value="<=">{t}kleiner dan of gelijk aan{/t}</option>
                                    <option value="<>" has-second-value="1">{t}ligt tussen{/t}</option>
                                    <option value="><" has-second-value="1">{t}ligt niet tussen{/t}</option>
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
                    </table>
				</div>
				{/foreach}

				<div class="formrow" style="margin-top:10px;border-top:1px dotted #999;padding-top:5px">
					<strong>{t}Geselecteerde zoekparameters{/t}</strong>
                    <span id="remove-all" style="display:none">&nbsp;
                    	<a href="#" onclick="removeAllSearchParameters();submitSearchParams();return;">{t}alles verwijderen{/t}</a>
                    	<!-- a href="nsr_search_extended.php">{t}alles verwijderen{/t}</a -->
					</span>
                    <ul id="search-parameters">
                    </ul>
				</div>

				<div class="formrow">
					<strong>{t}Resultaten sorteren op:{/t}</strong>
                    <select name="sort" id="sort">
                        <option value="name-valid"{if $search.sort!='name-valid'} selected="selected"{/if}>{t}Wetenschappelijk naam{/t}</option>
                        <option value="name-pref-nl"{if $search.sort=='name-pref-nl'} selected="selected"{/if}>{t}Nederlandse naam{/t}</option>
                    </select>
				</div>
                
                <div class="formrow">
					<input type="button=" class="zoekknop" value="zoek" onclick="submitSearchParams()" />
				</div>
                
			</fieldset>

			</div>
		</form>
		</div>

		<div id="results">
			<p>
				<h4><span id="resultcount-header">{$results.count}</span>
                {if $searchHR || $searchTraitsHR} {t}voor{/t} '{if $searchHR}{$searchHR}{/if}{if $searchTraitsHR}{$searchTraitsHR}{/if}'{/if}
                </h4>
			</p>
			{foreach from=$results.data item=v}
            <div class="result">
                {if $v.overview_image}
                <img src="http://images.naturalis.nl/120x75/{$v.overview_image}" />
                {/if}
                <strong><a href="../species/nsr_taxon.php?id={$v.id}">{$v.taxon}</a></strong><br />
                {if $v.common_name}{$v.common_name}<br />{/if}
                {t}Status voorkomen:{/t} {$v.presence_information_index_label} {$v.presence_information_title}
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


<script>
$(document).ready(function()
{
	{if $search}
		{foreach from=$search.presence item=v key=k}
		$("#presenceStatusList").val('presence[{$k}]');
		addSearchParameter('presenceStatusList');
		{/foreach}
	
		{foreach from=$search item=v key=k}
		{if $k=='images' || $k=='distribution' || $k=='trend'}
		$("#photoOptions").val('{$k}');
		addSearchParameter('photoOptions');
		$('label[for=photoOptions]').next().toggle(true);
		{else if $k=='dna' || $k=='dna_insuff'}
		$("#dnaOptions").val('{$k}');
		addSearchParameter('dnaOptions');
		$('label[for=dnaOptions]').next().toggle(true);
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
				$('label[for=traits]').next().toggle(true);
			}
	
		{/if}
	{/if}

	$('title').html('Uitgebreid zoeken naar soorten - '+$('title').html());

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


{include file="../shared/header.tpl"}

<style>
.options-panel {
	margin-top:5px;
}
.formrow select {
	font-size:1em;
	margin-left:0px;
	width:200px;
	margin-top:2px;
}
label.clickable {
	cursor:pointer;
	display:inline-block;
	height:15px;
}
label.clickable:hover {
	text-decoration:underline;
}
.zoekknop {
	cursor:pointer;
}
.traits-legend-cell {
	width:150px;
}	
.arrow-container {
	width:15px;
}
.arrow-e, .arrow-se, .arrow-s {
	width: 0;
	height: 0;
	margin-right:2px;
}
.arrow-e {
	margin-top:2px;
	border-top: 5px solid transparent;
	border-bottom: 5px solid transparent;
	border-left: 10px solid black;
}
.arrow-se {
	margin-top:2px;
	border-top:10px dashed transparent;
	border-right:10px solid black;
}
.arrow-s {
	margin-top:3px;
	border-left: 5px solid transparent;
	border-right: 5px solid transparent;
	border-top: 10px solid black;
}

.selectable-parameters {
	padding:0 8px 0 8px;
	-background-color:#ffe999;
	margin-bottom:10px;
}
.selected-parameters {
	margin:10px 0 0 -8px;
	-border-top:1px dashed #ddd;
	-border-bottom:1px dashed #ddd;
	padding:10px 6px 10px 10px;
	background-color:rgb(246, 245, 236);
}
#search-parameters {
	margin-top:5px;
}

</style>
                
<script>
var search_parameters=[];
var trait_group=null;
var init=true;

function addSearchParameter(id)
{
	
	if (!id) return;

	var ele=$('#'+id);
	var tagtype=ele.prop('tagName');
	var varlabel=$('label[for='+id+']').text().trim();	
	var istrait=ele.attr('id') && ele.attr('id').indexOf('trait-')===0;

	var traitid=null;
	var valueid=null;
	var value=null;
	var valuetext=null;
	var value2=null;
	var valuetext2=null;
	var operator=null;
	var operatorlabel=null;

	if (tagtype=='SELECT')
	{
		traitid=ele.attr('trait-id');
		valueid=$('#'+id+' :selected').val();
		if (valueid) value='on';
		valuetext=$('#'+id+' :selected').text().trim();

		if (valueid.indexOf(':')!=-1)
		{
			var d=valueid.split(':');
			valueid=d[0];
			value=d[1];
		}

	}
	else
	if (tagtype=='INPUT')
	{
		traitid=ele.attr('trait-id');
		valueid=null;
		value=ele.val();
		valuetext=value;

		var ele2=$('#'+id+'-2');
		//if (ele2.is(':visible'))
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

	if (!value || value.length==0)
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
	
	if(getTraitGroup())
	{
		$('#search-parameters').
			append(
				$(
					'<li>Taxa met exotenpaspoort '+
					' <a href="#" onclick="setTraitGroup(null);submitSearchParams();return false;"> X </a></li>'));
	}

	$('#remove-all').toggle(search_parameters.length>0 || getTraitGroup()!=null);
	$('.selected-parameters').toggle(search_parameters.length>0 || getTraitGroup()!=null);
	 

}

function removeSearchParameter(i)
{
	search_parameters.splice(i,1);
	printParameters();
}

function removeAllSearchParameters()
{
	search_parameters.splice(0);
	setTraitGroup(null);
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

function setTraitGroup(id)
{
	trait_group=id;
}

function getTraitGroup()
{
	return trait_group;
}

function toggle_panel(ele)
{
	$('#'+$(ele).attr('panel')).toggle();
}

function hover_panel_toggle(ele,out)
{
	var p=$('#'+$(ele).attr('panel'));
	var c=$(ele).children().children('div.arrow'); 
	if (out)
	{
		c.removeClass('arrow-se').addClass(p.is(':visible') ? 'arrow-s' :  'arrow-e')
	}
	else
	{
		c.removeClass('arrow-s').removeClass('arrow-e').addClass('arrow-se')
	}
}

function toggle_all_panels()
{
	var allopen=true;
	$('label').each(function()
	{
		if ($(this).attr('panel') && !$('#'+$(this).attr('panel')).is(':visible'))
		{
			allopen=false;
		}
	});
	$('label').each(function()
	{
		if ($(this).attr('panel') && (allopen || (!allopen && !$('#'+$(this).attr('panel')).is(':visible'))))
		{
			toggle_panel(this);
			hover_panel_toggle(this);
            hover_panel_toggle(this,true);
		}
	});
}

function submitSearchParams()
{
	if (init) return;

	var form=$('<form method="get"></form>').appendTo('body');
	form.append('<input type="hidden" name="group_id" value="'+$('#group_id').val()+'" />');
	form.append('<input type="hidden" name="group" value="'+$('#group').val()+'" />');
	//form.append('<input type="hidden" name="author_id" value="'+$('#author_id').val()+'" />');
	//form.append('<input type="hidden" name="author" value="'+$('#author').val()+'" />');
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

	var panels={};
	var j=0;

	$('.options-panel').each(function()
	{
		panels[j++]={ id:$(this).attr('id'),visible:$(this).is(':visible') };
	});

	form.append('<input type="hidden" name="panels" value="'+ encodeURIComponent(JSON.stringify(panels))+'" />');
	
	if (trait_group)
	{
		form.append('<input type="hidden" name="trait_group" value="'+ trait_group+'" />');
	}

	form.submit();	
}

</script>

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
					<label style="display:inline-block;margin-left:13px;margin-top:2px" accesskey="g" for="search">{t}Soortgroep{/t}</label>
					<input style="width:370px" type="text" size="60" class="field" id="group" name="group" autocomplete="off" value="{$search.group}">
					<div id="group_suggestion" match="start" class="auto_complete" style="display:none;"></div>
				</div>
				{*<div class="formrow">
					<label accesskey="g" for="author">{t}Auteur{/t}</label>
					<input type="text" size="60" class="field" id="author" name="author" autocomplete="off" value="{$search.author}">
					<div id="author_suggestion" match="start" class="auto_complete" style="display:none;"></div>
				</div>*}
			</fieldset>

			<fieldset class="selectable-parameters">

				<div class="formrow">
				<div style="float:right;margin-top:3px;">
                
                    <a href="#" onclick="toggle_all_panels();return false;">alles in-/uitklappen</a>&nbsp;&nbsp;
                    <span onmouseout="hintHide()" onmouseover="hint(this,'&lt;p&gt;Met dit zoekscherm maak je uiteenlopende selecties (onder)soorten. Verruim je selectie door meer dan 1 waarde binnen een kenmerk te selecteren (bijv. soorten met Status voorkomen 1a &lt;b&gt;of&lt;/b&gt; 1b). Vernauw je selectie door een waarde binnen een ander kenmerk te selecteren (bijv. soorten met Status voorkomen 1a &lt;b&gt;en&lt;/b&gt; met foto\'s). Druk op > om een kenmerkwaarde te selecteren.&lt;/p&gt;');" class="link">hulp bij zoeken</span>
			    </div>
    
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
                    <a href="http://www.nederlandsesoorten.nl/node/15" target="_blank"  title="{t}klik voor help over dit onderdeel{/t}" class="help">&nbsp;</a>
                    
					<!-- label for="presenceStatusList">
						<strong>{t}Status voorkomen{/t}</strong>&nbsp;
                        <a href="http://www.nederlandsesoorten.nl/node/15" target="_blank"  title="{t}klik voor help over dit onderdeel{/t}" class="help">&nbsp;</a>
					</label>
                    <br / -->
                    <p class="options-panel" id="presence-options-panel" style="display:none">
                        <select id="presenceStatusList" name="presenceStatusList" style="width:250px;margin-bottom:10px">
                            <option value="">maak een keuze</option>
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
                        <br />
                        <a href="#" onclick="addEstablished();submitSearchParams();return false;">{t}gevestigde soorten{/t}</a> / 
                        <a href="#" onclick="addNonEstablished();submitSearchParams();return false;">{t}niet gevestigde soorten{/t}</a>
					</p>
				</div>

				<div class="formrow">
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
                        	<td class="traits-legend-cell"><label for="multimedia-images">Foto('s)</label></td>
                            <td>
                                <select id="multimedia-images" style="width:250px;">
                                    <option value="">maak een keuze</option>
                                    <option value="images_on">met foto('s)</option>
                                    <option value="images_off">zonder foto's</option>
                                </select>
                                <input type="button" value=" > " onclick="addSearchParameter('multimedia-images');" />
							</td>
						</tr>
                    	<tr>
                        	<td class="traits-legend-cell"><label for="multimedia-distribution">Verspreidingskaart(en)</label></td>
                            <td>
                                <select id="multimedia-distribution" style="width:250px;">
                                    <option value="">maak een keuze</option>
                                    <option value="distribution_on">met verspreidingskaart(en)</option>
                                    <option value="distribution_off">zonder verspreidingskaarten</option>
                                </select>
                                <input type="button" value=" > " onclick="addSearchParameter('multimedia-distribution');" />
							</td>
						</tr>
                    	<tr>
                        	<td class="traits-legend-cell"><label for="multimedia-trend">Trendgrafiek</label></td>
                            <td>
                                <select id="multimedia-trend" style="width:250px;">
                                    <option value="">maak een keuze</option>
                                    <option value="trend_on">met trendgrafiek</option>
                                    <option value="trend_off">zonder trendgrafiek</option>
                                </select>
                                <input type="button" value=" > " onclick="addSearchParameter('multimedia-trend');" />
							</td>
						</tr>
					</table>
				</div>
                        
                        
				<div class="formrow">
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
                        title="klik voor help over dit onderdeel" 
                        class="help">&nbsp;</a>
                    <p class="options-panel" id="dna-options-panel" style="display:none">
                        <select id="dna-options" name="dna-options" style="width:250px;">
                            <option value="">maak een keuze</option>
                            <option value="dna">{t}met een of meer exemplaren verzameld{/t}</option>
                            <option value="dna_insuff">{t}minder dan drie exemplaren verzameld{/t}</option>
                        </select>
                        <input type="button" value=" > " onclick="addSearchParameter('dna-options');" />
                    </p>
				</div>
    
				{foreach from=$traits item=t key=k1}
				<div class="formrow">
					<label
                        class="clickable" 
                        panel="traits{$k1}-options"
                        onmouseover="hover_panel_toggle(this);"
                        onmouseout="hover_panel_toggle(this,true);"
                        onclick="toggle_panel(this);">
						<div class="arrow-container"><div class="arrow arrow-e"></div></div>
						<strong>{$t.name}</strong>
					</label>&nbsp;
					<a href="http://www.nederlandsesoorten.nl/content/exotenpaspoort" target="_blank"  title="{t}klik voor help over dit onderdeel{/t}" class="help">&nbsp;</a>
					
                    <table class="options-panel" id="traits{$k1}-options" style="display:none">
                    {if $t.description}
                    	<tr>
                        	<td colspan="2"><p>{$t.description}</p></td>
                        </tr>
                    {/if}
					{foreach from=$t.data item=d key=k2}
                    {if $d.type_sysname!=='stringfree'}
                    	<tr>
                        	<td class="traits-legend-cell"><label for="trait-{$k1}{$k2}">{$d.name}</label></td>
                            <td>
                                {if $d.type_allow_values==1 && $d.value_count>0}
                                <select trait-id="{$d.id}" id="trait-{$k1}{$k2}" style="width:250px;">
                                    <option value="">maak een keuze</option>
	                                {foreach from=$d.values item=v}
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
	                                {foreach from=$operators item=v key=k}
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
                    	<tr>
                        	<td colspan="2" style="padding-top:0.5em"><a href="#" onclick="setTraitGroup({$t.group_id});submitSearchParams();return;">Taxa met exotenpaspoort tonen</a></td>
                        </tr>
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

                {*<div class="formrow">
					<input type="button=" class="zoekknop" value="zoek" onclick="submitSearchParams()" />
				</div>*}
                
			</fieldset>

			</div>

		</div>

		<div id="results"> 
            <h4><span id="resultcount-header">{$results.count}</span>
            {* if $searchHR || $searchTraitsHR} {t}voor{/t} '{if $searchHR}{$searchHR}{/if}{if $searchTraitsHR}{$searchTraitsHR}{/if}'{/if *}
            </h4>

            <div class="formrow" style="margin-bottom:15px">
                {t}Resultaten sorteren op:{/t}
                <select name="sort" id="sort" onchange="submitSearchParams();">
                    <option value="name-valid"{if $search.sort!='name-valid'} selected="selected"{/if}>{t}Wetenschappelijk naam{/t}</option>
                    <option value="name-pref-nl"{if $search.sort=='name-pref-nl'} selected="selected"{/if}>{t}Nederlandse naam{/t}</option>
                </select>
            </div>

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
        
 		</form>


		{assign var=pgnEntityNames value=['soorten (of onderliggend taxon)','soorten (en onderliggende taxa)']}
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
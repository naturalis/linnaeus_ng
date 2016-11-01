{include file="../shared/header.tpl"}
{include file="../shared/flexslider.tpl"}
<style>
.options-panel {
	margin-top:5px;
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

/*.selected-parameters {
	margin:10px 0 0 -8px;
	padding:10px 6px 10px 10px;
	background-color:#fffbbb;
}*/
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
	console.log(search_parameters);

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
					' <a href="#" class="removeSearchParam" onclick="removeSearchParameter('+i+');submitSearchParams();return false;"><i class="ion-close-round"></i></a></li>'));
	}
	
	if(getTraitGroup())
	{
		$('#search-parameters').
			append(
				$(
					'<li>Taxa met exotenpaspoort '+
					' <a href="#" class="removeSearchParam" onclick="setTraitGroup(null);submitSearchParams();return false;"><i class="ion-close-round"></i></a></li>'));
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
	console.log(ele);
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

	var form=$('<form id="tradysuadijs" method="get"></form>').appendTo('body');
	form.append('<input type="hidden" name="group_id" value="'+$('#group_id').val()+'" />');
	form.append('<input type="hidden" name="group" value="'+$('#desktopgroup').val()+'" />');
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

	form.append('<input type="hidden" name="just_species" value="'+getJustSpeciesToggle()+'" />');

	form.submit();	
}

</script>


<div id="dialogRidge">	
	{include file="_searchtabs.tpl" activeTab="extendedSearch" responsiveTabs="mobile"}
	<div id="left">
		{include file="_extendedSearchFilters.tpl"}
		{include file="_toolbox.tpl"}
	</div>  

	<div id="content" class="simple-search">
		{include file="_searchtabs.tpl" activeTab="extendedSearch" responsiveTabs="desktop"}

		<div id="results" class="searchResultContainer"> 
			<div class="searchHeader">
	      <h2>
	      	Zoekresultaten
	      </h2>
          
	      <div class="formrow orderList">
		      <select name="sort" id="sort" class="customSelect" onchange="submitSearchParams();">
	          <option value="name-valid"{if $search.sort!='name-valid'} selected="selected"{/if}>{t}Wetenschappelijke naam{/t}</option>
	          <option value="name-pref-nl"{if $search.sort=='name-pref-nl'} selected="selected"{/if}>{t}Nederlandse naam{/t}</option>
		      </select>
	      </div>
      </div>
        <div style="margin-bottom:40px;"><span id="resultcount-header" style="float:left"></span>
            <a href="#" id="just-species-toggle" style="float:left;padding-left:10px;" onclick="toggleJustSpeciesToggle();submitSearchParams();return false;">
            {t}alleen soorten tonen{/t}
            </a>
        </div>


      <ul class="searchResult">
			{foreach from=$results.data item=v}
        <li class="result">
        	<a href="../species/nsr_taxon.php?id={$v.taxon_id}" class="clicklink"></a>	
          <a href="../species/nsr_taxon.php?id={$v.id}">{$v.taxon}</a>
          {if $v.common_name}
          	<span class="commonName">
          		{$v.common_name}
						</span>
      		{/if}
      		<span class="status">
          {t}Status voorkomen:{/t} {$v.presence_information_index_label} {$v.presence_information_title}
        	</span>
        	{if $v.overview_image}
        		<div class="image">
          		<img src="{$taxon_base_url_images_thumb_s}{$v.overview_image}" />
        		</div>
          {/if}
        </li>
			{/foreach}
			</ul>

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
	</div>
</div>
	
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

	{if $search.just_species}
	setJustSpeciesToggle({$search.just_species});
	{/if}
	
	$('#just-species-toggle').html(getJustSpeciesToggle()==0 ? '{t}alleen soorten tonen{/t}' : '{t}soorten en lagere taxa tonen{/t}' );

	$('title').html('{t}Uitgebreid zoeken naar soorten{/t} - '+$('title').html());

	bindKeys();

	$("[id$=group]").keyup(function(e)
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
function __(text)
{
	// _() is sloooooow!
	return text;
	return _(text);
}

var resultsHtmlTemplate = '<div class="resultRow">%RESULTS%</div>';
var resultsLineEndHtmlTemplate = '</div><br/><div class="resultRow">';
var brHtmlTemplate = '<br />';

var photoLabelHtmlTemplate = ' \
<div style="margin-left:130px"> \
	%SCI-NAME% \
	%GENDER% \
	%COMMON-NAME% \
	%PHOTO-DETAILS% \
</div> \
';

var photoLabelGenderHtmlTemplate = '<img class="gender" height="17" width="8" src="%IMG-SRC%" title="%GENDER-LABEL%" />';

var photoLabelPhotographerHtmlTemplate = '<br />(%PHOTO-LABEL% %PHOTOGRAPHER%)';

var imageHtmlTemplate = '\
<a rel="prettyPhoto[gallery]" href="%IMAGE-URL%" pTitle="%PHOTO-LABEL%" title=""> \
	<img class="result-image" src="%IMAGE-URL%" title="%PHOTO-CREDIT%" /> \
</a>\
';

var genderHtmlTemplate = '<img class="result-gender-icon" src="%ICON-URL%" title="%GENDER-LABEL%" />';

var matrixLinkHtmlTemplate = '<br /><a href="?mtrx=%MATRIX-ID%">%MATRIX-LINK-TEXT%</a>';

var remoteLinkClickHtmlTemplate = 'onclick="window.open(\'%REMOTE-LINK%\',\'_blank\');" title="%TITLE%"';

var statesClickHtmlTemplate = 'onclick="nbcToggleSpeciesDetail(\'%LOCAL-ID%\');return false;"title="%TITLE%"';

var relatedClickHtmlTemplate = 'onclick="setSimilar({id:%ID%,type:\'%TYPE%\'});return false;" title="%TITLE%"';

var statesHtmlTemplate = '\
<div id="det-%LOCAL-ID%" class="result-detail hidden"> \
	<ul> \
		<li>%STATES%</li> \
	</ul> \
</div> \
';

var statesJoinHtmlTemplate = '</li><li>';

var speciesStateItemHtmlTemplate = '<span class="result-detail-label">%CHARACTER%:</span> <span class="result-detail-value">%STATE%</span>';

var resultHtmlTemplate = '\
<div class="result%CLASS-HIGHLIGHT%" id="res-%LOCAL-ID%"> \
	<div class="result-result"> \
		<div class="result-image-container"> \
			%IMAGE-HTML% \
		</div> \
		<div class="result-labels"> \
			%GENDER% \
			<span class="result-name-scientific">%SCI-NAME%</span> \
			%MATRIX-LINK% \
			<span class="result-name-common"><br />%COMMON-NAME%</span> \
            </div> \
        </div> \
        <div class="result-icons"> \
			<div class="result-icon%REMOTE-LINK-CLASS%" \
				%REMOTE-LINK-CLICK% \
			>%REMOTE-LINK-ICON%</div> \
			<div class="result-icon%SHOW-STATES-CLASS%" id="tog-%LOCAL-ID%" \
				%SHOW-STATES-CLICK% \
			>%SHOW-STATES-ICON%</div> \
			<div class="result-icon%RELATED-CLASS% related" \
				%RELATED-CLICK% \
			>%RELATED-ICON%</div> \
        </div>%STATES% \
    </div> \
';

var resultBatchHtmlTemplate= '<span class=result-batch style="%STYLE%">%PREVIOUS-RESULTS% %RESULTS%</span>' ;
var buttonMoreHtmlTemplate='<li id="show-more"><input type="button" id="show-more-button" onclick="printResults();return false;" value="%LABEL%" class="ui-button"></li>';
var counterExpandHtmlTemplate='%START-NUMBER%%NUMBER-SHOWING%&nbsp;%FROM-LABEL%&nbsp;%NUMBER-TOTAL%';
var pagePrevHtmlTemplate='<li><a href="#" onclick="browsePage(\'p\');return false;">&lt;</a></li>';
var pageCurrHtmlTemplate='<li><strong>%NR%</strong></li>';
var pageNumberHtmlTemplate='<li><a href="#" onclick="browsePage(%INDEX%);return false;">%NR%</a></li>';
var pageNextHtmlTemplate='<li><a href="#" onclick="browsePage(\'n\');return false;" class="last">&gt;</a></li>';
var counterPaginateHtmlTemplate=' %FIRST-NUMBER%-%LAST-NUMBER% %NUMBER-LABEL% %NUMBER-TOTAL%';

var menuOuterHtmlTemplate ='<ul>%MENU%</ul>';

var menuGroupHtmlTemplate = '\
<li id="character-item-%ID%" class="closed"><a href="#" onclick="toggleGroup(%ID%);return false;">%LABEL%</a></li> \
<ul id="character-group-%ID%" class="hidden"> \
	%CHARACTERS% \
</ul> \
';

var menuCharDisabledHtmlTemplate='<li class="inner%CLASS% disabled">%LABEL%%VALUE%	%SELECTED% </li>';

var menuCharEmergentDisabledHtmlTemplate='\
<li class="inner%CLASS%" title="%TITLE%"> \
	<a class="facetLink emergent_disabled" href="#" onclick="showStates(%ID%);return false;">(%LABEL%%VALUE%)</a> \
	%SELECTED% \
</li> \
';
var menuCharHtmlTemplate='\
<li class="inner%CLASS%"> \
	<a class="facetLink" href="#" onclick="showStates(%ID%);return false;">%LABEL%%VALUE%</a> \
	%SELECTED% \
</li>';

var menuLoneCharHtmlTemplate='\
<li class="inner ungrouped last"> \
	<a class="facetLink" href="#" onclick="showStates(%ID%);return false;">%LABEL%%VALUE%</a> \
	%SELECTED% \
</li> \
';

var menuSelStateHtmlTemplate = '\
<div class="facetValueHolder"> \
%VALUE% %LABEL% %COEFF% \
<a href="#" class="removeBtn" onclick="clearStateValue(\'%STATE-ID%\');return false;"> \
<img src="%IMG-URL%"></a> \
</div> \
';

var menuSelStatesHtmlTemplate = '<span>%STATES%</span>';

var iconUrlHtmlTemplate ='<img class="result-icon-image" src="%IMG-URL%">';
var iconInfoHtmlTemplate='<img class="result-icon-image icon-info" src="%IMG-URL%">';
var iconSimilarTemplate='<img class="result-icon-image icon-similar" src="%IMG-URL%">';

var similarHeaderHtmlTemplate='\
%HEADER-TEXT% <span id="similarSpeciesName">%SPECIES-NAME%</span> \
<br /> \
<a class="clearSimilarSelection" href="#" onclick="closeSimilar();return false;">%BACK-TEXT%</a> \
<span id="show-all-divider"> | </span> \
<a class="clearSimilarSelection" href="#" onclick="nbcToggleAllSpeciesDetail();return false;" id="showAllLabel">%SHOW-STATES-TEXT%</a> \
';

var settings = {
	matrixId: 0,
	projectId: 0,
	perPage: 16,
	perLine: 4,
	start: 0,
	expandedShowing: 0,
	expandResults: true,
	useEmergingCharacters: true,
	showSpeciesDetails: true,
	imageRoot: "",
	defaultImage: "",
	browseStyle: 'paginate', // expand, paginate, show_all
	expandedPrevious: 0,
	paginate: true,
	currPage: 0,
	lastPage: 0,
	scoreThreshold: 0
};

var data={
	menu: Array(), // full menu
	dataset: Array(), // full dataset
	resultset: Array(), // result subset
	states: {}, // user-selected states
	scores: {}, // match scores based on selection
	related: {} // related species
}

var lastScrollPos=0;
var lastShowing=0;
var tempstatevalue="";
var openGroups=Array();

function retrieveDataSet()
{
	setCursor('wait');

	$.ajax({
		url : 'ajax_interface.php',
		type: 'POST',
		data : ({
			action : 'get_dataset',
			time : getTimestamp(),
			key : settings.matrixId,
			p : settings.projectId
		}),
		success : function (data)
		{
			//console.log(data);
			setDataSet($.parseJSON(data));
			//console.dir(getDataSet());

			applyScores();
			clearResults();
			printResults();
			
//			if (p && p.action!='similar') nbcDoOverhead();
//			nbcDoPaging();
//			if (p && p.action=='similar') nbcPrintSimilarHeader();
//			if (p && p.closeDialog==true) jDialogCancel();
//			if (p && p.refreshGroups==true) nbcRefreshGroupMenu();

			setCursor();
		}
	});
}

function retrieveMenu()
{
	setCursor('wait');

	$.ajax({
		url : 'ajax_interface.php',
		type: 'POST',
		data : ({
			action : 'get_menu',
			time : getTimestamp(),
			key : settings.matrixId,
			p : settings.projectId
		}),
		success : function (data)
		{
			//console.log(data);
			setMenu($.parseJSON(data));
			filterEmergingCharacters();
			printMenu();
			//console.dir(menu);
			setCursor();
		}
	});
}

function resetMatrix()
{
	clearStateValue();
	openGroups.splice(0,openGroups.length);
}

function printResults()
{
	var resultset = getResultSet();

	if (resultset && settings.browseStyle=='expand') 
	{
		printResultsExpanded();
	}
	else
	if (resultset && settings.browseStyle!='expand') // (non-)paginated
	{
		printResultsPaginated();
		if (settings.browseStyle=='paginate')
		{
			printPaging();
		}
	}

	clearOverhead();
	printCountHeader();
	prettyPhotoInit();

	$('.result-icon').on('mouseover',function()
	{	
		$(this).find('img').attr('src', $(this).find('img').attr('src') ? $(this).find('img').attr('src').replace('_grijs','')  : "" );
	}).on('mouseout',function()
	{
		$(this).find('img').attr('src', $(this).find('img').attr('src') ? $(this).find('img').attr('src').replace('.png','_grijs.png')  : "" );
	});

	
}

function clearResults()
{
	$('#results-container').html('');
	settings.start=0;
	settings.expandedShowing=0;
}

function printResultsExpanded()
{
	var resultset = getResultSet();

	settings.showSpeciesDetails = resultset.length <= settings.perPage;

	var s="";
	var added=0;
	var d=0;
	
	for(var i=0;i<resultset.length;i++)
	{
		if (i>=settings.expandedShowing && i<settings.expandedShowing+settings.perPage)
		{
			s=s+formatResult(resultset[i]);

			added++;

			if (++d==settings.perLine)
			{
				s=s+resultsLineEndHtmlTemplate;
				d=0;
			}
		}
	}
	
	

	$('#results-container').html(
		resultBatchHtmlTemplate
			.replace('%STYLE%',"")
			.replace('%PREVIOUS-RESULTS%',$('#results-container').html())
			.replace('%RESULTS%', resultsHtmlTemplate.replace('%RESULTS%',s))
	);

	// parallel processing if show() causes mayhem when clicking more-button fast.
	//		.replace('%STYLE%',(settings.expandedShowing>0  ? 'display:none' : ''))
	//	$('.result-batch:hidden').show('normal');
	
	settings.expandedShowing=settings.expandedShowing+added;

	if (settings.expandedShowing<resultset.length-1)
	{
		if (!$("#show-more").is(':visible'))
		{
			$("#paging-footer").append( buttonMoreHtmlTemplate.replace('%LABEL%',__('meer resultaten laden')) );
			$("#footerPagination").addClass('noline');
		}
	}
	else
	{
		$("#show-more").remove();
		$("#footerPagination").removeClass('noline');
	}
	
	if (settings.expandedShowing>0) 
	{
		//window.scrollBy(0,99999);
	}

}

function printResultsPaginated()
{
	var resultset = getResultSet();

	var s="";
	var d=0;

	for(var i=0;i<resultset.length;i++)
	{
		if (
			(settings.browseStyle=='paginate' && i>=settings.start && i<settings.start+settings.perPage) || 
			settings.browseStyle=='show_all'
		)
		{
			s=s+formatResult(resultset[i]);
			if (++d==settings.perLine)
			{
				s=s+resultsLineEndHtmlTemplate;
				d=0;
			}
		}
	}

	$('#results-container').html(resultsHtmlTemplate.replace('%RESULTS%',s));
}

function formatResult( data )
{
	//console.dir(data);
	
	if ( data.type=='taxon' )
	{
		//var sciName=data.label;
		var sciName='<i>'+data.taxon+'</i>';
		var commonName=data.commonname ? data.commonname : "";
	}
	else
	if ( data.type=='variation' )
	{
		//var sciName=data.taxon.label;
		var sciName='<i>'+data.taxon.taxon+'</i>';
		var commonName=data.label ? data.label : "";
	}


/*
	if (data.l!=data.c && data.l.indexOf(data.c)===0)
	{
		data.l = data.c + ' (' + data.l.replace(data.c,'').replace(/(^\s|\s$)/,'') + ')';
	}
*/

	if (settings.showSpeciesDetails && data.states)
	{
		var states = Array();

		for(var i in data.states)
		{
			var state=data.states[i];
			
			if (state.characteristic==undefined)
				continue;
			
			var labels = Array();
			
			if (state.characteristic.indexOf('|')!=false)
			{
				var t = state.characteristic.split('|');
				t = t[0];
			} 
			else
			{
				var t = state.characteristic;
			}
			
			for(var j in state.states)
			{
				labels.push(state.states[j].label);
			}

			if (labels.length>1)
			{
				var l = labels.join('; ');
			}
			else
			{
				var l = labels[0];
			}

			states.push(
				speciesStateItemHtmlTemplate
					.replace('%CHARACTER%',t)
					.replace('%STATE%',l)
			);
		}
	}

	var image="";

	if (data.info.url_image)
	{
		image=data.info.url_image;
	}
	else
	{
		if (settings.defaultImage) image=settings.defaultImage;
	}
		
	if (image && !image.match(/^(http:\/\/|https:\/\/)/i)) image=baseUrlProjectImages+image;
	
	var id = data.type+'-'+data.id;
	var showStates = states && states.length > 0;

	photoLabelHtml=
		photoLabelHtmlTemplate
			.replace('%SCI-NAME%',sciName)
			.replace('%GENDER%',(data.gender && data.gender.gender ?
				photoLabelGenderHtmlTemplate
					.replace('%IMG-SRC%', settings.imageRoot + data.gender.gender+'.png')
					.replace('%GENDER-LABEL%', data.gender.gender_label)
				: "" ))
			.replace('%COMMON-NAME%',(commonName ? brHtmlTemplate + commonName : ""))
			.replace('%PHOTO-DETAILS%',(data.info.photographer ? 
				photoLabelPhotographerHtmlTemplate
					.replace('%PHOTO-LABEL%', __('foto')+' &copy;' )
					.replace('%PHOTOGRAPHER%', data.info.photographer )
				: ""));

	imageHtml=
		imageHtmlTemplate
			.replace(/%IMAGE-URL%/g,image)
			.replace('%PHOTO-LABEL%',encodeURIComponent(photoLabelHtml))
			.replace('%PHOTO-CREDIT%',(data.info.photographer ? __('foto')+' &copy;'+data.info.photographer : ''))
		;	

	resultHtml=
		resultHtmlTemplate
			.replace('%CLASS-HIGHLIGHT%',(data.h ? ' result-highlight' : ''))
			.replace('%IMAGE-HTML%',(image ? imageHtml : ""))
			.replace('%GENDER%',(data.gender && data.gender.gender ? 
				genderHtmlTemplate
					.replace('%ICON-URL%', settings.imageRoot+data.gender.gender+'.png') 
					.replace('%GENDER-LABEL%', data.gender.gender_label) 
				: "" )
			)
			.replace('%SCI-NAME%', sciName)
			.replace('%MATRIX-LINK%', (data.type=='matrix' ? 
				matrixLinkHtmlTemplate.replace("%MATRIX-ID%",data.id).replace("%MATRIX-LINK-TEXT%",__('Ga naar sleutel'))
				: ""))
			.replace('%COMMON-NAME%', commonName)

			.replace('%REMOTE-LINK-CLASS%', data.info.url_external_page ? "" : " no-content")
			.replace('%REMOTE-LINK-CLICK%', data.info.url_external_page ?  
				remoteLinkClickHtmlTemplate
					.replace('%REMOTE-LINK%', data.info.url_external_page)
					.replace('%TITLE%', nbcLabelExternalLink)
				: "")
			.replace('%REMOTE-LINK-ICON%', data.info.url_external_page ?
				iconUrlHtmlTemplate.replace('%IMG-URL%',settings.imageRoot+"information_grijs.png") : "")
			.replace('%SHOW-STATES-CLASS%', showStates ? "" : " no-content")
			.replace('%SHOW-STATES-CLICK%', showStates ?  statesClickHtmlTemplate.replace('%TITLE%',nbcLabelDetails) : "")
			.replace('%SHOW-STATES-ICON%', showStates ?
				iconInfoHtmlTemplate.replace('%IMG-URL%',settings.imageRoot+"lijst_grijs.png") : "")
			.replace('%RELATED-CLASS%', data.related_count>0 ? "" : " no-content")
			.replace('%RELATED-CLICK%', (data.related_count>0 ?  
				relatedClickHtmlTemplate
					.replace('%TYPE%', data.type)
					.replace('%ID%', data.id)
					.replace('%TITLE%', nbcLabelSimilarSpecies)
				: "" )
			)
			.replace('%RELATED-ICON%', data.related_count>0 ?
				iconSimilarTemplate.replace('%IMG-URL%',settings.imageRoot+"gelijk_grijs.png") : "")
			.replace('%STATES%', showStates ? statesHtmlTemplate.replace( '%STATES%',states.join(statesJoinHtmlTemplate)) : "")
			.replace(/%LOCAL-ID%/g,id)
			.replace(/%ID%/g,data.od)
			;
			
	return resultHtml;
}

function clearOverhead()
{
	$('#result-count').html('');
	$('#similarSpeciesHeader').removeClass('visible').addClass('hidden');
	$('#similarSpeciesHeader').html('');
}

function printCountHeader()
{
	var resultset = getResultSet();
	
	if (settings.browseStyle=='expand')
	{
		$('#result-count').html(
			counterExpandHtmlTemplate
				.replace('%START-NUMBER%',(settings.expandedShowing > 1 ? "1-" : "" ))
				.replace('%NUMBER-SHOWING%',settings.expandedShowing)
				.replace('%FROM-LABEL%',__('van'))
				.replace('%NUMBER-TOTAL%',resultset.length)
		);
	}
	else
	if (settings.browseStyle=='paginate')
	{
		$('#result-count').html(
			counterPaginateHtmlTemplate
				.replace('%FIRST-NUMBER%', (settings.start+1))
				.replace('%LAST-NUMBER%',(settings.start+settings.perPage))
				.replace('%NUMBER-LABEL%',__('van'))
				.replace('%NUMBER-TOTAL%',resultset.length)
		);
	}
	else
	{
		$('#result-count').html(
			counterPaginateHtmlTemplate
				.replace('%FIRST-NUMBER%',1)
				.replace('%LAST-NUMBER%',resultset.length)
				.replace('%NUMBER-LABEL%',"")
				.replace('%NUMBER-TOTAL%',"")
		);
	}
}

function clearPaging()
{
	$('#paging-header').html('');	
	$('#paging-footer').html('');	
}

function printPaging()
{
	var resultset = getResultSet();

	settings.lastPage = Math.ceil(resultset.length / settings.perPage);
	settings.currPage = Math.floor(settings.start / settings.perPage);

	if (settings.lastPage > 1 && settings.currPage!=0)
	{
		$("#paging-header").append( pagePrevHtmlTemplate );
	}
	
	if (settings.lastPage>1)
	{ 
		for (var i=0;i<settings.lastPage;i++)
		{
			if (i==settings.currPage)
			{
				$("#paging-header").append( pageCurrHtmlTemplate.replace('%NR%',(i+1)) );
			}
		    else
			{
				$("#paging-header").append( pageNumberHtmlTemplate.replace('%NR%',(i+1)).replace('%INDEX%',i) );
			}
		}
	}

	if (settings.lastPage > 1 && settings.currPage<settings.lastPage-1)
	{
		$("#paging-header").append( pageNextHtmlTemplate );
	}

	$("#paging-footer").html($("#paging-header").html());
}

function browsePage( id )
{
	if (id=='n') settings.start = settings.start+settings.perPage;
	else if (id=='p') settings.start = settings.start-settings.perPage;
	else if (!isNaN(id)) settings.start = id * settings.perPage;
	else return;
			
//	nbcSaveSessionSetting('nbcStart',nbcStart);
	clearResults();
	printResults();
	clearPaging();
	printPaging();

}

function getActiveStates( id )
{
	var states=getStates();
	
	if (!states) return;
	
	var res=Array();
	
	for(var i in states)
	{
		var state=states[i];
		if (state.characteristic_id==id)
		{
			res.push(state);
		}
	}
	
	return (res.length>0 ? res : null);
}

function printMenu()
{
	$('#facet-categories-menu').html('');
	
	var menu=getMenu();
	var buffer=Array();

	for (var i in menu)
	{
		var item = menu[i];

		var s="";
		
		if (item.type=='group')
		{
			var c="";

			for (var j in item.chars)
			{
				var char = item.chars[j];

				var activestates=getActiveStates(char.id);
				
				var l=""

				if (activestates)
				{
					openGroups.push(item.id);

					var t="";
					for (var k in activestates)
					{
						var state = activestates[k];
						t=t+menuSelStateHtmlTemplate
							.replace('%VALUE%',(state.value ? state.value : ''))
							.replace('%LABEL%',(state.label ? state.label : ''))
							.replace('%COEFF%',(state.separationCoefficient ? '('+state.separationCoefficient+') ' : ''))
							.replace('%STATE-ID%',state.val)
							.replace('%IMG-URL%',settings.imageRoot+'clearSelection.gif');
					}
					
					l=menuSelStatesHtmlTemplate.replace('%STATES%',t);
				}

				if (char.disabled==true)
				{
					c=c+menuCharDisabledHtmlTemplate
						.replace('%CLASS%',(j==(item.chars.length-1)?' last':''))
						.replace('%LABEL%',char.label)
						.replace('%VALUE%',(char.value?' '+char.value:''))
						.replace('%SELECTED%',l);
				}
				else
				if (char.emergent_disabled==true)
				{
					c=c+menuCharEmergentDisabledHtmlTemplate
						.replace('%CLASS%',(j==(item.chars.length-1)?' last':''))
						.replace('%ID%',char.id)
						.replace('%LABEL%',char.label)
						.replace('%TITLE%',__( "Dit kenmkerk is bij de huidige selectie niet onderscheidend." ))
						.replace('%VALUE%',(char.value?' '+char.value:''))
						.replace('%SELECTED%',l);
				}
				else
				{
					c=c+menuCharHtmlTemplate
						.replace('%CLASS%',(j==(item.chars.length-1)?' last':''))
						.replace('%ID%',char.id)
						.replace('%LABEL%',char.label)
						.replace('%VALUE%',(char.value?' '+char.value:''))
						.replace('%SELECTED%',l);
				}
			}
			
			s=menuGroupHtmlTemplate
				.replace(/%ID%/g,item.id)
				.replace('%LABEL%',item.label)
				.replace('%CHARACTERS%',c);

		}
		else
		if (item.type=='char')
		{
			var activestates=getActiveStates(item.id);
			
			var l=""
	
			if (activestates)
			{
				var t="";
				for (var k in activestates)
				{
					var state = activestates[k];
					t=t+menuSelStateHtmlTemplate
						.replace('%VALUE%',(state.value ? state.value : ''))
						.replace('%LABEL%',(state.label ? state.label : ''))
						.replace('%COEFF%',(state.separationCoefficient ? '('+state.separationCoefficient+') ' : ''))
						.replace('%STATE-ID%',state.val)
						.replace('%IMG-URL%',settings.imageRoot+'clearSelection.gif');
				}
				
				l=menuSelStatesHtmlTemplate.replace('%STATES%',t);
			}
			
			s=menuLoneCharHtmlTemplate
				.replace('%ID%',item.id)
				.replace('%LABEL%',item.label)
				.replace('%VALUE%',(item.value?' '+item.value:''))
				.replace('%SELECTED%',l);
		}
		
		buffer.push(s);

	}
	
	$('#facet-categories-menu').html( menuOuterHtmlTemplate.replace('%MENU%',buffer.join('\n') ) );
	
	for(var i in $.unique(openGroups))
	{
		toggleGroup( openGroups[i], true );
	}
	
	var states=getStates();
	
	if (!states || states.count==0)
	{
		$('#clearSelectionContainer').addClass('ghosted');
	}
	else
	{
		$('#clearSelectionContainer').removeClass('ghosted');
	}	
}

function toggleGroup( id, forceOpen )
{
	if ( $('#character-group-'+id).css('display')=='none' || forceOpen )
	{
		$('#character-group-'+id).removeClass('hidden').addClass('visible');
		$('#character-item-'+id).removeClass('closed').addClass('open');
		openGroups.push(id);
	}
	else
	{
		$('#character-group-'+id).removeClass('visible').addClass('hidden');
		$('#character-item-'+id).removeClass('open').addClass('closed');

		for(var i=openGroups.length-1; i>=0; i--)
		{
			if(openGroups[i]===id)
			{
			   openGroups.splice(i,1);
			}
		}
	}
} 

function showStates(id)
{
	setCursor('wait');

	$.ajax({
		url : 'ajax_interface.php',
		type: 'POST',
		data : ({
			action : 'get_character_states',
			id: id,
			time : getTimestamp(),
			key : settings.matrixId,
			p : settings.projectId
		}),
		success : function (data)
		{
			//console.log(data);
			data = $.parseJSON(data);
			showDialog(data.title,data.page,{width:data.width,height:data.height,showOk:data.showOk});
			setCursor();
		}
	});

}

function clearStateValue(state)
{
	setState({state:state,action:'clear_state'});
}

function setStateValue(state)
{
	var state=state?state:$('#state-id').val();
	setState({state:state,value:tempstatevalue});
}

function setState( p )
{
	setCursor('wait');

	$.ajax({
		url : 'ajax_interface.php',
		type: 'POST',
		data : ({
			action : (p && p.action) ? p.action : 'set_state' ,
			state : p.state,
			value : p.value,
			time : getTimestamp(),
			key : settings.matrixId,
			p : settings.projectId
		}),
		success : function(data)
		{
			//console.log(data);
			var d=$.parseJSON(data);

			setScores(d.scores);
			setStates(d.states);
			//console.dir(getStates());

			applyScores();
			clearResults();
			printResults();
			printMenu();

			setCursor();
		}
	});
	
}

function applyScores()
{
	var scores=getScores();
	var states=getStates();
	var dataset=getDataSet();
	var resultset=getResultSet();
	
	// clean slate (also include states to be sure it's not a selection that returns zero matches)
	if ((!states || states.length==0) && (!scores || scores.length==0))
	{
		resultset=dataset.slice();
	}
	else
	{
		resultset.splice(0,resultset.length);

		for(var i in scores)
		{
			var score=scores[i];
			for(var j in dataset)
			{
				var item=dataset[j];
				if (score.id==item.id && score.type==item.type && (settings.scoreThreshold==0 || score.score>=settings.scoreThreshold))
				{
					resultset.push(item);
				}
			}
		}
	}
	
	setResultSet(resultset);

	// scores are sorted in the controller
}

function applyRelated()
{
	var related=getRelated();
	var dataset=getDataSet();
	var resultset=getResultSet();

	if ((!related || related.length==0))
	{
		resultset=dataset.slice();
	}
	else
	{
		resultset.splice(0,resultset.length);

		for(var i in related)
		{
			var relate=related[i];
			for(var j in dataset)
			{
				var item=dataset[j];
				if (relate.relation_id==item.id && relate.ref_type==item.type)
				{
					resultset.push(item);
				}
			}
		}
	}
	
	setResultSet(resultset);
}

function setSimilar( p )
{
	setCursor('wait');

	$.ajax({
		url : 'ajax_interface.php',
		type: 'POST',
		data : ({
			action : 'get_similar' ,
			id : p.id,
			type : p.type,
			time : getTimestamp(),
			key : settings.matrixId,
			p : settings.projectId
		}),
		success : function(data)
		{
			var related=$.parseJSON(data);
			setRelated(related);
			
			setLastShowing();
			setLastScrollPos();
			applyRelated();
			clearResults();
			printResults();
			printSimilarHeader();

			setCursor();
		}
	});
	
}


function clearSimilarHeader()
{
	$('#similarSpeciesHeader').html('');	
}

function printSimilarHeader()
{
	var resultset = getResultSet();
	
	$('#similarSpeciesHeader').html(
		similarHeaderHtmlTemplate
			.replace('%HEADER-TEXT%', __('Gelijkende soorten van'))
			.replace('%SPECIES-NAME%', resultset[0].label)
			.replace('%BACK-TEXT%', __('terug'))
			.replace('%SHOW-STATES-TEXT%', __('alle onderscheidende kenmerken tonen'))
	).removeClass('hidden').addClass('visible');
	
	$('.result-icon.related').find('img').remove();

}

function closeSimilar()
{
	setRelated();
	clearSimilarHeader();
	applyScores();
	clearResults();
	printResults();
	window.scroll(0,getLastScrollPos());
}
















var nbcFullDatasetCount = 0;
var nbcDetailShowStates = Array();
var nbcSearchTerm = '';
var nbcLabelShowAll = '';
var nbcLabelHideAll = '';
var nbcLabelClose = '';
var nbcLabelDetails = '';
var nbcLabelBack = '';
var nbcLabelSimilarSpecies = '';
var nbcPreviousBrowseStyles = {};
var baseUrlProjectImages = null;

function nbcDoSearch()
{
	var str = $('#inlineformsearchInput').val();
	str = str.replace(/^\s+|\s+$/g, ''); 

	if (str.length==0) return false;

	nbcSearchTerm=str;
	setState({norefresh:true,clearState:true});
	
	setCursor('wait');

	allAjaxHandle = $.ajax({
		url : 'ajax_interface.php',
		type: 'POST',
		data : ({
			action : 'do_search',
			params : {term: nbcSearchTerm},
			time : getTimestamp(),
			key : matrixId,
			p : projectId
		}),
		success : function (data)
		{
			//console.log(data);
			nbcData = $.parseJSON(data);
			nbcDoResults();
			nbcDoOverhead();
			nbcDoPaging();
			nbcPrintSearchHeader();
			nbcSaveSessionSetting('nbcSearch',nbcSearchTerm);

			setCursor();
			
			return false;

		}
	});
	return false; // suppress submit of form
}









function nbcClearSearchTerm() {
	
	nbcSearchTerm='';
	$('#inlineformsearchInput').val('');

}

function nbcCloseSearch() {

	nbcSetPaginate(nbcPreviousBrowseStyles.paginate);
	nbcSetExpandResults(nbcPreviousBrowseStyles.expand);
	nbcExpandedShowing = nbcPreviousBrowseStyles.expandShow;
	settings.expandedPrevious = nbcPreviousBrowseStyles.expandPrev;

	$('#inlineformsearchInput').val('');

	nbcGetResults();
	nbcClearOverhead();
	nbcSaveSessionSetting('nbcSearch');

	window.scroll(0,nbcPreviousBrowseStyles.lastPos);

}

function nbcPrintSearchHeader() {

	$('#similarSpeciesHeader').html(
		sprintf(__('Zoekresultaten voor %s'),'<span id="searchedForTerm">'+nbcSearchTerm+'</span>')+'<br />'+
		'<a class="clearSimilarSelection" href="#" onclick="nbcCloseSearch();return false;">'+nbcLabelBack+'</a>'
	);
	$('#similarSpeciesHeader').removeClass('hidden').addClass('visible');
}






function nbcToggleSpeciesDetail(id,state) {

	if (state)
		nbcDetailShowStates[id] = (state=='show');
	else
		nbcDetailShowStates[id] = nbcDetailShowStates[id] ? !nbcDetailShowStates[id] : true;
	
	if (nbcDetailShowStates[id]) {
		$('#det-'+id).css('display','block');
		$('#tog-'+id).attr('title',nbcLabelClose);
	} else {
		$('#det-'+id).css('display','none');
		$('#tog-'+id).attr('title',nbcLabelDetails);
	}
	
}

function nbcToggleAllSpeciesDetail() {
	
	var currHiding = ($('#showAllLabel').html()==nbcLabelShowAll);
	
	$('[id^="tog-"]').each(function(){
		nbcToggleSpeciesDetail($(this).attr('id').replace(/(tog-)/,''), currHiding ? 'show' : 'hide' );
	});

	if (currHiding)
		$('#showAllLabel').html(nbcLabelHideAll);
	else
		$('#showAllLabel').html(nbcLabelShowAll);

}




function filterEmergingCharacters()
{
	
	return;
	
	if (!settings.useEmergingCharacters) return;

	var charactersWithAnActiveState=Array();
	for(var i in nbcData.selectedStates)
	{
		charactersWithAnActiveState[nbcData.selectedStates[i].characteristic_id]=true;
	}

	for(var i in nbcData.menu.groups)
	{
		for (var j in nbcData.menu.groups[i].chars)
		{
			var char=nbcData.menu.groups[i].chars[j];
			var id=char.id;

			/*
				[ disabling non-distinctive characters ]

				we are setting disabled to false regardless of the
				remaining number of states:
				
				1. if there was no count per character in the data set
				(if (nbcData.countPerCharacter))
				
				2. for types other than text or media (i.e., for are
				"free-entry types", 'range' etc.)
				(char.type=='media' || char.type=='text')
				
				3. if the character has states that are already selected
				(charactersWithAnActiveState[id]!==true)

				next, characters are disabled when:

				4. within it, there are no selectable states left
				(nbcData.countPerCharacter[id]==undefined)
				
				5. the total taxon count for all its states taken is
				together is smaller than the current result set. put
				differently: there are remaining species that have
				no defined state for this particular character
				(nbcData.countPerCharacter[id]<nbcData.results.length)
				
				6. there is only one state left in a character, even
				if there is more than one species that "has" it.
				choosing the one last state would leave the size of
				the result set unchanged, and is therefore no
				longer distinctive.
				(nbcData.countPerCharacter[id].distinct_state_count<=1)

			*/
			
			if (nbcData.countPerCharacter && 
				(char.type=='media' || char.type=='text') && 
				charactersWithAnActiveState[id]!==true)
			{
				nbcData.menu.groups[i].chars[j].emergent_disabled=
					(
					nbcData.countPerCharacter[id]==undefined || 
					nbcData.countPerCharacter[id].taxon_count< (nbcData.results.length ? nbcData.results.length : 0) ||
					nbcData.countPerCharacter[id].distinct_state_count<=1
					);
			} 
			else
			{
				nbcData.menu.groups[i].chars[j].emergent_disabled=null;
			}
			
			//nbcData.menu.groups[i].chars[j].label=
			//	 nbcData.menu.groups[i].chars[j].label+'::'+nbcData.menu.groups[i].chars[j].id;
		}
	}

}

function prettyPhotoInit()
{
 	$("a[rel^='prettyPhoto']").prettyPhoto({
		allow_resize:true,
		animation_speed:50,
 		opacity: 0.70, 
		show_title: false,
 		overlay_gallery: false,
 		social_tools: false
 	});

}

function bindDialogKeyUp()
{
    $("#state-value").keydown(function(event)
	{
        // Allow: backspace, delete, tab, escape, and enter
        if (event.keyCode==46 || event.keyCode==8 || event.keyCode==9 || event.keyCode==27 || event.keyCode==13 || 
             // Allow: Ctrl+A
            (event.keyCode==65 && event.ctrlKey===true) || 
             // Allow: home, end, left, right
            (event.keyCode>=35 && event.keyCode<=39))
		{
			// let it happen, don't do anything
			return;
        }
        else
		{
			// Ensure that it is a number and stop the keypress
			if (event.shiftKey || (event.keyCode<48 || event.keyCode>57) && (event.keyCode<96 || event.keyCode>105))
			{
				event.preventDefault(); 
			}   
        }
    });

	$('#state-value').keyup(function(e)
	{
		if (e.keyCode==13)
		{
			// return
			setStateValue();
			closeDialog();
		}
		return;
	});

}

function jDialogOk()
{
	// dialog button function, called from main.js::showDialog 
	setStateValue();
	closeDialog();
}

function jDialogCancel()
{
	// dialog button function, called from main.js::showDialog 
	closeDialog();
}

function matrixInit()
{
	nbcLabelClose = __('sluiten');
	nbcLabelDetails = __('onderscheidende kenmerken');
	nbcLabelBack = __('terug');
	nbcLabelSimilarSpecies = __('gelijkende soorten');
	nbcLabelShowAll = __('alle kenmerken tonen');
	nbcLabelHideAll = __('alle kenmerken verbergen');
	nbcLabelExternalLink = __('Meer informatie over soort/taxon');

	$('#legendDetails').html(nbcLabelDetails);
	$('#legendSimilarSpecies').html(nbcLabelSimilarSpecies);
	$('#legendExternalLink').html(nbcLabelExternalLink);


	nbcPreviousBrowseStyles.paginate=settings.paginate;
	nbcPreviousBrowseStyles.expand=settings.expandResults;

	/*
	if ("ontouchstart" in document) {
		// touch only code (tablets)
		$('#legendDivider').removeClass('hidden'); // show icon legend
		$('#legendContainer').removeClass('hidden'); // show icon legend
	} else {
		// "desktop" code
	}
	*/

}


function setLastScrollPos()
{
	lastScrollPos=getPageScroll();
}

function getLastScrollPos()
{
	return lastScrollPos;
}

function setLastShowing()
{
	lastShowing=settings.expandedShowing;
}

function getLastShowing()
{
	return lastShowing;
}

function setScores(scores)
{
	data.scores=scores;
}

function getScores()
{
	return data.scores;
}

function setStates(states)
{
	data.states=states;
}

function getStates()
{
	return data.states;
}

function setDataSet(dataset)
{
	data.dataset=dataset;
}

function getDataSet()
{
	return data.dataset;
}

function setResultSet(resultset)
{
	data.resultset=resultset;
}

function getResultSet()
{
	return data.resultset;
}

function setMenu(menu)
{
	data.menu=menu;
}

function getMenu()
{
	return data.menu;
}

function setRelated(related)
{
	data.related=related;
}

function getRelated()
{
	return data.related;
}


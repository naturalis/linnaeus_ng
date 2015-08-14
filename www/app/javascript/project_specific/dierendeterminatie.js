var nbcBrowseStyle='paginate';
var nbcStart = 0;
var nbcExpandedShowing = 0;
var nbcExpandedPrevious = null;
var nbcPerPage = 16;	// default, reset in identify.php
var nbcPerLine = 4;		// default, reset in identify.php
var nbcData;
var nbcFullDatasetCount = 0;
var nbcCurrPage = 0;
var nbcLastPage = 0;
var nbcImageRoot;
var nbcPaginate = true;
var nbcExpandResults = false;
var nbcStatevalue = '';
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
var nbcUseEmergingCharacters=true;

function nbcGetResults(p)
{
	setCursor('wait');
	//console.dir({ action : 'get_results_nbc', params : p, time : getTimestamp(), key : matrixId, p : projectId });

	allAjaxHandle = $.ajax({
		url : 'ajax_interface.php',
		type: 'POST',
		data : ({
			action : 'get_results_nbc',
			params : p,
			time : getTimestamp(),
			key : matrixId,
			p : projectId
		}),
		success : function (data)
		{
			//console.log(data);
			nbcData = $.parseJSON(data);
			//console.dir(nbcData);
			nbcFilterEmergingCharacters();
			nbcDoResults();

			if (p && p.action!='similar') nbcDoOverhead();
			nbcDoPaging();
			if (p && p.action=='similar') nbcPrintSimilarHeader();
			if (p && p.closeDialog==true) jDialogCancel();
			if (p && p.refreshGroups==true) nbcRefreshGroupMenu();

			setCursor();
		}
	});

}

function nbcDoSearch()
{
	var str = $('#inlineformsearchInput').val();
	str = str.replace(/^\s+|\s+$/g, ''); 

	if (str.length==0) return false;

	nbcSearchTerm=str;
	nbcSetState({norefresh:true,clearState:true});
	
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


function nbcDoResults(p)
{
	if (p && p.resetStart!==false)
		nbcStart = 0;
	nbcExpandedShowing = 0;
	nbcClearResults();
	if (nbcData.results)
		nbcPrintResults();
	else 
		nbcRemoveShowMoreButton()
}

function nbcClearResults()
{
	$('#results-container').html('');
}

function nbcPrintResults()
{
	if (nbcExpandResults)
		nbcPrintResultsExpanded();
	else
		nbcPrintResultsPaginated(); // also for non-paginated, non-expanded

	nbcPrettyPhotoInit();
	nbcResetClearButton();	
}

function nbcPrintResultsPaginated()
{
	var results = nbcData.results;
	var s = '';
	var d = 0;

	s = '<div class="resultRow">';

	for(var i=0;i<results.length;i++)
	{
		if ((i>=nbcStart && i<nbcStart+nbcPerPage) || nbcPaginate==false)
		{
			s = s + nbcFormatResult(results[i]);
			if (++d==nbcPerLine)
			{
				s = s + '</div><br/><div class="resultRow">';
				d=0;
			}
		}
	}

	s = s + '</div>';
	
	nbcRemoveShowMoreButton();

	$('#results-container').html(s);
}

function nbcPrintResultsExpanded()
{
	var results = nbcData.results;
	var s = '';
	var added = d = 0;

	s = '<div class="resultRow">';

	for(var i=0;i<results.length;i++)
	{
		if ((nbcExpandedPrevious!=null && i<nbcExpandedPrevious) ||
			(i>=nbcExpandedShowing && i<nbcExpandedShowing+nbcPerPage)
			)
		{
			s = s + nbcFormatResult(results[i]);
			added++;
			if (++d==nbcPerLine)
			{
				s = s + '</div><br/><div class="resultRow">';
				d=0;
			}
		}
	}
	
	s = s + '</div>';

	if (nbcExpandedShowing>0) {
		var n = 'p'+rndStr();
		s = '<div id="'+n+'" style="display:none">'+s+'</div>';
	}

	$('#results-container').html($('#results-container').html()+s);

	if (nbcExpandedShowing>0)
		$('#'+n).show('normal');

	nbcExpandedShowing = nbcExpandedShowing + added;

	if (nbcExpandedShowing==added)
		nbcRemoveShowMoreButton();

	if (nbcExpandedShowing==added && nbcExpandedShowing < nbcData.count.results) {
		$("#paging-footer").append('<li id="show-more"><input type="button" id="show-more-button" onclick="nbcPrintResults();return false;" value="'+_('meer resultaten laden')+'" class="ui-button"></li>');
		$("#footerPagination").removeClass('noline').addClass('noline');
	}

	if (nbcExpandedShowing>added && nbcExpandedShowing >= nbcData.count.results)
		nbcRemoveShowMoreButton();

	nbcExpandedPrevious = null;
	
	nbcDoOverhead();
}

function nbcFormatResult(data)
{
	/*
		data.
			i : id 
			t : taxon id (absent when not a variation) 
			y : type: t(axon) or v(ariation) or m(atrix)
			l : label 
			c : taxon
			g : gender (absent when not a variation)
			s : scientific name 
			n : has image?
			m : image url
			x : generic image (only when n==false, and even then still optional)
			h : thumbnail
			p : photographer credit 
			u : remote url
			v : remote url target
			r : number of similars
			h : highlight (bool)
			d : full species details (only when comparing or resultset has only one taxon/variation)
    */
	
	//console.dir(data);
	
	var showDetails = nbcData.results.length <= nbcPerPage;

	if (data.l!=data.c && data.l.indexOf(data.c)===0) {
		data.l = data.c + ' (' + data.l.replace(data.c,'').replace(/(^\s|\s$)/,'') + ')';
	}

	var photoLabel = 
		'<div style="margin-left:130px">'+
		(data.s==data.l || !data.s ? '<i>'+(data.l)+'</i>' : data.l)+
		(data.g ? ' <img class="gender" height="17" width="8" src="'+nbcImageRoot+data.g+'.png" title="'+data.e+'" />' : '' )+
		(data.s!=data.l ? '<br /><i>'+(data.s)+'</i>' : '')+
		(data.p ? '<br />('+_('foto')+' &copy; '+(data.p)+')' : '')+'</div>';
	
	var id = data.y+'-'+data.i;

	if (showDetails && data.d) {

		var states = Array();

		for(var i in data.d) {
			
			if (data.d[i].characteristic==undefined)
				continue;
			
			var labels = Array();
			
			if (data.d[i].characteristic.indexOf('|')!=false) {
				var t = data.d[i].characteristic.split('|');
				t = t[0];
			} else {
				var t = data.d[i].characteristic;
			}
			
			for(var j in data.d[i].states)
				labels.push(data.d[i].states[j].label);

			if (labels.length>1)
				var l = labels.join('; ');
			else
				var l = labels[0];

			states.push('<span class="result-detail-label">'+t +':</span> <span class="result-detail-value">'+l+'</span>');
		}
		
	}
	
	var showStates = states && states.length > 0;

	if (data.n) {
		if (!data.m.match(/^(http:\/\/|https:\/\/)/i)) data.m = baseUrlProjectImages + data.m;
	} else {
		if (data.x) data.m = data.x;
	}

 	return '<div class="result'+(data.h ? ' result-highlight' : '')+'" id="res-'+id+'"> \
        <div class="result-result"> \
			<div class="result-image-container">'+
				(data.n ? '<a rel="prettyPhoto[gallery]" href="'+data.m+'" pTitle="'+escape(photoLabel)+'" title="">' : '')+
				'<img class="result-image" src="'+data.m+'" title="'+(data.p ? _('foto')+' &copy;'+data.p : '')+'" />' +
				(data.n ? '</a>' : '' )+
            '</div> \
			<div class="result-labels">'+
				(data.g ? '<img class="result-gender-icon" src="'+nbcImageRoot+data.g+'.png" title="'+(data.e ? data.e : '')+'" />' : '' )+
                '<span class="result-name-scientific">'+data.s+'</span> '+
				(data.y=='m'? '<br /><a href="?mtrx='+data.i+'">'+_('Ga naar sleutel')+'</a>' : '' )+
                '<span class="result-name-common">'+(data.s!=data.l ? '<br />' + data.l : '')+'</span> \
            </div> \
        </div> \
        <div class="result-icons"> \
			<div class="result-icon'+( data.u ? '' : ' no-content')+'"'+
				( data.u ? 
					' onclick="window.open(\''+data.u+'\',\''+data.v+'\');" title="'+nbcLabelExternalLink+'"'+
					' onmouseover="nbcSwitchImagename(this,1)" onmouseout="nbcSwitchImagename(this)"' : '' )+'>'+
				(data.u ? '<img class="result-icon-image" src="'+nbcImageRoot+'information_grijs.png">' : '' ) +
			'</div> \
			<div class="result-icon'+( showStates ? '' : ' no-content')+'" id="tog-'+id+'" '+
				( showStates ?
					' onclick="nbcToggleSpeciesDetail(\''+id+'\');return false;" title="'+nbcLabelDetails+'"' +
					' onmouseover="nbcSwitchImagename(this,1)" onmouseout="nbcSwitchImagename(this)"': '' )+'>'+
				(showStates ? '<img class="result-icon-image icon-info" src="'+nbcImageRoot+'lijst_grijs.png">' : '' ) +
			'</div> \
			<div class="result-icon'+( data.r ? '' : ' no-content')+'" '+
				( data.r ? 
					' onclick="nbcShowSimilar('+(data.i)+',\''+(data.t ? 'v' : 't')+'\');return false;"  title="'+nbcLabelSimilarSpecies+'"' +
					' onmouseover="nbcSwitchImagename(this,1)" onmouseout="nbcSwitchImagename(this)"' : '' )+'>'+
				( data.r ? '<img class="result-icon-image icon-similar" src="'+nbcImageRoot+'gelijk_grijs.png">' : '' ) +
			'</div> \
        </div>'+
		(states && states.length > 0 ? 
			'<div id="det-'+id+'" class="result-detail hidden"> \
				<ul> \
					<li>'+states.join('</li><li>')+'</li> \
				</ul> \
			</div> ' : '' ) + '\
    </div> \
	';

}

function nbcSwitchImagename(ele,state) {
	var p = '_grijs';
	$(ele).find('img').attr('src',state==1 ? $(ele).find('img').attr('src').replace(p,'') : $(ele).find('img').attr('src').replace('.png',p+'.png'));	
}

function nbcResetClearButton() {
	if (nbcData.paramCount==0) {
		$('#clearSelectionContainer').removeClass('ghosted').addClass('ghosted');
	} else {
		$('#clearSelectionContainer').removeClass('ghosted');
	}
}

function nbcRemoveShowMoreButton() {
	$("#show-more").remove();
	$("#footerPagination").removeClass('noline');	
}


function nbcDoOverhead() {
	nbcClearOverhead();
	if (nbcData.count) nbcPrintOverhead();
}

function nbcClearOverhead() {
	$('#result-count').html('');
	$('#similarSpeciesHeader').removeClass('visible').addClass('hidden');
	$('#similarSpeciesHeader').html('');
}

function nbcPrintOverhead() {

	if (nbcBrowseStyle=='expand') {

		$('#result-count').html((nbcExpandedShowing > 1 ? '1-'+nbcExpandedShowing : nbcExpandedShowing)+'&nbsp;'+_('van')+'&nbsp;'+nbcData.count.results);
		return;

	}

	var count = nbcData.count;
	
	nbcFullDatasetCount = (nbcFullDatasetCount==0) ? count.all : nbcFullDatasetCount;
	
	$('#result-count').html(
		sprintf(
			'<strong style="color:#333">%s</strong> %s',
			count.results,
			sprintf(
				_(' van %s '),
					sprintf(
						'<strong style="color:#777;">%s</strong>',
						nbcFullDatasetCount
					)
				)
			)
		);
}

function nbcDoPaging() {
	nbcClearPaging();
	if (nbcData.count) nbcPrintPaging();
}

function nbcClearPaging() {

	if (!nbcPaginate) return;

	$('#paging-header').html('');	
	$('#paging-footer').html('');	
}

function nbcPrintPaging() {

	if (!nbcPaginate) return;

	var count = nbcData.count;

	nbcLastPage = Math.ceil(count.results / nbcPerPage);
	nbcCurrPage = Math.floor(nbcStart / nbcPerPage);

	if (nbcLastPage > 1 && nbcCurrPage!=0)
		$("#paging-header").append('<li><a href="#" onclick="nbcBrowse(\'p\');return false;">&lt;&lt;</a></li>');
	
	if (nbcLastPage>1) { 
	
		for (var i=0;i<nbcLastPage;i++) {
	
			if (i==nbcCurrPage)
				$("#paging-header").append('<li><strong>'+(i+1)+'</strong></li>');
		    else
				$("#paging-header").append('<li><a href="#" onclick="nbcBrowse('+i+');return false;">'+(i+1)+'</a></li>');
	
		}
		
	}

	if (nbcLastPage > 1 && nbcCurrPage<nbcLastPage-1)
		$("#paging-header").append('<li><a href="#" onclick="nbcBrowse(\'n\');return false;" class="last">&gt;&gt;</a></li>');

	$("#paging-footer").html($("#paging-header").html());
}

function nbcShowSimilar(id,type) {

	nbcPreviousBrowseStyles.paginate = nbcPaginate;
	nbcPreviousBrowseStyles.expand = nbcExpandResults;
	nbcPreviousBrowseStyles.expandShow = nbcExpandedShowing;
	nbcPreviousBrowseStyles.expandPrev = nbcExpandedPrevious;
	nbcPreviousBrowseStyles.lastPos = getPageScroll();

	nbcSetPaginate(false);
	nbcSetExpandResults(false);
	nbcGetResults({action:'similar',id:id,type:type,refreshCount:false});
	nbcSaveSessionSetting('nbcSimilar',[id,type]);

}

function nbcPrintSimilarHeader()
{

	if (!nbcData.results) return;

	var label = nbcData.results[0].l;

	$('#similarSpeciesHeader').html(
		sprintf(_('Gelijkende soorten van %s'),'<span id="similarSpeciesName">'+label+'</span>')+
		'<br />'+
		'<a class="clearSimilarSelection" href="#" onclick="nbcCloseSimilar();return false;">'+nbcLabelBack+'</a>'+
		'<span id="show-all-divider"> | </span>'+
		'<a class="clearSimilarSelection" href="#" onclick="nbcToggleAllSpeciesDetail();return false;" id="showAllLabel">'+nbcLabelShowAll+'</a>'
	);
	$('#similarSpeciesHeader').removeClass('hidden').addClass('visible');

	var t=$('.icon-info:visible').not('.legend-icon-image').length!=0;
	$('#showAllLabel').toggle(t);
	$('#show-all-divider').toggle(t);

}

function nbcCloseSimilar() {

	nbcSetPaginate(nbcPreviousBrowseStyles.paginate);
	nbcSetExpandResults(nbcPreviousBrowseStyles.expand);
	nbcExpandedShowing = nbcPreviousBrowseStyles.expandShow;
	nbcExpandedPrevious = nbcPreviousBrowseStyles.expandPrev;

	nbcGetResults();
	nbcClearOverhead();
	nbcSaveSessionSetting('nbcSimilar');

	window.scroll(0,nbcPreviousBrowseStyles.lastPos);
	
}


function nbcClearSearchTerm() {
	
	nbcSearchTerm='';
	$('#inlineformsearchInput').val('');

}

function nbcCloseSearch() {

	nbcSetPaginate(nbcPreviousBrowseStyles.paginate);
	nbcSetExpandResults(nbcPreviousBrowseStyles.expand);
	nbcExpandedShowing = nbcPreviousBrowseStyles.expandShow;
	nbcExpandedPrevious = nbcPreviousBrowseStyles.expandPrev;

	$('#inlineformsearchInput').val('');

	nbcGetResults();
	nbcClearOverhead();
	nbcSaveSessionSetting('nbcSearch');

	window.scroll(0,nbcPreviousBrowseStyles.lastPos);

}

function nbcPrintSearchHeader() {

	$('#similarSpeciesHeader').html(
		sprintf(_('Zoekresultaten voor %s'),'<span id="searchedForTerm">'+nbcSearchTerm+'</span>')+'<br />'+
		'<a class="clearSimilarSelection" href="#" onclick="nbcCloseSearch();return false;">'+nbcLabelBack+'</a>'
	);
	$('#similarSpeciesHeader').removeClass('hidden').addClass('visible');
}



function nbcBrowse(id) {

	if (id=='n')
	    nbcStart = nbcStart+nbcPerPage;
	else 
	if (id=='p')
    	nbcStart = nbcStart-nbcPerPage;
	else
		nbcStart = id * nbcPerPage;
			
	nbcSaveSessionSetting('nbcStart',nbcStart);
	nbcClearResults();
	nbcPrintResults();
	nbcClearPaging();
	nbcPrintPaging();

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

function nbcToggleGroup(id) {

	if ($('#character-group-'+id).css('display')=='none') {
		$('#character-group-'+id).removeClass('hidden').addClass('visible');
		$('#character-item-'+id).removeClass('closed').addClass('open');
	} else {
		$('#character-group-'+id).removeClass('visible').addClass('hidden');
		$('#character-item-'+id).removeClass('open').addClass('closed');
	}
	
} 

function nbcShowStates(id) {

	setCursor('wait');
	
	allAjaxHandle = $.ajax({
		url : 'ajax_interface.php',
		type: 'POST',
		data : ({
			action : 'get_formatted_states' ,
			id : id , 
			time : getTimestamp(),
			key : matrixId,
			p : projectId
		}),
		success : function (data) {
			//console.log(data);
			data = $.parseJSON(data);
			showDialog(
				data.character.label,
				data.page,
				{width:data.width,height:data.height,showOk:data.showOk}
			);
			setCursor();
		}
	});

}

function nbcRefreshGroupMenu() {

	if (nbcData.menu) nbcBuildGroupMenu(nbcData.menu);
	
}

function nbcBuildGroupMenu(data) {

	$('#facet-categories-menu').html('');
	
	var d = Array();

	for (var i in data.groups) {

		var v = data.groups[i];

		var openGroup = data.groups.length==1 ? true : false;

		if (v.type=='group') {

			var s = 
				'<li id="character-item-'+v.id+'" class="closed"><a href="#" onclick="nbcToggleGroup('+v.id+');return false;">'+v.label+'</a></li>'+
				'<ul id="character-group-'+v.id+'" class="hidden">';			
	
			for (var j in v.chars) {
	
				var c = data.groups[i].chars[j];
	
				if (c.disabled===true)
				{
					s=s+'<li class="inner'+(j==(v.chars.length-1)?' last':'')+' disabled">'+c.label+(c.value?' '+c.value:'');
				}
				else
				if (c.emergent_disabled==true)
				{
					s=s+'<li class="inner'+(j==(v.chars.length-1)?' last':'')+'" title="'+_( "Dit kenmkerk is bij de huidige selectie niet onderscheidend." )+'"> \
						<a class="facetLink emergent_disabled" href="#" onclick="nbcShowStates('+c.id+');return false;">('+
							c.label+(c.value?' '+c.value:'')+
						')</a>';
				}
				else
				{
					s=s+'<li class="inner'+(j==(v.chars.length-1)?' last':'')+'"> \
							<a class="facetLink" href="#" onclick="nbcShowStates('+c.id+');return false;">'+
								c.label+(c.value?' '+c.value:'')+
							'</a>';
				}
					
				if (data.activeChars[c.id]) {
					openGroup = true;
					s = s + '<span>';
					for (k in data.storedStates) {
						var state = data.storedStates[k];
						if (state.characteristic_id==c.id) {
							var dummy = state.type=='f' ? state.type+':'+state.characteristic_id : state.val;
							s = s + 
								'<div class="facetValueHolder">'+
									(state.value ? state.value+' ' : '')+
									(state.label ? state.label+' ' : '')+
									(state.separationCoefficient ? ' ('+state.separationCoefficient+') ' : '')+
									'<a href="#" class="removeBtn" onclick="nbcClearStateValue(\''+dummy+'\');return false;">'+
									'<img src="'+nbcImageRoot+'clearSelection.gif">'+
									'</a>'+
								'</div>';
	
						}
					}
					
					s = s + '</span>';
	
				}
	
				s = s  +'</li>';
	
			}
	
			s = s  +'</ul>';
			
			if (openGroup)
				s = s + '<script> \n nbcToggleGroup('+v.id+'); \n </script>';
					
			d.push(s);

		} else {
			
			var c = v;

			s = '<li class="inner ungrouped last"><a class="facetLink" href="#" onclick="nbcShowStates('+c.id+');return false;">'+c.label+(c.value ? ' '+c.value : '')+'</a>';
			
			if (data.activeChars[c.id]) {
				openGroup = true;
				s = s + '<span>';
				for (k in data.storedStates) {
					var state = data.storedStates[k];
					if (state.characteristic_id==c.id) {
						var dummy = state.type=='f' ? state.type+':'+state.characteristic_id : state.val;
						s = s + 
							'<div class="facetValueHolder">'+
								(state.value ? state.value+' ' : '')+
								(state.label ? state.label+' ' : '')+
								(state.separationCoefficient ? ' ('+state.separationCoefficient+') ' : '')+
								'<a href="#" class="removeBtn" onclick="nbcClearStateValue(\''+dummy+'\');return false;">'+
								'<img src="'+nbcImageRoot+'clearSelection.gif">'+
								'</a>'+
							'</div>';

					}
				}
				
				s = s + '</span>';

			}

			s = s  +'</li>';
			
			d.push(s);


		}

	}
	
	$('#facet-categories-menu').html('<ul>'+d.join('\n')+'</ul>');
	
}



function nbcSaveSessionSetting(name,value) {

	allAjaxHandle = $.ajax({
		url : 'ajax_interface.php',
		type: 'POST',
		data : ({
			action : 'save_session_setting' ,
			setting : { name : name, value: value },
			id : null,
			time : getTimestamp(),
			key : matrixId,
			p : projectId
		}),
		success : function (data) {
			//alert(data);
		}
	});
	
}

function nbcSetState(p) {
	
	//nbcSetPaginate(true);
	
	setCursor('wait');

	allAjaxHandle = $.ajax({
		url : 'ajax_interface.php',
		type: 'POST',
		data : ({
			action : (p && p.clearState) ? 'clear_state' : 'set_state' ,
			state : p.state,
			value : p.value,
			id : null,
			time : getTimestamp(),
			key : matrixId,
			p : projectId
		}),
		success : function (data) {
			if (p.norefresh!==true)
				nbcGetResults({closeDialog:true,refreshGroups:true});
			setCursor();
		}
	});
	
}

function nbcSetStateValue(state) {
	
	var state = state ? state : $('#state-id').html();

	nbcSetState({state:state,value:nbcStatevalue});
		
}

function nbcClearStateValue(state) {

	$('#state-value').val('');
	nbcSetState({state:state,clearState:true});
		
}

function nbcBindDialogKeyUp() {

    $("#state-value").keydown(function(event) {
        // Allow: backspace, delete, tab, escape, and enter
        if (event.keyCode==46 || event.keyCode==8 || event.keyCode==9 || event.keyCode==27 || event.keyCode==13 || 
             // Allow: Ctrl+A
            (event.keyCode==65 && event.ctrlKey===true) || 
             // Allow: home, end, left, right
            (event.keyCode>=35 && event.keyCode<=39)) {
                 // let it happen, don't do anything
                 return;
        }
        else {
            // Ensure that it is a number and stop the keypress
            if (event.shiftKey || (event.keyCode<48 || event.keyCode>57) && (event.keyCode<96 || event.keyCode>105)) {
                event.preventDefault(); 
            }   
        }
    });

	$('#state-value').keyup(function(e) {
		if (e.keyCode==13) {
			// return
			nbcSetStateValue();
		}
		return;
	});

}

function nbcPrettyPhotoInit() {

 	$("a[rel^='prettyPhoto']").prettyPhoto({
		allow_resize:true,
		animation_speed:50,
 		opacity: 0.70, 
		show_title: false,
 		overlay_gallery: false,
 		social_tools: false
 	});

}

function nbcSetPaginate(state) {

	nbcPaginate = state;
	
}

function nbcSetExpandResults(state) {

	nbcExpandResults = state;
	
}

function nbcFilterEmergingCharacters()
{
	if (nbcUseEmergingCharacters==false) return;
	if (!nbcData.results) return;

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
					nbcData.countPerCharacter[id].taxon_count< nbcData.results.length ||
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

// dialog button function, called from main.js::showDialog 
function jDialogOk() {

	nbcSetStateValue();

}

// dialog button function, called from main.js::showDialog 
function jDialogCancel() {

	closeDialog();

}

function nbcInit() {

	nbcLabelClose = _('sluiten');
	nbcLabelDetails = _('onderscheidende kenmerken');
	nbcLabelBack = _('terug');
	nbcLabelSimilarSpecies = _('gelijkende soorten');
	nbcLabelShowAll = _('alle kenmerken tonen');
	nbcLabelHideAll = _('alle kenmerken verbergen');
	nbcLabelExternalLink = _('Meer informatie over soort/taxon');

	$('#legendDetails').html(nbcLabelDetails);
	$('#legendSimilarSpecies').html(nbcLabelSimilarSpecies);
	$('#legendExternalLink').html(nbcLabelExternalLink);


	if (nbcBrowseStyle=='paginate') {
		nbcSetPaginate(true);
		nbcSetExpandResults(false);
	} else
	if (nbcBrowseStyle=='expand') {
		nbcSetPaginate(false);
		nbcSetExpandResults(true);
	}

	nbcPreviousBrowseStyles.paginate=nbcPaginate;
	nbcPreviousBrowseStyles.expand=nbcExpandResults;

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

// alleen bijen
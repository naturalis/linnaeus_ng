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
function __(text)
{
	// _() is sloooooow!
	return text;
	return _(text);
}

var resultsHtmlTpl = '<div class="resultRow">%RESULTS%</div>';
var noResultHtmlTpl='<div style="margin-top:10px">%MESSAGE%</div>';
var resultsLineEndHtmlTpl = '</div><br/><div class="resultRow">';
var brHtmlTpl = '<br />';

var photoLabelHtmlTpl = ' \
<div style="margin-left:130px"> \
	%SCI-NAME% \
	%GENDER% \
	%COMMON-NAME% \
	%PHOTO-DETAILS% \
</div> \
';

//var photoLabelGenderHtmlTpl = '<img class="gender" height="17" width="8" src="%IMG-SRC%" title="%GENDER-LABEL%" />';
var photoLabelGenderHtmlTpl = '';

var photoLabelPhotographerHtmlTpl = '<br />(%PHOTO-LABEL% %PHOTOGRAPHER%)';

var imageHtmlTpl = '\
<a rel="prettyPhoto[gallery]" href="%IMAGE-URL%" pTitle="%PHOTO-LABEL%" title=""> \
	<img class="result-image" src="%THUMB-URL%" title="%PHOTO-CREDIT%" /> \
</a>\
';

//var genderHtmlTpl = '<img class="result-gender-icon" src="%ICON-URL%" title="%GENDER-LABEL%" />';
var genderHtmlTpl = '';

var matrixLinkHtmlTpl = '<br /><a href="?mtrx=%MATRIX-ID%">%MATRIX-LINK-TEXT%</a>';

var remoteLinkClickHtmlTpl = 'onclick="window.open(\'%REMOTE-LINK%\',\'_blank\');" title="%TITLE%"';

var statesClickHtmlTpl = 'onclick="toggleDetails(\'%LOCAL-ID%\');return false;"title="%TITLE%"';

var relatedClickHtmlTpl = 'onclick="setSimilar({id:%ID%,type:\'%TYPE%\'});return false;" title="%TITLE%"';

var statesHtmlTpl = '\
<div id="det-%LOCAL-ID%" class="result-detail hidden"> \
	<ul> \
		<li>%STATES%</li> \
	</ul> \
</div> \
';

var statesJoinHtmlTpl = '</li><li>';

var speciesStateItemHtmlTpl = '<span class="result-detail-label">%GROUP% %CHARACTER%:</span> <span class="result-detail-value">%STATE%</span>';

var resultHtmlTpl = '\
<div class="result%CLASS-HIGHLIGHT%" id="res-%LOCAL-ID%"> \
	<div class="result-result"> \
		<div class="result-image-container"> \
			%IMAGE-HTML% \
		</div> \
		<div class="result-labels"> \
			%GENDER% \
			<span class="result-name-scientific" title="%SCI-NAME-TITLE%">%SCI-NAME%</span> \
			%MATRIX-LINK% \
			<span class="result-name-common" title="%COMMON-NAME-TITLE%"><br />%COMMON-NAME%</span> \
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

var resultBatchHtmlTpl= '<span class=result-batch style="%STYLE%">%RESULTS%</span>' ;
var buttonMoreHtmlTpl='<li id="show-more"><input type="button" id="show-more-button" onclick="printResults();return false;" value="%LABEL%" class="ui-button"></li>';
var counterExpandHtmlTpl='%START-NUMBER%%NUMBER-SHOWING%&nbsp;%FROM-LABEL%&nbsp;%NUMBER-TOTAL%';
var pagePrevHtmlTpl='<li><a href="#" onclick="browsePage(\'p\');return false;">&lt;</a></li>';
var pageCurrHtmlTpl='<li><strong>%NR%</strong></li>';
var pageNumberHtmlTpl='<li><a href="#" onclick="browsePage(%INDEX%);return false;">%NR%</a></li>';
var pageNextHtmlTpl='<li><a href="#" onclick="browsePage(\'n\');return false;" class="last">&gt;</a></li>';
var counterPaginateHtmlTpl=' %FIRST-NUMBER%-%LAST-NUMBER% %NUMBER-LABEL% %NUMBER-TOTAL%';

var menuOuterHtmlTpl ='<ul>%MENU%</ul>';

var menuGroupHtmlTpl = '\
<li id="character-item-%ID%" class="closed"><a href="#" onclick="toggleGroup(%ID%);return false;">%LABEL%</a></li> \
<ul id="character-group-%ID%" class="hidden"> \
	%CHARACTERS% \
</ul> \
';

var menuLoneCharHtmlTpl='\
<li class="inner ungrouped last"> \
	<a class="facetLink" href="#" onclick="showStates(%ID%);return false;">%LABEL%%VALUE%</a> \
	%SELECTED% \
</li> \
';
var menuLoneCharDisabledHtmlTpl='<li class="inner ungrouped %CLASS% disabled" title="%TITLE%" ondblclick="showStates(%ID%);">%LABEL%%VALUE%	%SELECTED% </li>';
var menuLoneCharEmergentDisabledHtmlTpl='\
<li class="inner ungrouped %CLASS%" title="%TITLE%"> \
	<a class="facetLink emergent_disabled" href="#" onclick="showStates(%ID%);return false;">(%LABEL%%VALUE%)</a> \
	%SELECTED% \
</li> \
';
var menuCharHtmlTpl=menuLoneCharHtmlTpl.replace('ungrouped ','');
var menuCharDisabledHtmlTpl=menuLoneCharDisabledHtmlTpl.replace('ungrouped ','');
var menuCharEmergentDisabledHtmlTpl=menuLoneCharEmergentDisabledHtmlTpl.replace('ungrouped ','');

var menuSelStateHtmlTpl = '\
<div class="facetValueHolder"> \
%VALUE% %LABEL% %COEFF% \
<a href="#" class="removeBtn" onclick="clearStateValue(\'%STATE-ID%\');return false;"> \
<img src="%IMG-URL%"></a> \
</div> \
';
var menuSelStatesHtmlTpl = '<span>%STATES%</span>';

var iconInfoHtmlTpl='<img class="result-icon-image icon-info" src="%IMG-URL%">';
var iconUrlHtmlTpl = iconInfoHtmlTpl.replace(' icon-info','');
var iconSimilarTpl = iconInfoHtmlTpl.replace(' icon-info',' icon-similar');

var similarHeaderHtmlTpl='\
%HEADER-TEXT% <span id="similarSpeciesName">%SPECIES-NAME%</span> <span class="result-count">(%NUMBER-START%-%NUMBER-END%)</span> \
<br /> \
<a class="clearSimilarSelection" href="#" onclick="closeSimilar();return false;">%BACK-TEXT%</a> \
<span id="show-all-divider"> | </span> \
<a class="clearSimilarSelection" href="#" onclick="toggleAllDetails();return false;" id="showAllLabel">%SHOW-STATES-TEXT%</a> \
';

var searchHeaderHtmlTpl='\
%HEADER-TEXT% <span id="similarSpeciesName">%SEARCH-TERM%</span> <span class="result-count">(%NUMBER-START%-%NUMBER-END% %OF-TEXT% %NUMBER-TOTAL%)</span> \
<br /> \
<a class="clearSimilarSelection" href="#" onclick="closeSearch();return false;">%BACK-TEXT%</a> \
';

var settings={
	matrixId: 0,
	projectId: 0,
	perPage: 16,
	perLine: 4,
	start: 0,
	expandedShowing: 0,
	expandResults: true,
	useEmergingCharacters: true,
	showSpeciesDetails: true,
	alwaysShowDetails: false,
	imageRootSkin: "",  // for skin images (icons and such)
	imageRootProject: "",  // for local project images
	imageOrientation: "portrait",
	defaultSpeciesImages: {},
	defaultSpeciesImage: "",
	browseStyle: 'paginate', // expand, paginate, show_all
	expandedPrevious: 0,
	paginate: true,
	currPage: 0,
	lastPage: 0,
	scoreThreshold: 0,
	mode: "identify", // similar, search
	groupsAlwaysOpen: false
};

var data={
	menu: Array(), // full menu
	dataset: Array(), // full dataset
	resultset: Array(), // result subset
	states: {}, // user-selected states
	characters: {}, // remaining states/taxa per character
	scores: {}, // match scores based on selection
	related: {}, // related species
	found: {} // search results
}

var prevSettings={};

var initialize=true;
var lastScrollPos=0;
var tempstatevalue="";
var openGroups=Array();
var searchedfor="";


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
		success : function ( d )
		{
			setDataSet($.parseJSON( d ));
			applyScores();
			clearResults();
			printResults();
			setCursor();
			
			if (initialize)
			{
				initialize=false;
				retrieveMenu();
			}
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
			setMenu($.parseJSON(data));
			printMenu();
			setCursor();
		}
	});
}

function resetMatrix()
{
	clearStateValue();
	openGroups.splice(0,openGroups.length);
	closeSimilar();
	closeSearch();
	printCountHeader();
}

function printResults()
{
	var resultset = getResultSet();

	// pre-emptively remove show_more-button (clicking similar automatically switches to browseStyle='show_all')
	$("#show-more").remove();
	$("#footerPagination").removeClass('noline');

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
	
	if (resultset.length==0)
	{
		$('#results-container').html(noResultHtmlTpl.replace('%MESSAGE%',__('Geen resultaten.')));
	}

	clearOverhead();
	printHeader();
	prettyPhotoInit();
	disableImgContextMenu();

	$('.result-icon').on('mouseover',function()
	{	
		$(this).find('img').attr('src', $(this).find('img').attr('src') ? $(this).find('img').attr('src').replace('_grijs','')  : "" );
	}).on('mouseout',function()
	{
		$(this).find('img').attr('src', $(this).find('img').attr('src') ? $(this).find('img').attr('src').replace('.png','_grijs.png')  : "" );
	});
}

function shouldDisableChar( id )
{
	/*	
	if the character has states that are already selected, we don't disable
	*/
	var activestates=getActiveStates(id);
	if (activestates && activestates.length>=1) return false;


	/*
	if there is no or just one taxon left that "has" a state from this character, we disable
	*/
	var charactercounts=getCharacterCounts(id);
	return (charactercounts.distinct_state_count<=1);
}

function shouldDisableEmergentChar( id )
{
	/*
	usage of emergent characters can be turned off in project settings
	*/
	if (!settings.useEmergingCharacters) return false;
	
	/*
	types other than text or media (i.e., "free-entry types", 'range' etc.)
	can never be emergent
	*/
	var character=getCharacter(id);
	if (character.prefix!="c") return false
	
	/*
	the distinct taxon count for all its states that are  still available is
	smaller than the current result set. put differently: there are remaining
	species that have no defined state for this particular character.
	*/
	var charactercounts=getCharacterCounts(id);
	return charactercounts.taxon_count < getResultSet().length;
}

function printMenu()
{
	$('#facet-categories-menu').html('');
	
	var menu=getMenu();
	var buffer=Array();
	var groupcount=0;
	var lastgroupid;

	for (var i in menu)
	{
		var item = menu[i];

		var s="";
		
		if (item.type=='group')
		{
			groupcount++;
			lastgroupid=item.id;			

			var c="";

			for (var j in item.chars)
			{
				if (settings.groupsAlwaysOpen) openGroups.push(item.id);
				
				var char=item.chars[j];

				char.disabled = shouldDisableChar(char.id);
				char.emergent_disabled = shouldDisableEmergentChar(char.id);

				var activestates=getActiveStates(char.id);

				var l=""

				if (activestates)
				{
					openGroups.push(item.id);

					var t="";
					for (var k in activestates)
					{
						var state = activestates[k];
						t=t+menuSelStateHtmlTpl
							.replace('%VALUE%',(state.value ? state.value : ''))
							.replace('%LABEL%',(state.label ? state.label : ''))
							.replace('%COEFF%',(state.separationCoefficient ? '('+state.separationCoefficient+') ' : ''))
							.replace('%STATE-ID%',state.val)
							.replace('%IMG-URL%',settings.imageRootSkin+'clearSelection.gif');
					}
					
					l=menuSelStatesHtmlTpl.replace('%STATES%',t);
				}

				if (char.disabled==true)
				{
					c=c+menuCharDisabledHtmlTpl
						.replace('%CLASS%',(j==(item.chars.length-1)?' last':''))
						.replace('%ID%',char.id)
						.replace('%LABEL%',char.label) //  + ':' + charactercounts.taxon_count
						.replace('%TITLE%',__( "Dit kenmkerk is bij de huidige selectie niet langer onderscheidend." ))
						.replace('%VALUE%',(char.value?' '+char.value:''))
						.replace('%SELECTED%',l);
				}
				else
				if (char.emergent_disabled==true)
				{

					//var charactercounts=getCharacterCounts(char.id);
					//console.log(char.label,charactercounts.taxon_count);
					
					c=c+menuCharEmergentDisabledHtmlTpl
						.replace('%CLASS%',(j==(item.chars.length-1)?' last':''))
						.replace('%ID%',char.id)
						.replace('%LABEL%',char.label) //  + ':' + charactercounts.taxon_count
						.replace('%TITLE%',__( "Dit kenmkerk is bij de huidige selectie nog niet onderscheidend." ))
						.replace('%VALUE%',(char.value?' '+char.value:''))
						.replace('%SELECTED%',l);
				}
				else
				{
					c=c+menuCharHtmlTpl
						.replace('%CLASS%',(j==(item.chars.length-1)?' last':''))
						.replace('%ID%',char.id)
						.replace('%LABEL%',char.label) //  + ':' + charactercounts.taxon_count
						.replace('%VALUE%',(char.value?' '+char.value:''))
						.replace('%SELECTED%',l);
				}
			}
			
			s=menuGroupHtmlTpl
				.replace(/%ID%/g,item.id)
				.replace('%LABEL%',item.label)
				.replace('%CHARACTERS%',c);

		}
		else
		if (item.type=='char')
		{
			item.disabled = shouldDisableChar(item.id);
			item.emergent_disabled = shouldDisableEmergentChar(item.id);
			
			var activestates=getActiveStates(item.id);
			
			var l=""
	
			if (activestates)
			{
				var t="";
				for (var k in activestates)
				{
					var state=activestates[k];
					t=t+menuSelStateHtmlTpl
						.replace('%VALUE%',(state.value ? state.value : ''))
						.replace('%LABEL%',(state.label ? state.label : ''))
						.replace('%COEFF%',(state.separationCoefficient ? '('+state.separationCoefficient+') ' : ''))
						.replace('%STATE-ID%',state.val)
						.replace('%IMG-URL%',settings.imageRootSkin+'clearSelection.gif');
				}
				
				l=menuSelStatesHtmlTpl.replace('%STATES%',t);
			}
			
			if (item.disabled==true)
			{
				s=menuLoneCharDisabledHtmlTpl
					.replace('%CLASS%',"")
					.replace('%ID%',item.id)
					.replace('%LABEL%',item.label)
					.replace('%TITLE%',__( "Dit kenmkerk is bij de huidige selectie niet langer onderscheidend." ))
					.replace('%VALUE%',(item.value?' '+item.value:''))
					.replace('%SELECTED%',l);
			}
			else
			if (item.emergent_disabled==true)
			{
				s=menuLoneCharEmergentDisabledHtmlTpl
					.replace('%CLASS%',"")
					.replace('%ID%',item.id)
					.replace('%LABEL%',item.label)
					.replace('%TITLE%',__( "Dit kenmkerk is bij de huidige selectie nog niet onderscheidend." ))
					.replace('%VALUE%',(item.value?' '+item.value:''))
					.replace('%SELECTED%',l);
			}
			else
			{
				s=menuLoneCharHtmlTpl
					.replace('%CLASS%',"")
					.replace('%ID%',item.id)
					.replace('%LABEL%',item.label)
					.replace('%VALUE%',(item.value?' '+item.value:''))
					.replace('%SELECTED%',l);
			}
		}
		
		buffer.push(s);

	}

	if (groupcount==1 && lastgroupid) openGroups.push(lastgroupid);
	
	$('#facet-categories-menu').html( menuOuterHtmlTpl.replace('%MENU%',buffer.join('\n') ) );
	
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
		showRestartButton();
	}	
}

function clearResults()
{
	$('#results-container').html('');
	setSetting({expandedShowing:0});
}

function printHeader()
{
	if (settings.mode=="search")
	{
		printSearchHeader();
	}
	else
	if (settings.mode=="similar")
	{
		printSimilarHeader();
	}
	else
	{
		printCountHeader();
	}
}

function printResultsExpanded()
{
	var resultset = getResultSet();

	var s="";
	var printed=0;
	var d=0;
	
	for(var i=0;i<resultset.length;i++)
	{
		if (i < settings.expandedShowing+settings.perPage)
		{
			s=s+formatResult(resultset[i]);

			printed++;

			if (++d==settings.perLine)
			{
				s=s+resultsLineEndHtmlTpl;
				d=0;
			}
		}
	}

	$('#results-container').html(
		resultBatchHtmlTpl
			.replace('%STYLE%',"")
			.replace('%RESULTS%', resultsHtmlTpl.replace('%RESULTS%',s))
	);

	// parallel processing using show() causes mayhem when clicking the 'show more'-button fast.
	//		.replace('%STYLE%',(settings.expandedShowing>0  ? 'display:none' : ''))
	//	$('.result-batch:hidden').show('normal');
	
	settings.expandedShowing=printed;

	if (settings.expandedShowing<resultset.length)
	{
		if (!$("#show-more").is(':visible'))
		{
			$("#paging-footer").append( buttonMoreHtmlTpl.replace('%LABEL%',__('meer resultaten laden')) );
			$("#footerPagination").addClass('noline');
		}
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
				s=s+resultsLineEndHtmlTpl;
				d=0;
			}
		}
	}

	$('#results-container').html(resultsHtmlTpl.replace('%RESULTS%',s));
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
		var commonName=(data.taxon.commonname ? data.taxon.commonname : "" ) + " " + (data.label ? "(" + data.label + ")" : "");
		commonName.trim();
	}
	else
	if ( data.type=='matrix' )
	{
		//var sciName=data.taxon.label;
		var sciName='<i>'+data.label+'</i>';
		var commonName="";
	}

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
				speciesStateItemHtmlTpl
					.replace('%GROUP%',state.group_label + ' > ')
					.replace('%CHARACTER%',t)
					.replace('%STATE%',l)
			);
		}
	}

	var image="";

	if (data.info && data.info.url_image)
	{
		image=data.info.url_image;
		if (image && !image.match(/^(http:\/\/|https:\/\/)/i)) image=settings.imageRootProject+image;
	}
	else
	{
		if (settings.defaultSpeciesImage) image=settings.defaultSpeciesImage;
	}

	var thumb="";

	if (data.info && data.info.url_thumb)
	{
		thumb=data.info.url_thumb;
		if (thumb && !thumb.match(/^(http:\/\/|https:\/\/)/i)) thumb=settings.imageRootProject+thumb;
	}
	else
	{
		thumb=image;
	}
	
	var id = data.type+'-'+data.id;
	var showStates = states && states.length > 0;

	photoLabelHtml=
		photoLabelHtmlTpl
			.replace('%SCI-NAME%',sciName)
			.replace('%GENDER%',(data.gender && data.gender.gender ?
				photoLabelGenderHtmlTpl
					.replace('%IMG-SRC%', settings.imageRootSkin + data.gender.gender+'.png')
					.replace('%GENDER-LABEL%', data.gender.gender_label)
				: "" ))
			.replace('%COMMON-NAME%',(commonName ? brHtmlTpl + commonName : ""))
			.replace('%PHOTO-DETAILS%',(data.info && data.info.photographer ? 
				photoLabelPhotographerHtmlTpl
					.replace('%PHOTO-LABEL%', __('foto')+' &copy;' )
					.replace('%PHOTOGRAPHER%', data.info.photographer )
				: ""));

	imageHtml=
		imageHtmlTpl
			.replace('%IMAGE-URL%',image)
			.replace('%THUMB-URL%',thumb)
			.replace('%PHOTO-LABEL%',encodeURIComponent(photoLabelHtml))
			.replace('%PHOTO-CREDIT%',(data.info && data.info.photographer ? __('foto')+' &copy;'+data.info.photographer : ''))
		;	

	resultHtml=
		resultHtmlTpl
			.replace('%CLASS-HIGHLIGHT%',(data.h ? ' result-highlight' : ''))
			.replace('%IMAGE-HTML%',(image ? imageHtml : ""))
			.replace('%GENDER%',(data.gender && data.gender.gender ? 
				genderHtmlTpl
					.replace('%ICON-URL%', settings.imageRootSkin+data.gender.gender+'.png') 
					.replace('%GENDER-LABEL%', data.gender.gender_label) 
				: "" )
			)
			.replace('%SCI-NAME%', sciName)
			.replace('%SCI-NAME-TITLE%', addSlashes(stripTags(sciName)) )
			.replace('%MATRIX-LINK%', (data.type=='matrix' ? 
				matrixLinkHtmlTpl.replace("%MATRIX-ID%",data.id).replace("%MATRIX-LINK-TEXT%",__('Ga naar sleutel'))
				: ""))
			.replace('%COMMON-NAME%', commonName)
			.replace('%COMMON-NAME-TITLE%', addSlashes(commonName) )
			.replace('%REMOTE-LINK-CLASS%', data.info && data.info.url_external_page ? "" : " no-content")
			.replace('%REMOTE-LINK-CLICK%', data.info && data.info.url_external_page ?  
				remoteLinkClickHtmlTpl
					.replace('%REMOTE-LINK%', data.info.url_external_page)
					.replace('%TITLE%', nbcLabelExternalLink)
				: "")
			.replace('%REMOTE-LINK-ICON%', data.info && data.info.url_external_page ?
				iconUrlHtmlTpl.replace('%IMG-URL%',settings.imageRootSkin+"information_grijs.png") : "")
			.replace('%SHOW-STATES-CLASS%', showStates ? "" : " no-content")
			.replace('%SHOW-STATES-CLICK%', showStates ?  statesClickHtmlTpl.replace('%TITLE%',nbcLabelDetails) : "")
			.replace('%SHOW-STATES-ICON%', showStates ?
				iconInfoHtmlTpl.replace('%IMG-URL%',settings.imageRootSkin+"lijst_grijs.png") : "")
			.replace('%RELATED-CLASS%', data.related_count>0 ? "" : " no-content")
			.replace('%RELATED-CLICK%', (data.related_count>0 ?  
				relatedClickHtmlTpl
					.replace('%TYPE%', data.type)
					.replace('%ID%', data.id)
					.replace('%TITLE%', nbcLabelSimilarSpecies)
				: "" )
			)
			.replace('%RELATED-ICON%', data.related_count>0 ?
				iconSimilarTpl.replace('%IMG-URL%',settings.imageRootSkin+"gelijk_grijs.png") : "")
			.replace('%STATES%', showStates ? statesHtmlTpl.replace( '%STATES%',states.join(statesJoinHtmlTpl)) : "")
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
			counterExpandHtmlTpl
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
			counterPaginateHtmlTpl
				.replace('%FIRST-NUMBER%', (settings.start+1))
				.replace('%LAST-NUMBER%',(settings.start+settings.perPage))
				.replace('%NUMBER-LABEL%',__('van'))
				.replace('%NUMBER-TOTAL%',resultset.length)
		);
	}
	else
	{
		$('#result-count').html(
			counterPaginateHtmlTpl
				.replace('%FIRST-NUMBER%',1)
				.replace('%LAST-NUMBER%',resultset.length)
				.replace('%NUMBER-LABEL%',"")
				.replace('%NUMBER-TOTAL%',"")
		);
	}
	
	if (resultset.length==0)
	{
		$('#result-count').html("");
	};	
}

function clearPaging()
{
	$('#paging-header').html('');	
	$('#paging-footer').html('');	
}

function printPaging()
{
	var resultset = getResultSet();

	setSetting({lastPage:Math.ceil(resultset.length / settings.perPage)});
	setSetting({currPage:Math.floor(settings.start / settings.perPage)});

	if (settings.lastPage > 1 && settings.currPage!=0)
	{
		$("#paging-header").append( pagePrevHtmlTpl );
	}
	
	if (settings.lastPage>1)
	{ 
		for (var i=0;i<settings.lastPage;i++)
		{
			if (i==settings.currPage)
			{
				$("#paging-header").append( pageCurrHtmlTpl.replace('%NR%',(i+1)) );
			}
		    else
			{
				$("#paging-header").append( pageNumberHtmlTpl.replace('%NR%',(i+1)).replace('%INDEX%',i) );
			}
		}
	}

	if (settings.lastPage > 1 && settings.currPage<settings.lastPage-1)
	{
		$("#paging-header").append( pageNextHtmlTpl );
	}

	$("#paging-footer").html($("#paging-header").html());
}

function browsePage( id )
{
	if (id=='n')
		setSetting({start: settings.start+settings.perPage});
	else if (id=='p')
		setSetting({start: settings.start-settings.perPage});
	else if (!isNaN(id))
		setSetting({start:id * settings.perPage});
	else
		return;
			
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

function getCharacterCounts( id )
{
	var characters=getCharacters();

	if (!characters) return;
	
	for(var c in characters)
	{
		if (c==id)
		{
			return characters[c];
		}
	}

	return {taxon_count:0,distinct_state_count:0};
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
			var d=$.parseJSON(data);

			setScores(d.scores);
			setStates(d.states);
			setCharacters(d.characters);

			closeSimilar();
			closeSearch();

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

	// scores are sorted in the controller
	
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

	setSetting({showSpeciesDetails: settings.alwaysShowDetails || (resultset.length <= settings.perPage)});
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

function applyFound()
{
	var found=getFound();
	var dataset=getDataSet();
	var resultset=getResultSet();

	resultset.splice(0,resultset.length);

	for(var i in found)
	{
		var tfound=found[i];
		for(var j in dataset)
		{
			var item=dataset[j];
			if (tfound.id==item.id && tfound.type==item.type)
			{
				resultset.push(item);
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

			setPrevSettings();
			setLastScrollPos();

			setSetting({mode:"similar"});
			setSetting({start:0});
			setSetting({expandedShowing:0});
			setSetting({browseStyle:'show_all'});
			setSetting({showSpeciesDetails: true});
			
			clearPaging();
			clearResults();

			applyRelated();
			removeSimilarCharacters();
			printResults();
			printSimilarHeader();
			window.scroll(0,0);
			setCursor();
			showRestartButton();
		}
	});
	
}

function peersHaveIdenticalValues(fcharacter,fvalues)
{
	var resultset=getResultSet();
	
	if (resultset.length<=1)
	{
		return false;
	}

	fvalues.sort();
	
	foundnothing=true;
	
	for(var i=0;i<resultset.length;i++)
	{
		var result=resultset[i];
		for(var c in result.states)
		{
			if (c==fcharacter)
			{
				foundnothing=false;
				var character=result.states[c];
				var values=Array();
				
				for(var s in character.states)
				{
					var state=character.states[s];
					values.push(state.id);
				}
				
				values.sort();
				
				if (values.join(';')!=fvalues.join(';')) return false;
			}
		}
	}
	
	if (foundnothing)
		return false;
	else
		return true;
}

function removeSimilarCharacters()
{
	var resultset=getResultSet();
	//console.dir(resultset);
	
	var filteredStates=Array();
	
	for(var i=0;i<resultset.length;i++)
	{
		var result=resultset[i];
		var filteredCharacters={};
		for(var c in result.states)
		{
			var character=result.states[c];
			//console.dir(character);
			var values=Array();
			for(var s in character.states)
			{
				var state=character.states[s];
				//console.dir(state);
				values.push(state.id);
			}
			values.sort();
			if (!peersHaveIdenticalValues(c,values))
			{
				var myObj = new Object;
				myObj[c] = character;
				$.extend(filteredCharacters,myObj);
			}
		}

		resultset[i].states=filteredCharacters;
	}
}

function clearSimilarHeader()
{
	$('#similarSpeciesHeader').html('');	
}

function printSimilarHeader()
{
	var resultset = getResultSet();

	$('#similarSpeciesHeader').html(
		similarHeaderHtmlTpl
			.replace('%HEADER-TEXT%', __('Gelijkende soorten van'))
			.replace('%SPECIES-NAME%', resultset[0].label)
			.replace('%BACK-TEXT%', __('terug'))
			.replace('%SHOW-STATES-TEXT%', nbcLabelShowAll)
			.replace('%NUMBER-START%', settings.start+1)
			.replace('%NUMBER-END%', data.resultset.length)
	).removeClass('hidden').addClass('visible');
	
	$('.result-icon.related').find('img').remove();

}

function toggleAllDetails()
{
	if ($('.result-detail:visible').length < getResultSet().length)
	{
		$('.result-detail').toggle(true);
		$('#showAllLabel').html(nbcLabelHideAll);
	}
	else
	{
		$('.result-detail').toggle(false);
		$('#showAllLabel').html(nbcLabelShowAll);
	}

}

function toggleDetails(id)
{
	$('#det-'+id).toggle();
}

function setSearch( p )
{
	var s=$('#inlineformsearchInput').val();
	
	if (s.length==0) return;
	
	setCursor('wait');
	
	searchedfor=s;

	$.ajax({
		url : 'ajax_interface.php',
		type: 'POST',
		data : ({
			action : 'get_search' ,
			search : s,
			time : getTimestamp(),
			key : settings.matrixId,
			p : settings.projectId
		}),
		success : function(data)
		{
			//console.log(data);
			var found=$.parseJSON(data);
			setFound(found);

			setPrevSettings();
			setLastScrollPos();

			setSetting({mode:"search"});
			setSetting({start:0});
			setSetting({expandedShowing:0});
			setSetting({browseStyle:'expand'});
			setSetting({showSpeciesDetails: true});
			
			clearPaging();
			clearResults();

			applyFound();
			removeSimilarCharacters();
			printResults();
			printSearchHeader();
			window.scroll(0,0);
			setCursor();
			showRestartButton();
		}
	});
	
}

function printSearchHeader()
{
	$('#similarSpeciesHeader').html(
		searchHeaderHtmlTpl
			.replace('%HEADER-TEXT%', __('Zoekresultaten voor'))
			.replace('%SEARCH-TERM%', searchedfor)
			.replace('%BACK-TEXT%', __('terug'))
			.replace('%NUMBER-START%', settings.start+1)
			.replace('%NUMBER-END%', settings.expandedShowing)
			.replace('%OF-TEXT%', __('van'))
			.replace('%NUMBER-TOTAL%', data.resultset.length)
	).removeClass('hidden').addClass('visible');
}

function closeSimilarSearch()
{
	setSetting({mode:"identify"});
	clearSimilarHeader();
	applyScores();
	clearResults();
	setSetting(getPrevSettings());
	setSetting({expandedShowing:settings.expandedShowing-settings.perPage});
	printResults();
	window.scroll(0,getLastScrollPos());
}

function closeSimilar()
{
	setRelated();
	closeSimilarSearch();
}

function closeSearch()
{
	setFound();
	$('#inlineformsearchInput').val("");
	closeSimilarSearch();
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
			// Ensure that it is a number or a dot and stop the keypress
			if (event.shiftKey || (event.keyCode<48 || event.keyCode>57) && (event.keyCode<96 || event.keyCode>105) && event.keyCode!=190)
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


function setLastScrollPos()
{
	lastScrollPos=getPageScroll();
}

function getLastScrollPos()
{
	return lastScrollPos;
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

function setCharacters(characters)
{
	data.characters=characters;
}

function getCharacters()
{
	return data.characters;
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

function setFound(found)
{
	data.found=found;
}

function getFound()
{
	return data.found;
}

function setPrevSettings()
{
	prevSettings = jQuery.extend({}, settings);
}

function getPrevSettings()
{
	return prevSettings;
}

function setSetting( p )
{
	$.extend(settings, p);
}

function getCharacter( id )
{
	var menu=getMenu();

	for (var i in menu)
	{
		var item = menu[i];

		if (item.type=='group')
		{
			for (var j in item.chars)
			{
				if (item.chars[j].id==id) return item.chars[j];
			}
		}
		else
		if (item.type=='char')
		{
			if (item.id==id) return item;
		}
	}
}

function matrixInit()
{
	nbcLabelClose = __('sluiten');
	nbcLabelDetails = __('onderscheidende kenmerken');
	nbcLabelBack = __('terug');
	nbcLabelSimilarSpecies = __('gelijkende soorten');
	nbcLabelShowAll = __('alle onderscheidende kenmerken tonen');
	nbcLabelHideAll = __('kenmerken verbergen');
	nbcLabelExternalLink = __('Meer informatie over soort/taxon');

	$('#legendDetails').html(nbcLabelDetails);
	$('#legendSimilarSpecies').html(nbcLabelSimilarSpecies);
	$('#legendExternalLink').html(nbcLabelExternalLink);

	settings.defaultSpeciesImage=settings.defaultSpeciesImages[settings.imageOrientation];

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

function disableImgContextMenu()
{

    $("img").on("contextmenu",function(){
       return false;
    });

}

function showRestartButton()
{
	$('#clearSelectionContainer').removeClass('ghosted');
}


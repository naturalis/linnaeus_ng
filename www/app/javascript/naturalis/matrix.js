var nbcStart = 0;
var nbcExpandedShowing = 0;
var nbcExpandedPrevious = null;
var nbcPerPage = 16;
var nbcData;
var nbcFullDatasetCount = 0;
var nbcImageRoot;
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
var popup_species_link;

function nbcGetResults(p) {

	setCursor('wait');

	console.dir({
			action : 'get_results_nbc',
			params : p,
			time : getTimestamp(),
			key : matrixId,
			p : projectId
		});

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

			if (p && p.clearOverhead==true) nbcClearOverhead();
			if (p && p.action!='similar') nbcDoOverhead();
			if (p && p.action=='similar') nbcPrintSimilarHeader();
			if (p && p.closeDialog==true) jDialogCancel();
			if (p && p.refreshGroups==true) nbcRefreshGroupMenu();
			if (p && p.scrollWindow==true) window.scroll(0,nbcPreviousBrowseStyles.lastPos);
			if (p && p.scrollTop==true) window.scroll(0,0);

			setCursor();
		}
	});
	
}

function nbcDoSearch() {

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
		success : function (data) {
			//console.log(data);
			nbcData = $.parseJSON(data);
			nbcDoResults();
			nbcDoOverhead();
			nbcPrintSearchHeader();
			nbcSaveSessionSetting('nbcSearch',nbcSearchTerm);
			setCursor();
			nbcShowShowMoreButton(false);
			return false;

		}
	});

	return false;

}


function nbcDoResults(p) {

	if (p && p.resetStart!==false)
		nbcStart = 0;
	nbcExpandedShowing = 0;
	nbcClearResults();
	if (nbcData.results)
		nbcPrintResults();
	//else nbcShowShowMoreButton();
	
}

function nbcClearResults() {

	$('#results-container').html('');

}

function nbcPrintResults() {

	var results = nbcData.results;
	var s = '';
	var added = 0;

	for(var i=0;i<results.length;i++) {
		if (
			(nbcExpandedPrevious!=null && i<nbcExpandedPrevious) ||
			(i>=nbcExpandedShowing && i<nbcExpandedShowing+nbcPerPage)
		) {
			s = s + nbcFormatResult(results[i]);
			added++;
		}
	}

	$('#results-container').html($('#results-container').html()+s);

/*
	if (nbcExpandedShowing>0) {
		var n = 'p'+rndStr();
		s = '<div id="'+n+'" style="display:none">'+s+'</div>';
	}
*/
	nbcExpandedShowing = nbcExpandedShowing + added;

	if (nbcExpandedShowing==added && nbcExpandedShowing < nbcData.count.results) {
		$("#paging-footer").html("<input class='ui-button' id='show-more-button' onclick='nbcPrintResults();return false;' type='button' value='"+_('meer resultaten laden')+"'>");
	}

	if (nbcExpandedShowing<nbcData.count.results)
		nbcShowShowMoreButton(true);
	else
		nbcShowShowMoreButton(false);

	nbcExpandedPrevious = null;
	nbcDoOverhead();
	nbcPrettyPhotoInit();

}

function nbcFormatResult(data) {

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
	
	var showDetails = nbcData.results.length <= nbcPerPage;

	if (data.l!=data.c && data.l.indexOf(data.c)===0) {
		data.l = data.c + ' (' + data.l.replace(data.c,'').replace(/(^\s|\s$)/,'') + ')';
	}

	var photoLabel = 
		(data.s==data.l || !data.s ? '<i>'+(data.l)+'</i>' : data.l)+
		(data.g ? ' <img class="gender" height="17" width="8" src="'+nbcImageRoot+data.g+'.png" title="'+data.g+'" />' : '' )+
		(data.s!=data.l ? '<br /><i>'+(data.s)+'</i>' : '')
	
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

			states.push('<span class="result-detail-label">'+t +': </span><span class="result-detail-value">'+l+'</span>');
		}
		
	}
	
	var showStates = states && states.length > 0;

	if (data.n) {
		if (!data.m.match(/^(http:\/\/|https:\/\/)/i)) data.m = baseUrlProjectImages + data.m;
	} else {
		if (data.x) data.m = data.x;
	}

	var gender='';//' &#9794;'
	var book=details=resemblance="<div class='icon no-content'></div>";

	if (data.g) {
		if (data.g=='m') gender="<span class='gender'>&#9794;</span>";
		else
		if (data.g=='f') gender="<span class='gender'>&#9792;</span>"
	}
	if (data.u)
		book="<div class='icon icon-book' onclick='window.open(\""+data.u+"\",\""+data.v+"\");' title='"+nbcLabelExternalLink+"'></div>";
	if (showStates)
		details="<div class='icon icon-details' id='tog-"+id+"' onclick='nbcToggleSpeciesDetail(\""+id+"\");return false;' title='"+nbcLabelDetails+"'></div>";
	if (data.r)
		resemblance="<div class='icon icon-resemblance' onclick='nbcShowSimilar("+(data.i)+",\""+(data.t ? "v" : "t")+"\");return false;' title='"+nbcLabelSimilarSpecies+"'></div>";
	
	return "<div class='result' id='res-"+id+"'> \
		  <div class='result-result'> \
			<div class='result-image-container'>"+
			  (data.n ? "<a href='"+data.m+"' ptitle='"+escape(photoLabel)+"' rel='prettyPhoto[gallery]' title=''>" : "" )+" \
				<img class='result-image' src='"+data.m+"'>"+
			  (data.n ? "</a>" : "" )+" \
			</div> \
			<div class='result-labels'> \
			  <span class='result-name-scientific'>"+data.s+gender+"</span>"+
			(data.y=='m'? "<br /><a href='?mtrx="+data.i+"&main="+nbcData.matrix+"'>"+_('Ga naar sleutel')+"</a>" : "" )+" \
			  <span class='result-name-common'>"+(data.s!=data.l ? data.l : '')+"</span> \
			</div> \
		  </div> \
		  <div class='result-icons'>"+book+details+resemblance+" \
		  </div>"+
		  (states && states.length > 0 ? 
			  "<div class='result-detail hidden' id='det-"+id+"'> \
					<ul> \
					  <li>"+states.join('</li><li>')+"</li> \
					</ul> \
				</div>"
				: "")+
		"</div>";

}

function nbcShowShowMoreButton(state) {
	if (state)
		$("#show-more-button").removeClass('hidden');
	else
		$("#show-more-button").addClass('hidden');
}


function nbcDoOverhead() {
	nbcClearOverhead();
	if (nbcData.count) nbcPrintOverhead();
}

function nbcClearOverhead() {
	$('#result-count').html('');
	$('#similarSpeciesHeader').removeClass('visible').addClass('hidden');
	$('#similarSpeciesName').html('');
}

function nbcPrintOverhead() {
	$('#result-count').html((nbcExpandedShowing > 1 ? '1-'+nbcExpandedShowing : nbcExpandedShowing)+'&nbsp;'+_('van')+'&nbsp;'+nbcData.count.results);
}

function nbcShowSimilar(id,type) {

	nbcPreviousBrowseStyles.expand = nbcExpandResults;
	nbcPreviousBrowseStyles.expandShow = nbcExpandedShowing;
	nbcPreviousBrowseStyles.expandPrev = nbcExpandedPrevious;
	nbcPreviousBrowseStyles.lastPos = getPageScroll();

	nbcSetExpandResults(false);
	nbcGetResults({action:'similar',id:id,type:type,refreshGroups:true,scrollTop:true});
	nbcSaveSessionSetting('nbcSimilar',[id,type]);

}

function nbcPrintSimilarHeader() {

	var label = nbcData.results[0].s+(nbcData.results[0].l && nbcData.results[0].s!=nbcData.results[0].l ? ', '+nbcData.results[0].l : '');
	$('#similarSpeciesLabel').html(_('Soorten gelijkend op'));
	$('#similarSpeciesName').html(label);
	$('#similarSpeciesHeader').removeClass('hidden');

	$('#showAllLabelLabel').html(nbcLabelShowAll);
	$('#similarSpeciesNav').removeClass('hidden');

}

function nbcCloseSimilar() {

	$('#similarSpeciesNav').addClass('hidden');
	nbcSetExpandResults(nbcPreviousBrowseStyles.expand);
	nbcExpandedShowing = nbcPreviousBrowseStyles.expandShow;
	nbcExpandedPrevious = nbcPreviousBrowseStyles.expandPrev;
	nbcGetResults({clearOverhead:true,scrollWindow:true});	
	nbcSaveSessionSetting('nbcSimilar');
	
}


function nbcClearSearchTerm() {
	
	nbcSearchTerm='';
	$('#inlineformsearchInput').val('');

}

function nbcCloseSearch() {

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

	$('#similarSpeciesLabel').html(_('Zoekresultaten voor'));
	$('#similarSpeciesName').html('<span id="searchedForTerm">'+nbcSearchTerm+'</span>');
//	$('#similarSpeciesBack').html('<a class="clearSimilarSelection" href="#" onclick="nbcCloseSearch();return false;">'+nbcLabelBack+'</a>');
	$('#similarSpeciesHeader').removeClass('hidden').addClass('visible');
}

function nbcToggleSpeciesDetail(id,state) {

	if (state)
		nbcDetailShowStates[id] = (state=='show');
	else
		nbcDetailShowStates[id] = nbcDetailShowStates[id] ? !nbcDetailShowStates[id] : true;
	
	if (nbcDetailShowStates[id]) {
		$('#det-'+id).removeClass('hidden');
		$('#tog-'+id).attr('title',nbcLabelClose);
	} else {
		$('#det-'+id).addClass('hidden');
		$('#tog-'+id).attr('title',nbcLabelDetails);
	}
	
}

function nbcToggleAllSpeciesDetail() {

	var currHiding = ($('#showAllLabelLabel').html()==nbcLabelShowAll);
	
	$('[id^="tog-"]').each(function(){
		nbcToggleSpeciesDetail($(this).attr('id').replace(/(tog-)/,''), currHiding ? 'show' : 'hide' );
	});

	$('#showAllLabelLabel').html(currHiding ? nbcLabelHideAll : nbcLabelShowAll);

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
			if (data.character.type=="media")
				$('#modal-data-container').css('width','975px');
			else
				$('#modal-data-container').css('width','350px');

			$('#value-selector').modal({show:true});
			$('#value-selector-title').html(data.character.label);
			$('#value-selector-body').html(data.page);

			setCursor();
		}
	});

}

function nbcToggleGroup(id) {

	if ($('#character-item-'+id).hasClass('facet-group-open')) {
		$('#character-item-'+id).removeClass('facet-group-open').addClass('facet-group-closed');
	} else {
		$('#character-item-'+id).removeClass('facet-group-closed').addClass('facet-group-open');
	}

} 

function nbcRefreshGroupMenu() {
	
	if (!nbcData.menu) return;
	
	var data = nbcData.menu;

	$('#facet-categories-menu').html('');
	
	var d = Array();

	for (var i in data.groups) {

		var v = data.groups[i];

		var openGroup = data.groups.length==1 ? true : false;

		if (v.type=='group') {

			var s = 
				"<li id='character-item-"+v.id+"' class='facet-group-"+(openGroup?"open":"closed")+"'> \
				<a href='#' onclick='nbcToggleGroup("+v.id+");return false;'>"+v.label+"</a> \
				<ul id='character-group-"+v.id+"'>";

			for (var j in v.chars) {
	
				var c = data.groups[i].chars[j];
	
				if (c.disabled===true)
					s=s+"<li class='key-disabled'>"+c.label+(c.value?c.value:"");
				else
					s=s+"<li><a href='#' onclick='nbcShowStates("+c.id+");return false;'>"+c.label+(c.value?c.value:"")+"</a>";
					
				if (data.activeChars[c.id]) {

					openGroup = true;
					var selectionsToShow=Array();
					for (k in data.storedStates) {
						var state = data.storedStates[k];
						if (state.characteristic_id==c.id) {
							var dummy = state.type=='f' ? state.type+':'+state.characteristic_id : state.val;
							selectionsToShow.push( 
								"<span class='facetValueHolder'><a href='#' onclick='nbcClearStateValue(\""+dummy+"\");return false;'>"+
								(state.value ? state.value+' ' : '')+
								(state.label ? state.label+' ' : '')+
								(state.separationCoefficient ? '('+state.separationCoefficient+') ' : '')+"</a></span>"
							);


						}
					}
					
					
					if (selectionsToShow.length==1) {
						s = s + selectionsToShow[0];
					} else {
						s = s + '<br />&nbsp;&nbsp;&nbsp;' + selectionsToShow.join('<br />&nbsp;&nbsp;&nbsp;');
					}
					
					
					
					//s = s + "</span>";
	
				}
	
				s = s  +"</li>";
	
			}
	
			s = s  +"</ul>";

			if (openGroup)
				s = s + '<script> \n nbcToggleGroup('+v.id+'); \n </script>';

			d.push(s);

		} else {
			
			var c = v;

//			s = '<li class="inner ungrouped last"><a class="facetLink" href="#" onclick="nbcShowStates('+c.id+');return false;">'+c.label+(c.value ? ' '+c.value : '')+'</a>';
			s = "<li class='facet-characteristic'> \
					<a href='#' onclick='nbcShowStates("+c.id+");return false;'>"+c.label+(c.value ? ' '+c.value : '')+"</a> \
				  </li>";
	  			
			if (data.activeChars[c.id]) {
				openGroup = true;
				s = s + '<span>';
				for (k in data.storedStates) {
					var state = data.storedStates[k];
					if (state.characteristic_id==c.id) {
						var dummy = state.type=='f' ? state.type+':'+state.characteristic_id : state.val;
/*
						s = s + 
							'<div class="facetValueHolder">'+
								(state.value ? state.value+' ' : '')+
								(state.label ? state.label+' ' : '')+
								(state.separationCoefficient ? ' ('+state.separationCoefficient+') ' : '')+
								'<a href="#" class="removeBtn" onclick="nbcClearStateValue(\''+dummy+'\');return false;">'+
								'<img src="'+nbcImageRoot+'clearSelection.gif">'+
								'</a>'+
							'</div>';
*/
							s = s + 
							"<a href='#' onclick='nbcClearStateValue(\""+dummy+"\");return false;'>"+
							(state.value ? state.value+' ' : '')+
							(state.label ? state.label+' ' : '')+
							(state.separationCoefficient ? '('+state.separationCoefficient+') ' : '')+"</a>";
							if (k<data.storedStates.length-1 && data.storedStates.length>1)
								s=s+"<br />";
					}
				}
				
				s = s + '</span>';

			}

			s = s  + '</li>';
			
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

	setCursor('wait');

//	jDialogCancel();

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

function nbcSetExpandResults(state) {

	nbcExpandResults = state;
	
}

function nbcFilterEmergingCharacters() {

	if (nbcUseEmergingCharacters==false) return;

	var charactersWithAnActiveState=Array();
	for(var i in nbcData.selectedStates) {
		charactersWithAnActiveState[nbcData.selectedStates[i].characteristic_id]=true;
	}

	for(var i in nbcData.menu.groups) {
		for (var j in nbcData.menu.groups[i].chars) {
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

			if (nbcData.countPerCharacter && (char.type=='media' || char.type=='text') && charactersWithAnActiveState[id]!==true) {

				nbcData.menu.groups[i].chars[j].disabled=
					(
					nbcData.countPerCharacter[id]==undefined || 
					nbcData.countPerCharacter[id].taxon_count<nbcData.results.length ||
					nbcData.countPerCharacter[id].distinct_state_count<=1
					);

			} else {

				nbcData.menu.groups[i].chars[j].disabled=false;

			}
			
			//nbcData.menu.groups[i].chars[j].label=nbcData.menu.groups[i].chars[j].label+'::'+nbcData.menu.groups[i].chars[j].id;
		}
	}

}

// dialog button function, called from main.js::showDialog 
function jDialogOk() {

	nbcSetStateValue();

}

// dialog button function, called from main.js::showDialog 
function jDialogCancel() {

	$('#value-selector').modal('hide');
	//closeDialog();

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

	nbcSetExpandResults(true);

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


function printInfo( info, title, url )
{
	if (info)
	{
		showDialog(
			title,
			infoDialogHtmlTpl
				.replace('%BODY%',info)
				.replace('%URL%', url ? 
					infoDialogUrlHtmlTpl
						.replace('%URL%',url)
						.replace('%LINK-LABEL%',popup_species_link)
					 : "" ),
			{showOk:false});
	}
}
// alleen litho


var resultBatchSize=3;

var activeState={};
var activeTaxon={};
var stack=Array();
var activePage=selection;
var resultsVisible=0;
var forceScrollTop=false;

function init(p)
{
	if (p.matrix) appController.setmatrix(p.matrix);	
	if (p.language) appController.setlanguage(p.language);	
	main();
}

function main() 
{
	$('#moreRowsButton-text').html('Volgende '+resultBatchSize+' tonen');
	setVisible();
	appController.result(resultlist);
}

function resultlist(results)
{
	var buffer=Array();

	for (var i=0; i<results.length; i++){

		var element=results[i];
		var img = element.url_thumbnail;
		var tpl=templates.result.tpl;
		
		var initialrows=getVisible()!=0?getVisible():resultBatchSize;
			
		tpl=(i==0?tpl.replace('%class%',templates.result.class_0):(i<initialrows-1?tpl.replace('%class%',templates.result.class_1):tpl.replace('%class%',templates.result.class_n)));

		buffer.push(tpl.
			replace('%content%',templates.resultcontent.
				replace('%onclick%','appController.detail(%id%,'+(element.type=='taxon' ? 'false' : 'true')+',detail)').
				replace('%label%',element.label).
				replace(/\%id\%/g,element.id).
				replace('%image%',img)
		).replace('%n%',i).replace('%style%',(i>initialrows-1 ? 'display:none' : '')));
	}

	document.getElementById('resultsListView').innerHTML = buffer.join('');

	document.getElementById('num-of-results-top').innerHTML = 
		document.getElementById('num-of-results-bottom').innerHTML = results.length;
	document.getElementById('num-of-results-label-top').innerHTML = 
		document.getElementById('num-of-results-label-bottom').innerHTML = ' '+(results.length==1 ? 'dier' : 'dieren')+' gevonden';
		
	appController.states(activePage);
}

function resultsexpand()
{
	var i=0;
	$('li[content-type=result]').each(function(e){
		$(this).removeClass('ui-corner-bottom');
		if($(this).css('display')!='none') return;
		if(i>=resultBatchSize) return;
		$(this).css('display','block').slideDown();
		i++;
	});
	
	$('#scrollUpButton').toggle(true);

	redrawresults();
}

function setVisible(i)
{
	if (i)
		resultsVisible=i;
	else
		resultsVisible=0;
}

function getVisible()
{
	return resultsVisible;
}

function selection(states,active)
{
	// all options
	var buffer=Array();
	for (var i in states) {
		var element=states[i];
		var tpl = element.hasSelected ? templates.stateselected : templates.state;
		letter='abcdefghijklmnopqrstuvwxyz'.charAt(i%4);
		buffer.push(tpl.
			replace(/\%onclick\%/g,'setactivestate(([%id%,\'%type%\']));appController.states(character)').
			replace(/\%letter\%/g,letter).
			replace(/\%label\%/g,element.label).
			replace(/\%image\%/g,element.img).
			replace(/\%id\%/g,element.id).
			replace(/\%type\%/g,element.type)
		);
	}

	$('#filtergrid').html(buffer.join(''));

	// selected options
	var buffer=Array();
	for (var i in active) {
		var element=active[i];
		var tpl = templates.selectedstate;
		letter='abcdefghijklmnopqrstuvwxyz'.charAt(i%4);
		buffer.push(tpl.
			replace(/\%onclick\%/g,'activePage=selection;setVisible();appController.set({%id%:null},main);$(this).attr(\'onclick\',\'void(0);\');').
			replace(/\%letter\%/g,letter).
			replace(/\%label\%/g,element.label).
			replace(/\%image\%/g,element.img).
			replace(/\%charlabel\%/g,element.character.label).
			replace(/\%id\%/g,element.id)
		);
	}

	$('#selectiongrid').html(buffer.join(''));
	$('#x-menu-selection').css('display',(active.length>0 ? 'block' : 'none'));
	
	hideall();

	$('#selectioncontent').css('display','block');
	$('#resultcontent').css('display','block');
	$('#moreRowsButton').css('display','block');
	$('#bottom-bar').css('display','block');

	redrawresults();
	stackreset();
	resetclearallbutton();

	if (Object.size(appController.get())>0 && !forceScrollTop)
		scrollselected();
	else
		scrolltop();
		
	forceScrollTop=false;
	
}

function characterstates(character)
{
	var states=Array();
	for (var i in character.states) {

		var element=character.states[i];
		var tpl = (element.select_state=='-1' ? templates.statedisabled : (element.select_state=='1' ? templates.stateselected : templates.state));
		var onclick = (element.select_state=='0' ? 'state({%id%:true});' : 'state({%id%:null});')+"$(this).attr('onclick','void(0);');";
		var letter='abcdefghijklmnopqrstuvwxyz'.charAt(i%4);

		states.push(tpl.
			replace(/\%onclick\%/g,onclick).
			replace(/\%letter\%/g,letter).
			replace(/\%label\%/g,element.label).
			replace(/\%image\%/g,element.img).
			replace(/\%id\%/g,element.id)
		);
	}

	return templates.character.replace(/\%description\%/g,(character.description ? character.description : character.label.ucwords())).replace(/\%states\%/g,states.join(''));
}

function character(results)
{
	var act = getactivestate();
	var buffer=Array();
	for(var i in results) {
		var element=results[i];
		if (element.id==act.id && element.type==act.type) {
			if (element.type=='c_group' && element.hasCharacters) {
				for(var j in element.characters) {
					buffer.push(characterstates(element.characters[j]));
				}
			} else 
			if (element.hasStates) {
				buffer.push(characterstates(element));
			}
		}
	}
	
	$('#expanded-characters').html(buffer.join(''));

	hideall();

	$('#charactercontent').css('display','block');
	$('#resultcontent').css('display','block');
	$('#bottom-bar').css('display','block');
	
	scrolltop();
	redrawresults();
	resetclearallbutton();

}

function state(id)
{
	appController.set(id);
	setVisible();
	//activePage=character; // remove this to always return to home screen after (de)selecting a state
	appController.result(resultlist);
}

function detail(data)
{
	setactivetaxon([data.id,data.type]);
	
	var group='';
	if (data.group.id && data.group.name_nl)
		group=templates.speciesgroup.replace('%onclick%','appController.detail(%id%,false,detail);').replace('%label%',data.group.name_nl).replace(/\%id\%/g,data.group.id);

	var similar='';
	if (Object.size(data.similar)) {
		var buffer=Array();
		for (var i in data.similar) {
			var element=data.similar[i];
			var tpl=templates.speciessimilaritem.tpl;
			
			if (Object.size(data.similar)==1)
				tpl=tpl.replace('%class%',templates.speciessimilaritem.class_single);
			else
				tpl=(i==0?tpl.replace('%class%',templates.speciessimilaritem.class_0):(i<Object.size(data.similar)-1?tpl.replace('%class%',templates.speciessimilaritem.class_1):tpl.replace('%class%',templates.speciessimilaritem.class_n)));

			buffer.push(tpl.
				replace('%onclick%','appController.detail(%id%,false,detail);').
				replace('%image%',element.img).
				replace('%label%',element.label).replace(/\%id\%/g,element.id)
			);
		}
		similar=templates.speciessimilar.replace('%specieslist%',buffer.join('')).replace('%title%','Lijkt op:');
	} else {
		similar=templates.speciessimilar.replace('%specieslist%','').replace('%title%','');
	}
	
	var img = data.img_main.file ? templates.speciesdetailimage.replace('%image%',data.img_main.file) : '';

	var extraimages='';
	if (Object.size(data.img_add)>0) {
		var buffer=Array();
		var buffer2=Array();
		for (var i in data.img_add) {
			var element=data.img_add[i];
			var tpl=templates.extraimage.tpl;
			tpl=(i==0?tpl.replace('%style%',templates.extraimage.style_0):tpl.replace('%style%',templates.extraimage.style_n));
			if (element.file) buffer.push(tpl.replace(/\%image\%/g,element.file));
			if (element.copyright) buffer2.push(element.copyright);
		}
		var credits='';
		if (buffer2.length>0) credits=templates.extraimagescredits.replace('%credits%',buffer2.join(', '));
	
		extraimages = templates.extraimages.replace('%images%',buffer.join('')).replace('%credits%',credits);
	}

	$('#species-detail-content').html(templates.speciesdetail.
		replace('%title%',data.name_nl ? data.name_nl : '').
		replace('%subtitle%',data.name_sci ? data.name_sci : '').
		replace('%image%',img).
		replace('%image_copyright%',data.img_main.copyright ? data.img_main.copyright : '').
		replace('%text%',data.text ? data.text : '').
		replace('%text%','').
		replace('%extra_images%',extraimages).
		replace('%group%',group).
		replace('%similar%',similar)
	);
	
	hideall();
	$('#speciescontent').css('display','block');
	$('#go-to-top-link').click();
	scrolltop();

	stackadd(data.id);
}

function detailback()
{
	var id = stackget();
	if (id==undefined)
		main();
	else
		appController.detail(id,false,detail);
}

function loadpage(page)
{
	if (page) $('#secondary-content').load(page+'.html');

	$('#secondary').toggle(page);
	$('#main').toggle(!page);	

	$('#bottom-bar').css('display',(page ? 'none' : 'block'));

	scrolltop();
}

function stackadd(i)
{
	p=stack.pop();
	if (p!=i) stack.push(p);
	stack.push(i);	
}

function stackget()
{
	stack.pop();
	return stack.pop();
}

function stackreset()
{	
	stack=Array();
}

function setactivestate(a)
{
	activeState={id:a[0],type:a[1]};
}

function getactivestate()
{
	return activeState;	
}

function setactivetaxon(a)
{
	activeTaxon={id:a[0],type:a[1]};	
}

function getactivetaxon()
{
	return activeTaxon;	
}

function errorhandler()
{
	var e=appController.geterror();
	alert('error: '+e.message+' (error '+e.code+')');
}

function hideall()
{
	var pages = ['speciescontent','charactercontent','selectioncontent','resultcontent','bottom-bar'];
	
	for (var i in pages)
		$('#'+pages[i]).css('display','none');
}

function scrolltop()
{
  $("html, body").animate({ scrollTop: 0 }, "easeOutCubic");
}

function scrollresults()
{
	$("html, body").animate({ scrollTop: $("#result-top").offset().top }, 200, "easeOutCubic");
}

function scrollselected()
{
	$("html, body").animate({ scrollTop: $("#x-menu-selection").offset().top }, 1000, "easeOutCubic");
}

function redrawresults()
{
	$('li[content-type=result]:visible:last').addClass('ui-corner-bottom');

	$('#moreRowsButton').toggle($('li[content-type=result]:visible').length<$('li[content-type=result]').length);
	
	$('#scrollUpButton').toggle($('html').offset().top!==0);
	
	var remaining=$('li[content-type=result]').length-$('li[content-type=result]:visible').length;

	if (remaining < resultBatchSize)
		$('#moreRowsButton-text').html('Laatste '+remaining+' tonen');
	else
		$('#moreRowsButton-text').html('Volgende '+resultBatchSize+' tonen');
	
	setVisible($('li[content-type=result]:visible').length);
}

function clearall()
{
	$('#clear-all-button').attr('onclick','void(0);');
	activePage=selection;
	setVisible();
	appController.reinitialise(main);
}

function resetclearallbutton()
{
	$('#clear-all-button').attr('onclick','clearall();');
}

function rprt(msg)
{
	if (typeof msg=="Object")
		console.dir(msg);
	else
		console.log(msg);
}



var act={};

function init(p) 
{
	if (p.matrix) appController.setmatrix(p.matrix);	
	if (p.language) appController.setlanguage(p.language);	
	if (p.imgroot) appController.setimgroot(p.imgroot);	

	appController.set({47671:true,47532:true});
}

function main() 
{
	appController.states(states);
	appController.result(results);
}

function setActive(a)
{
	act={id:a[1],type:a[0]};	
}

function getActive(l)
{
	return act;	
}

function errorHandler()
{
	var e=appController.geterror();
	alert('error: '+e.message+' (error '+e.code+')');
}

function states(states,active)
{
	var buffer=Array();
	for (var i in states) {
		var element=states[i];
		var tpl = element.hasSelected ? templates.stateselected : templates.state;
		letter='abcdefghijklmnopqrstuvwxyz'.charAt(i%4);
		buffer.push(tpl.
			replace(/\%onclick\%/g,'setActive(([\'%type%\',%id%]));appController.states(character)').
			replace(/\%letter\%/g,letter).
			replace(/\%label\%/g,element.label).
			replace(/\%image\%/g,element.img).
			replace(/\%id\%/g,element.id).
			replace(/\%type\%/g,element.type)
		);
	}
	$('#filtergrid').html(buffer.join(''));
	
	var buffer=Array();
	for (var i in active) {
		var element=active[i];
		var tpl = templates.selectedstate;
		letter='abcdefghijklmnopqrstuvwxyz'.charAt(i%4);
		buffer.push(tpl.
			replace(/\%onclick\%/g,'appController.set({%id%:null},main)').
			replace(/\%letter\%/g,letter).
			replace(/\%label\%/g,element.label).
			replace(/\%image\%/g,element.img).
			replace(/\%charlabel\%/g,element.character.label).
			replace(/\%id\%/g,element.id)
		);
	}
	$('#selectiongrid').html(buffer.join(''));
	$('#x-menu-selection').css('display',(active.length>0 ? 'block' : 'none'));
	
}

function results(results)
{
	var buffer=Array();
	for(var i in results) {
		var element=results[i];
		var tpl = i==0 ? templates.resultfirst : templates.resultrest;
		var img = element.url_thumbnail.search(/(http|https):(\/\/)/i)==-1 ? appController.getimgroot()+element.url_thumbnail : element.url_thumbnail;
		buffer.push(tpl.
			replace('%content\%',templates.resultcontent.
			replace(/\%label\%/g,element.label).
			replace(/\%id\%/g,element.id).
			replace(/\%image\%/g,img)
		).replace(/\%n\%/g,i));
	}
	$('#resultsListView').html(buffer.join(''));
	
	$('.num-of-results').html(results.length);
	$('.num-of-results-label').html(' '+(results.length==1 ? 'dier' : 'dieren')+' gevonden');

}

function toggleScreens()
{
	$('#maincontent').toggle();
	$('#selection-pane').toggle();
}

function _character(c)
{
	var buffer=states=Array();
	for (var i in c.states) {
		var element=c.states[i];
		var tpl = (element.state=='-1' ? templates.statedisabled : (element.state=='1' ? templates.stateselected : templates.state));
		letter='abcdefghijklmnopqrstuvwxyz'.charAt(i%4);
		states.push(tpl.
			replace(/\%onclick\%/g,'toggleScreens();appController.set({%id%:true},main);').
			replace(/\%letter\%/g,letter).
			replace(/\%label\%/g,element.label).
			replace(/\%image\%/g,element.img).
			replace(/\%id\%/g,element.id)
		);
	}
	buffer.push(templates.character.replace(/\%description\%/g,c.description).replace(/\%states\%/g,states.join('')));
	return buffer;
}

function character(results)
{
	var act = getActive();
	var buffer=Array();
	for(var i in results) {
		var element=results[i];
		if (element.id==act.id && element.type==act.type) {
			if (element.type=='c_group' && element.hasCharacters) {
				for(var j in element.characters) {
					buffer=buffer.concat(_character(element.characters[j]));
				}
			} else 
			if (element.hasStates) {
				buffer=buffer.concat(_character(element));
			}
		}

	}

	$('#expanded-characters').html(buffer.join(''));

	toggleScreens();
}
var act={};

function init(p) 
{
	if (p.matrix) appController.setmatrix(p.matrix);	
	if (p.language) appController.setlanguage(p.language);	
	if (p.imgroot) appController.setimgroot(p.imgroot);	

//	appController.set({47671:true,47532:true});
}

function main() 
{
	
	appController.detail(158310,false,detail);
		
//	appController.states(states);
//	appController.result(results);
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
	
	console.dir(active);
	
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
	var buffer=Array();
	var states=Array();
	for (var i in c.states) {

		var element=c.states[i];
		var tpl = (element.select_state=='-1' ? templates.statedisabled : (element.select_state=='1' ? templates.stateselected : templates.state));
		var onclick = element.select_state=='0' ? 'state({%id%:true})' : 'state({%id%:null})';
		var letter='abcdefghijklmnopqrstuvwxyz'.charAt(i%4);

		letter='abcdefghijklmnopqrstuvwxyz'.charAt(i%4);
			states.push(tpl.
			replace(/\%onclick\%/g,onclick).
			replace(/\%letter\%/g,letter).
			replace(/\%label\%/g,element.label).
			replace(/\%image\%/g,element.img).
			replace(/\%id\%/g,element.id)
		);
	}

	return templates.character.replace(/\%description\%/g,c.description).replace(/\%states\%/g,states.join(''));
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
					buffer.push(_character(element.characters[j]));
				}
			} else 
			if (element.hasStates) {
				buffer.push(_character(element));
			}
		}

	}

	$('#expanded-characters').html(buffer.join(''));
	toggleScreens();

}

function state(id)
{
	toggleScreens();
	appController.set(id);
	appController.states(states);
	appController.result(results);

}

function detail(data)
{
	//console.dir(data);

	var group=null;
	if (data.group.id && data.group.name_nl)
		group=templates.speciesgroup.replace(/\%onclick\%/,'alert('+data.group.id+')').replace(/\%label\%/,data.group.name_nl);

	var similar=null;
	if (data.similar) {
		var buffer=Array();
		for (var i in data.similar) {
			var element=data.similar[i];
			buffer.push(templates.speciessimilaritem.
				replace(/\%onclick\%/,'appController.detail(%id%,false,detail);').
				replace(/\%image\%/,element.img).
				replace(/\%label\%/,element.label).replace(/\%id\%/,element.id)
			);
		}
		similar=templates.speciessimilar.replace(/\%specieslist\%/,buffer.join(''));
	}

	$('#species-detail-content').html(templates.speciesdetail.
		replace(/\%title\%/g,data.name_nl).
		replace(/\%subtitle\%/g,data.name_sci).
		replace(/\%image\%/g,data.img_main.file).
		replace(/\%image_copyright\%/g,data.img_main.copyright).
		replace(/\%text\%/g,data.text).
		replace(/\%group\%/g,group).
		replace(/\%similar\%/g,similar)
	);

}

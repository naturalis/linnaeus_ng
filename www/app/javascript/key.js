function keyDoChoice(id) {
	window.open('../key/index.php?choice='+id+'&step=','_self');
}

function keyDoStep(id) {
	window.open('../key/index.php?choice=&step='+id,'_self');
}

var keyFullPathVisibility = false;

function keyToggleFullPath() {

	var id = '#path-full';

	if (keyFullPathVisibility) {

		$(id).removeClass().addClass('full-invisible');

	} else {

		var pos = $('#toggle').position();
		$(id).removeClass().addClass('full');
		$(id).offset({ left: pos.left, top: pos.top+23});

	}

	keyFullPathVisibility = !keyFullPathVisibility;

}

function getData(action) {

	allAjaxHandle = $.ajax({
		url : 'ajax_interface.php',
		type: 'POST',
		data : ({
			'action' : action 
		}),
		success : function (data) {
			//alert(data);
			obj = $.parseJSON(data);
		}
	})
	
}

function showRemaining() {

	$('#excluded').css('display','none');
	$('#remaining').css('display','block');
	$('#eLi').removeClass('category-active');
	$('#rLi').addClass('category-active');
	getData('store_remaining');

}

function showExcluded() {

	$('#remaining').css('display','none');
	$('#excluded').css('display','block');
	$('#rLi').removeClass('category-active');
	$('#eLi').addClass('category-active');
	getData('store_excluded');

}

var keyListAttr='name_sci';

function keyCompare(a,b)
{
	var x=$(a).attr(keyListAttr).replace( /<.*?>/g,'').toLowerCase();
	var y=$(b).attr(keyListAttr).replace( /<.*?>/g,'').toLowerCase();
	return x<y ? -1 : x>y ? 1 : 0;
}

function keyListsort(list)
{
	
	var items=[];

	$('#'+list+' li').each(function() {
		items.push($(this).html());
	});

	items.sort(keyCompare);
	
	$('#'+list+' li').remove();
	for(var i in items)
		$('#'+list).append('<li>'+items[i]+'</li>');

	$('#'+list).html('<li>'+items.join('</li><li>')+'</li>');
	
}

function keyNameswitch(ele)
{
	keyListAttr=$(ele).attr('data-type');

	$('.taxon-links').each(function(){
		if ($(this).attr(keyListAttr).length>0)
			$(this).html($(this).attr(keyListAttr));
	});
	
	keyListsort('ul-remaining');
	keyListsort('ul-excluded');
	
	$("[data-type=name_common]").toggle(true);
	$("[data-type=name_sci]").toggle(true);
	$("[data-type="+keyListAttr+"]").toggle(false);

	//setSessionVar(variable,value);
	//getSessionVar(variable,callback);

}



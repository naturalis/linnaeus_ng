var litAddedTaxa = Array();
var litAuthors = Array();
var litThisReference = '';
var litDropDownVisible = false;


function litToggleAuthorTwo() {
	
	var n = $("input[name='auths']:checked").val();

	$('#auth-label').html(_('Authors:'));

	if (n==1) {
		$('#auth-two').removeClass().addClass('lit-author-two-hidden');
		$('#auth-etal').removeClass().addClass('lit-author-etal-hidden');
		$('#auth-label').html(_('Author:'));
	} else
	if (n==2) {
		$('#auth-two').removeClass().addClass('lit-author-two');
		$('#auth-etal').removeClass().addClass('lit-author-etal-hidden');
	} else {
		$('#auth-two').removeClass().addClass('lit-author-two-hidden');
		$('#auth-etal').removeClass().addClass('lit-author-etal');
	}

}

function litShowAuthList(ele) {

	if ($(ele).val().length<3) {

		litHideAuthList();
		return;

	}
	
	$.ajax({
		url : "ajax_interface.php",
		data : ({
			'str' : $(ele).val() ,
			'action' : 'get_authors',
			'time' : allGetTimestamp()
		}),
		success : function (data) {
			//alert(data);
			if (data) {
				obj = $.parseJSON(data);
				if (obj.length > 0) {
					litPopulateAuthList(obj,ele);
					if (!litDropDownVisible) {
						var pos = $(ele).position();
						$('#dropdown').removeClass().addClass('lit-dropdown');
						$('#dropdown').offset({ left: pos.left, top: pos.top+23});
						litDropDownVisible = true;
					}
				}
			}
		}
	});	


}

function litSetAuthorFromList(eleId,id) {

	$('#'+eleId).val(litAuthors[id]);

}

function litPopulateAuthList(obj,ele) {

	litAuthors = Array();
	var b = '';

	for(var i=0;i<obj.length;i++) {

		b = b +'<span style="cursor:pointer" onclick="litSetAuthorFromList(\''+ele.id+'\','+i+')">'+obj[i].author+'</span><br/>'+"\n";
		litAuthors[i] = obj[i].author;

	}

	$('#dropdown').html(b);

}


function litHideAuthList() {

	$('#dropdown').removeClass().addClass('lit-dropdown-invisible');
	$('#dropdown').html('');
	litDropDownVisible = false;

}

function litCheckYear(ele,force) {

	var y = $(ele).val();

	if (y.length >= 4 || force) {

		var d = new Date(y, 1, 1, 0, 0, 0, 0)
		var now = new Date()

		var result = 
			d.getFullYear()==y && 
			d.getFullYear() <= now.getFullYear()+2 &&
			d.getFullYear() >= 0

		$('#msgYear').html(result ? _('ok') : _('invalid year'));
			
		return result;

	} else {

		$('#msgYear').html('');
		
		return false;

	}

}

function litCheckForm(ele) {

	if ($('#author_first').val().length==0) {

		alert(_('An author is required.'));
		
		$('#author_first').focus();

	} else 
	if ($('#author_second').val().length==0 && $("input[name='auths']:checked").val()==2) {

		alert(_('A second author is required.'));
		
		$('#author_second').focus();

	} else 
	if ($('#year').val().length==0) {

		alert(_('A year is required.'));
		
		$('#year').focus();

	} else 
	if (!litCheckYear($('#year'),true)) {

		alert(_('Invalid year.'));
		
		$('#year').val('').focus();

	} else 
	if ($('#text').val().length==0) {

		alert(_('A reference is required.'));
		
		$('#text').focus();

	} else {

		for(var i=0;i<litAddedTaxa.length;i++) {

			$("#theForm").append('<input type="hidden" name="selectedTaxa[]" value="'+litAddedTaxa[i][0]+'">');

		}

		$(ele).closest("form").submit();

	}

}

function litUpdateTaxonSelection() {

	var b = '';
	
	for(var i=0;i<litAddedTaxa.length;i++) {
	
		b = b + '<span style="cursor:pointer" ondblclick="litRemoveTaxonFromList('+litAddedTaxa[i][0]+')">'+litAddedTaxa[i][1]+'</span><br />';
	
	}
	
	$('#selected-taxa').html(b);

}

function litRemoveTaxonFromList(id) {

	var t = Array();

	for(var i=0;i<litAddedTaxa.length;i++) {

		if (litAddedTaxa[i][0] != id) {

			t[t.length] = litAddedTaxa[i];

		}

	}
	
	litAddedTaxa = t;

	litUpdateTaxonSelection();

}

function litAddTaxonToList(taxon) {

	if (!taxon) {
	
		taxon =([$('#taxa :selected').val(),$('#taxa :selected').text().trim()]);
	
	}

	var add = true;

	for(var i=0;i<litAddedTaxa.length;i++) {

		if (litAddedTaxa[i][0] == taxon[0]) {

			add = false;

			break;

		}

	}

	if (add) { 
	
		litAddedTaxa[litAddedTaxa.length] = taxon;

		litUpdateTaxonSelection();

	}

}

function litDelete() {

	if (!allDoubleDeleteConfirm(_('reference'),litThisReference)) return;
	
	$('#action').val('delete');

	$('#theForm').submit();

}
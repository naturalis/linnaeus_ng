var litAddedTaxa = Array();
var litAuthors = Array();
var litThisReference = '';
var litDropDownVisible = false;
var litDropDownType = 'reference'; // 'authors';
var litTaxonNames = Array();


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

	if (litDropDownType=='reference' && ele.id=='author_second') return;

	$.ajax({
		url : "ajax_interface.php",
		data : ({
			'str' : $(ele).val() ,
			'action' : (litDropDownType=='reference' ? 'get_references' : 'get_authors'),
			'time' : allGetTimestamp()
		}),
		success : function (data) {
			//alert(data);
			if (data) {
				obj = $.parseJSON(data);
				if (obj.length > 0) {
					if (litDropDownType=='reference')
						litPopulateRefList(obj,ele);
					else
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

function litSetAuthorFromRefList(id) {

	var r = litAuthors[id];
	
	if (!r) return;

	$('#auths-1').attr('checked', r[1]=='' ? true : false);
	$('#auths-2').attr('checked', r[1]!='' ? true : false);
	$('#auths-n').attr('checked', r[2]==1 ? true : false);
	litToggleAuthorTwo();
	$('#author_first').val(r[0]);
	$('#author_second').val(r[1]);

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

function litPopulateRefList(obj,ele) {

	litAuthors = Array();
	var b = '';

	for(var i=0;i<obj.length;i++) {

		b = b +
			'<span style="cursor:pointer" onclick="litSetAuthorFromRefList('+i+')">'+obj[i].author_full+'</span> '+
			'<span style="color:#666">'+obj[i].year+(obj[i].suffix ? obj[i].suffix : '')+'</span>'+
			'<br/>'+"\n";
		litAuthors[i] = [obj[i].author_first,obj[i].author_second,obj[i].multiple_authors];

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
	if (tinyMCE.get('text').getContent().length==0) {

		alert(_('A reference is required.'));
		
		$('#text').focus();

	} else {

		for(var i=0;i<litAddedTaxa.length;i++) {

			$("#theForm").append('<input type="hidden" name="selectedTaxa[]" value="'+litAddedTaxa[i]+'">');

		}

		$('#text').val(tinyMCE.get('text').getContent());
		
//		$(ele).closest("form").submit();
		$("#theForm").submit();

	}

}

function litGetTaxonName(taxonId) {

	if (!litTaxonNames[taxonId]) {

		$("#taxa > option").each(function() {
			if (this.value==taxonId) { 
				litTaxonNames[taxonId] = this.text.trim();
			}
		});
		
	}
	
	return litTaxonNames[taxonId];

}

function litUpdateTaxonSelection() {
		
	var b = '<table style="border-collapse:collapse;width:100%"">';
	
	for(var i=0;i<litAddedTaxa.length;i++) {
	
		b = b + 
			'<tr class="tr-highlight"><td style="width:97%">'+
			litGetTaxonName(litAddedTaxa[i])+
			'</td><td class="pseudo-a" onclick="litRemoveTaxonFromList('+litAddedTaxa[i]+')">x</td></tr>';
	
	}

	b = b + '</table>';

	$('#selected-taxa').html(b);

}

function litRemoveTaxonFromList(id) {

	var t = Array();

	for(var i=0;i<litAddedTaxa.length;i++) {

		if (litAddedTaxa[i] != id) {

			t[t.length] = litAddedTaxa[i];

		}

	}
	
	litAddedTaxa = t;

	litUpdateTaxonSelection();

}

function litCheckTaxonList(taxonId) {

	var add = true;

	// check if it's not aready in the list
	for(var i=0;i<litAddedTaxa.length;i++) {

		if (litAddedTaxa[i] == taxonId) {
			add = false;
			break;
		}

	}
	
	return add;

}


function litAddTaxonToList(taxonId,noupdate) {

	if (!taxonId) {
		
		$('#taxa option:selected').each(function () {

			if (litCheckTaxonList($(this).val())) {
				
				litAddedTaxa[litAddedTaxa.length] = $(this).val();
				if (!noupdate) litUpdateTaxonSelection();
				
			}

		});

	} else {

		if (litCheckTaxonList(taxonId)) {
			
			litAddedTaxa[litAddedTaxa.length] = taxonId;
			if (!noupdate) litUpdateTaxonSelection();
			
		}

	}
	
}

function litDelete() {

	if (!allDoubleDeleteConfirm(_('reference'),litThisReference)) return;
	
	$('#action').val('delete');

	$('#theForm').submit();

}
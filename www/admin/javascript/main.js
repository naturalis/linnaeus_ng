function tableColumnSort(col) {
	var e = document.getElementById('key');
	e.value = col;
	e = document.getElementById('postForm');
	e.submit();
}

function toggleHelpVisibility() {
	var e = document.getElementById('inlineHelp-body');
	if (e.className=='inlineHelp-body-hidden') {
		e.className='inlineHelp-body';
	} else {
		e.className='inlineHelp-body-hidden';
	}
}

function setErrorClass(id,error) {

	$('#'+id+'-message').removeClass().addClass(error ? 'admin-message-error' : 'admin-message-no-error');

}

function remoteValueCheck(id,values,tests,idti) {

	$.ajax({ url:
		   		"ajax_interface.php?f="+
		   		encodeURIComponent(id)+"&v="+
				encodeURIComponent(values)+"&t="
				+encodeURIComponent(tests)+
				(idti ? "&i="+encodeURIComponent(idti) : "" ),
		success: function(data){
	        $('#'+id+'-message').html(data);
			setErrorClass(id,data.search(/\<error\>/gi)!=-1);
      	}
	});

}

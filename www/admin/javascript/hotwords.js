function hotwordsDelete(id) {

	if(confirm(_('Are you sure?'))) {
		if (id=='*') {
			$('#action').val('delete_all');
		} else
		if (id) {
			$('#id').val(id);
			$('#action').val('delete');
		} else {
			$('#action').val('delete_module');
		}
		$('#theForm').submit(); 
	}

}

function hotwordsDelete(id) {

	if(confirm(_('Are you sure?'))) {
		if (id) {
			$('#id').val(id);
			$('#action').val('delete');
		} else {
			$('#action').val('delete_all');
		}
		$('#theForm').submit(); 
	}

}

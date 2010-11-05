function keyChoiceDelete() {
	
	if (confirm('Are you sure you want to delete this choice?')) {

		$('#action').val('delete');
		$('#theForm').submit();

	}

}

function keyDeleteImage() {

	if (confirm('Are you sure you want to delete this image?')) {

		$('#action').val('deleteImage');
		$('#theForm').submit();

	}

}

function keyCheckTargetIntegrity(ele) {

	if (ele.id == 'res_taxon_id' && $('#res_taxon_id option:selected').val()!='0') {

		$('#res_keystep_id').val(0);

	} else
	if (ele.id == 'res_keystep_id' && $('#res_keystep_id option:selected').val()!='0') {

		$('#res_taxon_id').val(0);

	}

}
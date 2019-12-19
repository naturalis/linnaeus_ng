function saveDisplayPreference (type)
{
	$.ajax({
		url : "ajax_interface.php" ,
		type: "POST",
		data : ({
			'action' : 'display_preference',
			'type' : type
		})
	});
}
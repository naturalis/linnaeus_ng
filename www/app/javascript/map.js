$(function(){
	$('body').on('change', '#map-select', function() {
		window.location.href = $(this).val();
	})
})
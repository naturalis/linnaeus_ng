$(function() {
	$('body').on('click', '.search-toggle-js', function() {
		$('body').toggleClass('search-open');
		$('#search').focus();
	});

	$('body').on('click', '.menu-toggle-js', function() {
		$('body').toggleClass('menu-open-push');
	});

	$('body').on('click', '.menu-toggle-over-js', function() {
		$('body').toggleClass('menu-open-over');
	});

	$('body').on('click', '.close-search-js', function() {
		$('body').removeClass('search-open');
	});

})
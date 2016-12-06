$(function() {
	$(document).click(function(event) { 
        if ($(event.target).parents('.menu__container').length === 0 && 
        	!$(event.target).hasClass('menu-toggle-js')) {
        	$('body').removeClass('menu-open-push');
        }     
    });

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
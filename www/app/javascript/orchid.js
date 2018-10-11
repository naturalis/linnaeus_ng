function renderDecisionPath(title, decisionPath) {
	$('.decisionpathContent').html(decisionPath);
	$('#eLi').removeClass('category-active');
	$('#rLi').removeClass('category-active');
	$('#decision-path-icon').addClass('category-active');
	$('#excluded').css('display','none');
	$('#decisionPathContainer').css('display','flex');
	$('#remaining').css('display','none');
} 

$(function() {
	$('body').on('click', '.fancy-box-video', function() {
		var hrefAttr = $(this).attr('href');
		if (hrefAttr.indexOf("#") >= 0) {
			result = hrefAttr.split("#");
			$(this).attr('href', "#"+result[1]);
		}
	});

	$('body').on('click', '.click-letter', function() {
		$('.click-letter.alphabet-active-letter').removeClass('alphabet-active-letter');
		$(this).addClass('alphabet-active-letter');
	});

	$('body').on('keyup', '#lookup-input-title, #lookup-input-author', function() {
		$('.click-letter.alphabet-active-letter').removeClass('alphabet-active-letter');
	});

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

	$('body').on('click', ".menu-toggle-js", function() {
		$('body').toggleClass('menu-open-push');
		//alert();
	});

	$('body').on('click', '.menu-toggle-over-js', function() {
		$('body').toggleClass('menu-open-over');
	});

	$('body').on('click', '.close-search-js', function() {
		$('body').removeClass('search-open');
	});
});
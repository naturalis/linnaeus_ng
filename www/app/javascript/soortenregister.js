$(function(){
	$('body').on('click', '.clickable', function(){
		var panel = $(this).attr('panel');
		
		if ($('#' + panel).is(':visible')) {
			
      $(this).find('.up').hide();
      $(this).find('.down').show();
			$('#'+panel).hide();			
		} else {
			$(this).find('.up').show();
      $(this).find('.down').hide();
			$('#'+panel).show();
		}
	});

  $('body').on('click', '.search-toggle-js', function() {
    $('body').toggleClass('search-open');
    $('.menuContainer').find('input').select().focus();
  });

  $(document).ready(function() {
    $('body').on('click', '.close-suggestion-list-js', function() {
      $('#name_suggestion').hide();
    });

    $('body').on('keyup', '#inlineformsearch #inlineformsearchInput', function(e) {
      if (e.keyCode==27 || $(this).val() == '') {
        $('.simpleSuggestions').hide();
      } else {
        $('.simpleSuggestions').show();
      }

      $('.simpleSuggestions ul').append('<li>Nog een suggestie</li>');
    });

    $(".fancybox").fancybox({
      beforeShow : function(){
		try {
			description = decodeURIComponent($(this.element).attr("ptitle"));
		} catch (e) {
			description = unescape($(this.element).attr("ptitle"));
		}
        if (description != "" && description != undefined) {
          this.title = description;
        }
      }
    });
  });

  $('body').on('click', '.menuToggle', function(e){
  	e.preventDefault();
  	
  	$('.menu').slideToggle('fast', function(){
  		$('body').toggleClass('menuOpen');
  	});
  });

  $('body').on('click', '.menu li .toggle-submenu-js', function(e) {
  	if ($('.menuToggle').css('display') === 'block') {
  		e.preventDefault();	
  		var submenu = $(this).parent().find('ol'),
  				plus = $(this).parent().find('i.plus');

  		if (submenu.css('display') === 'block') {
  			submenu.slideUp('fast');
  			plus.fadeIn('fast');
  		} else {
  			$('.menu').find('ol').slideUp('fast');
  			$('.menu').find('i.plus').fadeIn('fast');
  			plus.fadeOut('fast');
	  		submenu.slideDown('fast');
  		}

      if (submenu.length == 0) {
        window.location.replace($(this).attr('href'));
      }
  	}
  });

  $('body').on('click', '.toggleFooterLinks', function(e){
    e.preventDefault();
    
    if ($('.menuToggle').css('display') === 'block') {
      $('.footerLinkContainer').slideToggle();
    }
  });

/*
  $('body').on('change', '.filterPictures input[type=text]', function(){
    $(this).parents('form').submit();
  });
*/
  $('body').on('click', '.filterPictures label', function(){
    $(this).parent().find('.filter').toggle();
    $(this).parent().find('.down').toggle();
    $(this).parent().find('.up').toggle();
  });

	$('.flexslider').flexslider();
});
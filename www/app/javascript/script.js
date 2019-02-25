var popup_species_link;

function closeMenu() {
	$('body').removeClass('menuOpen');
}

function closeSearchBox() {
	$('body').removeClass('searchOpen');
}

function overlayOpen() {
	$('body').addClass('imageOverlayContainer-open');
}

function colofonOverlay(data, title) {
	$('.imageOverlayContainer .image').html(data);
	$('.imageOverlayContainer .name').html('<span class="colofonTitle">'+title+'</span>');
	//$('body').addClass('imageOverlayContainer-open');		
}

function colofonVersion(data, title) {
	$('.imageOverlayContainer .version').html('<div class="versionContent">'+data+'</div>');
}

function adjustPageTitleHeight() {
	var title = $('.topBarContainer .pageTitle'),
			parent = $(".topBarContainer"),
  		fontstep = 2;
  if (title.height()>parent.height() || title.width()>parent.width()) {
    title.css('font-size',((title.css('font-size').substr(0,2)-fontstep)) + 'px').css('line-height',((title.css('font-size').substr(0,2))) + 'px');
//    adjustPageTitleHeight(); /* sometimes thrashes browser!!! */
  }
}

function closeImageOverlay() {
	$('body').removeClass('imageOverlayContainer-open');
	$('body').removeClass('moreInfoOverlay');
}

function showMoreInfoOverlay(info, url, nameScientific, nameCommon) {
	if (info === null) {
		info = '';
	}
	var moreInfo = "<div><a href='"+url+"' target='_blank'>"+popup_species_link+"</a></div>";
	$('.imageOverlayContainer .name').html('<span class="result-name-scientific" title="'+nameScientific+'"><i>'+nameScientific+'</i></span><br><span class="result-name-common" title="'+nameCommon+'">'+nameCommon+'</span>');
	$('.imageOverlayContainer .image').html(info + moreInfo);
	$('.imageOverlayContainer .version').html('');
	$('body').addClass('imageOverlayContainer-open');
	$('body').addClass('moreInfoOverlay');	
}

$(function(){
	$( window ).resize(function() {
	  adjustPageTitleHeight();
	});
	adjustPageTitleHeight();

	$('body').on('click', '.icon-resemblance', function(){
		$('#scrollContainer').scrollTop(0);
	});

	$('body').on('click', '.result-image-container a', function(e) {
		e.preventDefault();
		if (window.matchMedia("(min-width: 676px)").matches) {
			var names = $(this).parents('.result').find('.result-labels').html(),
	  			imgUrl = $(this).attr('href'),
	  			photographerName = $(this).parents('.result-result').find('.photographerName').html();

			if (photographerName != '') {
				photographerName = '<div>foto © '+photographerName+'</div>';
			}
			$('.imageOverlayContainer .image').html('<img src="'+imgUrl+'" />'+photographerName);
			$('.imageOverlayContainer .name').html(names);
			$('.imageOverlayContainer .version').html('');
			$('body').addClass('imageOverlayContainer-open');	
		}
	});

	$('body').on('click', '.imageOverlayContainer .closeOverlay, .imageOverlayBackground', function() {
		closeImageOverlay();
	});

	var scrollContainer = document.getElementById("scrollContainer");
	new ScrollFix(scrollContainer);

	var scrollableFilter = document.getElementById("scrollableFilter");
	new ScrollFix(scrollableFilter);

	var menuOverlay = document.getElementById("menuOverlay");
	new ScrollFix(menuOverlay);

	$('body').on('click', '.filterToggle', function(e) {
		e.preventDefault();
		$('body').toggleClass('menuOpen');
	});

	$('body').on('click', '.closeOverlay', function(e){
		e.preventDefault();
		closeOverlay();
	});

	$('body').on('click', '.buttons .cancel', function(e){
		e.preventDefault();
		closeOverlay();
	});

	$('body').on('click', '.buttons .oke', function(e){
		e.preventDefault();
		jDialogOk();
	});

	$('body').on('click', '#searchToggle', function(e) {
		if (window.matchMedia("(max-width: 980px)").matches) {
		  	e.preventDefault();
		  	$('body').addClass('searchOpen');
		  	$('body').removeClass('menuOpen');
		}
	});

	$( "#quicksearch form" ).submit(function( event ) {
	  closeSearchBox();
	});

	$('body').on('click', '#menuOverlay', function(){
		closeMenu();
	});

	$('body').on('click', '#overlay', function(){
		closeOverlay();
	});

	$(document).keyup(function(e) {
   	if (e.keyCode == 27) { 
      closeMenu();
      closeSearchBox();
      closeOverlay();
      closeImageOverlay();
    }
	});
});
$.expr[":"].contains = $.expr.createPseudo(function(arg) {
  return function( elem ) {
      return $(elem).text().toUpperCase().indexOf(arg.toUpperCase()) >= 0;
  };
});

$(function() {
	$('body').on('click', '.search button', function(e) {
		e.preventDefault();
		var value = $('.search input').val();

		$('.identifiers li span').hide();
		$('.identifiers li span:contains("'+value+'")').each(function(){
			$(this).show();
		});
	})
});
(function( $ ){

  $.fn.prettyDialog = function() {

    var str_html =
          '<div class="dialog dialog-closed">'
      +   '  <div class="dialog-header" class="promptheader">'
      +   '    <div class="dialog-title">Contents</div>'
      +   '    <div class="dialog-close"></div>'
      +   '  </div>'
      +   '  <div class="dialog-content" class="prompt">'
      +   '    <div class="dialog-content-inner">'
      +   '    </div>'
      +   '    <div class="dialog-button-container">'
      +   '      <span class="dialog-button promptbutton"></span>'
      +   '    </div>'
      +   '  </div>'
      +   '</div>';

    this.each(function(){
      var $_me = $(this);
      var str_url = $_me.attr("href");
      var str_title = $_me.attr("title");
      var x = Math.round(Math.random()*150);
      var y = Math.round(Math.random()*150);

      var $_target = 
        $(str_html)
          .appendTo('body')
          .css({left: x, top:y})
            .find(".dialog-title")
              .html(str_title)
              .end()
            .find(".dialog-content-inner")
              .append("<img src='" + str_url + "'>")
              .end();

     // console.log($_target);
     $_target.draggable();

      $('.dialog-close', $_target).click(function(){
        $(this).parent().parent().addClass('dialog-closed')
      });

      $_me.click(function(){
        $_target.toggleClass('dialog-closed');
        return false;
      });
  
    });



    return this;
  
  };

})( jQuery );


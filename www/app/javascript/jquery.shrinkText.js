(function( $ ){

  $.fn.shrinkText = function() {

    var $_me = this;
    var $_parent = $_me.parent();

    var int_my_width = $_me.width();
    var int_parent_width = $_parent.width();

    if ( int_my_width > int_parent_width ){
      
      ratio =   int_parent_width / int_my_width;
      
      var int_my_fontSize = $_me.css("font-size").replace(/[^-\d\.]/g, '');
      
      int_my_fontSize = Math.floor(int_my_fontSize * ratio);
      
     
      $_me.css("font-size", int_my_fontSize + "px");

    }
  };

})( jQuery );

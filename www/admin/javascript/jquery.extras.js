/* adds 'show' and 'hide' events */
(function ($) {
  $.each(['show', 'hide'], function (i, ev) {
	var el = $.fn[ev];
	$.fn[ev] = function () {
	  this.trigger(ev);
	  return el.apply(this, arguments);
	};
  });
  $.fn.size = function()
  {
        return $(this).length;
  };
})(jQuery);


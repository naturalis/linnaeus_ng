// Inspired by http://stackoverflow.com/questions/5106243/how-do-i-get-background-image-size-in-jquery, a simple jquery plugin who does the task
 
$.fn.getBgImage = function(callback) {
    var height = 0;
    var path = $(this).css('background-image').replace('url', '').replace('(', '').replace(')', '').replace('"', '').replace('"', '');
    var tempImg = $('<img />');
    tempImg.hide(); //hide image
    tempImg.bind('load', callback);
    $('body').append(tempImg); // add to DOM before </body>
    tempImg.attr('src', path);
    $('#tempImg').remove(); //remove from DOM
};
 
 
// usage
$("#background").getBgImage(function() {
    console.log($(this).height());
});
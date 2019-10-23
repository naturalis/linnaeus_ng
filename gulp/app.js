'use strict';

window.$ = require('jquery');
window.jQuery = require('jquery');

var sprintf = require('sprintf-js');
var modal = require('modaldialog');
var modernizr = require('../node_modules/modernizr/modernizr.js');
var prettyphoto = require('../node_modules/prettyPhoto/js/jquery.prettyPhoto.js');
var purl = require('../node_modules/\@bower_components/purl/purl.js');
require('raphael', function (Raphael, $) {
    var container = $("#container")[0];
    var paper = Raphael(container, 600, 600);
    paper.circle(100, 100, 100);
});
var morris = require('morris.js06');
var fancybox = require('../node_modules/\@bower_components/fancybox');
var tableSorter = require('tablesorter');
/*

Not needed for all app pages

//var jQUi = require('jquery-ui');
//var nestedSortable = require('nestedSortable');
//var tinymce = require('tinymce');
//var jQst = require('jquery-sortable');

*/

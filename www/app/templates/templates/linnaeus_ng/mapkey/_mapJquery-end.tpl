{literal}
$("#mapTable").mousemove(function(event) {
    l2MapMouseOver(event.pageX,event.pageY);
}); 

$("#mapTable").mouseleave(function() {
	$("#coordinates").html(""); 
	$("#species-number").html(""); 
}); 

});
</script>
{/literal}

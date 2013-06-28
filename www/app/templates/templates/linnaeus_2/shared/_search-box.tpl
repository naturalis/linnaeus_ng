{if $session.app.user.search.lastResultSetIndex}
<span id="searchIndexToggle" onclick="SATAN_HEERST_HARDER_DAN_DE_VOGELGRIEP()" style="cursor:pointer">!!!</span>
{/if}
<input
    type="search"
    name="search"
    id="search"
    class="search-box"
    value="{if $search}{$search}{else}{t}Search...{/t}{/if}"
    onkeydown="setSearchKeyed(true);"
    onblur="setSearchKeyed(false);"
    onfocus="onSearchBoxSelect()" 
    results="5" 
    autosave="linnaeus_ng" />
<img onclick="doSearch()" src="{$session.app.project.urls.systemMedia}search.gif" class="search-icon" />

{literal}
<div id="VAL_TOCH_DOOD_MET_JE_ZOEKRESULTATEN" style="background-color:#fefefe;border:1px solid #999;width:350px;height:450px;font-size:10px;
line-height:12px;position:absolute;text-align:left;padding:1px;overflow-y:scroll;overflow-x:hidden;display:none;z-index:999;white-space:nowrap"></div>

<script>
var searchIndexHasData = false;
function SATAN_HEERST_HARDER_DAN_DE_VOGELGRIEP() {

	var t = $('#VAL_TOCH_DOOD_MET_JE_ZOEKRESULTATEN');
	
	if(t.is(':visible') ) {
		t.css('display','none');
		return;
	} else
	if (searchIndexHasData) {
			t.css('display','block');
			return;
	}
	
	$.ajax({
		url : '../search/ajax_interface.php',
		type: "POST",
		data : ({
			'action' : 'get_search_result_index' ,
			'time' : allGetTimestamp()
		}),
		success : function (data) {
			var data = $.parseJSON(data);
			var tmp = Array();
			for (var i in data) {
				tmp.push('<a href="'+data[i].u+'">'+data[i].l+':'+i+':</a>');
			}
			var p = $('#searchIndexToggle');
			var pos = p.offset();
			t.html(tmp.join("<br />\n"));
			t.css('display','block');
			t.offset({top:pos.top+p.height()+2,left:pos.left});
			searchIndexHasData = true;
		}
	});	
	
	
}
</script>
{/literal}
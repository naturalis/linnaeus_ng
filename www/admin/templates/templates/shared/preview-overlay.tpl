<div id="preview-overlay" style="
	position:absolute;
	top:10px;
	left:0px;
	width:500px;
	height:150px;
	color:#aaa;
	text-align:right;
"
>
<input type="button" value="back to editing" onclick="window.open('{$backUrl}','_self');" />
{if $nextUrl}<input type="button" value="next" onclick="window.open('{$nextUrl}','_self');" />{/if}
<div id="overlay-warning" style="margin-top:5px">
warning - the following stylesheets do not exist yet:<br />
{assign var=i value=0}
{section name=i loop=$cssToLoad}
	{if !file_exists($cssToLoad[i])}{$cssToLoad[i]}<br />{assign var=i value=$i+1}{/if}
{/section}
</div>
</div>
{literal}
<script type="text/JavaScript">
$(document).ready(function(){
{/literal}

	{if $i==0}
	$('#overlay-warning').hide();
	{/if}
	
	$('#main-menu').hide();
	$('#language-change').hide();
	

	var pos = $('#body-container').position();
	$('#preview-overlay').css('left',(pos.left + $('#body-container').width() - $('#preview-overlay').width()));


{literal}
	$('#header-container a').each(function(){
	$(this).attr('href','javascript:void(0);');
	});
	$('#main-menu a').each(function(){
	$(this).attr('href','javascript:void(0);');
	});
	$('#main-menu span').each(function(){
	$(this).attr('onclick','javascript:void(0);');
	});
	$('#search').attr('disabled','disabled');
	$('#languageSelect').attr('disabled','disabled');
	$('#footer-container a').each(function(){
	$(this).attr('href','javascript:void(0);');
	});
	$('#footer-container span').each(function(){
	$(this).attr('onclick','javascript:void(0);');
	});
	
	$('#allNavigationPane a').each(function(){
	$(this).attr('href','javascript:void(0);');
	});
	$('#allNavigationPane button').each(function(){
	$(this).attr('onclick','javascript:void(0);');
	});
	
	$('#allLookupBox').attr('disabled','disabled');	
	
});
</script>
{/literal}

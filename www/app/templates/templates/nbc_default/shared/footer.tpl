	</div>
</div>

<div id="footer">
    <div id="footerInfo">
        <p class="copyright">
            © Naturalis 2005 - {$currdate.year}  -  <a href="http://www.nederlandsesoorten.nl/nsr/nsr/colofon.html" title="{t}Disclaimer{/t}">
            {t}Colofon &amp; Disclaimer{/t}</a>
        </p>
        <a href="#top" class="up">{t}naar boven{/t}</a>
    </div>
</div>


<!-- div id="allLookupList" class="allLookupListInvisible"></div -->

<div id="jDialog" title="" class="ui-helper-hidden"></div>
<div id="tmpcontent" title="" class="ui-helper-hidden"></div>

{literal}
<script type="text/JavaScript">
$(document).ready(function(){
	if(jQuery().prettyPhoto) {
		nbcPrettyPhotoInit();
	}
	$('img').bind('contextmenu',function(e){
		e.preventDefault();
	});	
})
</script>

{snippet}google_analytics.html{/snippet}

{/literal}
</body>
</html>

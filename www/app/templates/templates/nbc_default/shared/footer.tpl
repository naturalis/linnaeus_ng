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
<script type="text/javascript">

  var _gaq = _gaq || [];
  _gaq.push(['_setAccount', 'UA-21555206-1']);
  _gaq.push(['_setDomainName', 'nederlandsesoorten.nl']);
  _gaq.push(['_trackPageview']);

  (function() {
    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
  })();

</script>
{/literal}
</body>
</html>

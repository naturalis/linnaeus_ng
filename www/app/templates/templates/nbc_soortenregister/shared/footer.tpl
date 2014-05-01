	</div>
	<div id=push></div>
</div>

<div id="footer">
    <div id="footerInfo">
        <p class="copyright">
            © Naturalis 2005 - {$currdate.year}  -  <a href="http://www.nederlandsesoorten.nl/nsr/nsr/colofon.html" title="Disclaimer">
            Colofon &amp; Disclaimer</a>
        </p>
        <a href="#top" class="up">naar boven</a>
    </div>
</div>


<!-- div id="allLookupList" class="allLookupListInvisible"></div -->

<div id="jDialog" title="" class="ui-helper-hidden"></div>
<div id="tmpcontent" title="" class="ui-helper-hidden"></div>
<div id="hint-box"></div>

{literal}
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

$(document).ready(function(){
	allLookupAlwaysFetch=true;
});
  
</script>

{/literal}
</body>
</html>

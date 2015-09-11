	</div>
	<div id=push></div>
</div>




<style>
/* 
	styles stolen from the drupal 
	front-end to fix the footer
*/
#footer {
    background-color: #58574b;
    color: #9b9a93;
    font-size: 95%;
    padding-top: 20px;
    clear: both;
    width: 100%;
    height: auto;
}

#footer h2 {
    font-size: 18px;
    padding-bottom: 6px;	
    color: #ffffff;
    font-family: Arial;
    font-weight: normal;
}
#footer .kader {
    text-align: left;
    margin-bottom: 25px;
}

#footer .kader ul, .kader li {
    list-style: outside none none;
    padding: 0;
    float: left;
    margin: 11px 20px 0 0;
    width: 170px;
}

#footer .kader ul {
    margin-top: 0px;
}

#footer .kader li {
    background: rgba(0, 0, 0, 0) none repeat scroll 0 0;
    margin-bottom: 1px;
    padding-left: 0;
}

#header {
    height: 160px;
}

#footer {
	height: auto;
}
</style>

<div id="footer">

    <div id="footerInfo">
        <p class="copyright">
            © Naturalis 2005 - {$currdate.year}  -  <a href="http://www.nederlandsesoorten.nl/nsr/nsr/colofon.html" title="Disclaimer">
            Colofon &amp; Disclaimer</a>
        </p>
        <a href="#top" class="up">{t}naar boven{/t}</a>
    </div>
</div>

<!-- div id="allLookupList" class="allLookupListInvisible"></div -->

<div id="jDialog" title="" class="ui-helper-hidden"></div>
<div id="tmpcontent" title="" class="ui-helper-hidden"></div>
<div id="hint-box"></div>


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


function getDrupalFooter()
{
	var url="http://www.nederlandsesoorten.nl/content/footer-only-lng";

	$.get( url, function( data )
	{
		var bits=data.split(/(<!-- REGION: footerInfo-->|<!-- \/#footerInfo -->)/);
		if (bits[2] && bits[2].length>10) $('#footer').html( bits[2] );
	});
}


$(document).ready(function()
{
	allLookupAlwaysFetch=true;
	getDrupalFooter();
});
  
</script>
</body>
</html>

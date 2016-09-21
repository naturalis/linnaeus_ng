	</div>
</div>

    <div class="footerContainer">
        <div class="footer">
            <div class="row">
                <div class="col-md-12">
                    <a class="toggleFooterLinks">In samenwerking met</a>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12 cooperation">
                    <div class="footerLinkContainer">
                        <ul class="footerLinks">
                            <li><a href="">Ministerie van Economische Zaken</a></li>
                            <li><a href="">Ecyclopedia of Life</a></li>
                            <li><a href="">Universiteit van Amsterdam</a></li>
                            <li><a href="">Nationaal Herbarium Nederland</a></li>
                            <li><a href="">Centraal Bureau voor Schimmelcultures</a></li>
                            <li><a href="">Bryologische en Lichenologische Werkgroep - KNNV</a></li>
                            <li><a href="">Nederlandse Mycologische Vereniging</a></li>
                            <li><a href="http://www.ndff.nl/">Nationale Databank Flora en Fauna</a></li>
                        </ul>
                        <ul class="footerLinks">
                            <li><a href="">Fauna Europaea</a></li>
                            <li><a href="">MARBEF</a></li>
                            <li><a href="">Nederlandse Voedsel- en Warenautoriteit</a></li>
                            <li><a href="">NLBIF</a></li>
                            <li><a href="">Centraal Bureau voor Statistiek</a></li>
                            <li><a href="">Nederlandse Entomologische Vereniging</a></li>
                            <li><a href="">Nederlandse Malacologische Vereniging</a></li>
                        </ul>
                        <ul class="footerLinks">
                            <li><a href="">Waarneming.nl</a></li>
                            <li><a href="">FLORON</a></li>
                            <li><a href="">Stichting Anemoon</a></li>
                            <li><a href="">De Vlinderstichting</a></li>
                            <li><a href="">SOVON Vogelonderzoek Nederland</a></li>
                            <li><a href="">EIS Kenniscentrum Insecten en andere ongewervelden</a></li>
                            <li><a href="">Stichting RAVON</a></li>
                            <li><a href="">Zoogdiervereniging</a></li>
                        </ul>
                        <div class="logos">
                            <a target="_blank" href="http://www.eol.org">
                                <img src="{$baseUrl}app/style/img/eol_logo.svg" class="eol_logo">    
                            </a>
                            <a target="_blank" href="http://www.eis-nederland.nl">
                                <img src="{$baseUrl}app/style/img/eis_logo.png" class="eis_logo">    
                            </a>
                        </div>
                        
                    </div>

                </div>
            </div>
            <div class="row">
                <div class="col-md-4 sitemapLinks mobile">
                    <ul>
                        <li><a href="">Sitemap</a></li>
                        <li><a href="">Links</a></li>
                        <li><a href="">English</a></li>
                        <li><a href="">Contact</a></li>
                    </ul>    
                </div>
                <div class="col-md-4 colofonLinks">
                    <div class="colofonContainer">
                        <div class="colofon">
                            </span><a href="http://www.nederlandsesoorten.nl/nsr/nsr/colofon.html" class="blue" title="Disclaimer">Colofon &amp; Disclaimer</a>
                            <span class="copyright">© Naturalis 2005 - {$currdate.year}
                        </div>
                    </div>
                </div>
                <div class="col-md-4 sitemapLinks desktop">
                    <ul>
                        <li><a href="">Sitemap</a></li>
                        <li><a href="">Links</a></li>
                        <li><a href="">English</a></li>
                        <li><a href="">Contact</a></li>
                    </ul>    
                </div>
                <div class="col-md-4 upLink">
                    <a href="#top" class="blue up">{t}Naar boven{/t}</a>
                </div>
            </div>
        </div>
    </div>

<!-- div id="allLookupList" class="allLookupListInvisible"></div -->

<div id="jDialog" title="" class="ui-helper-hidden"></div>
<div id="tmpcontent" title="" class="ui-helper-hidden"></div>
<div id="hint-box" style="display:none"></div>

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


$(document).ready(function()
{
	allLookupAlwaysFetch=true;
});
  
</script>

{snippet}change_footer.html{/snippet}

</body>
</html>

<?php
	include_once('_header.php');
?>
            <div class="sub-header-wrapper" style="padding-bottom:1px;height:60%">
                <div class="sub-header" style="padding-bottom:200px;width:793px;margin-left:auto;margin-right:auto;height:100%">
                    <div class="sub-header-inner extra-inner" style="position:static;margin-left:0;padding:50px;padding-top:20px;width:693px;padding-top:0px;">
                        
                        
                        <h1>Colofon</h1>
                        <body><p>Dierenzoeker versie 2.2.</p><p><strong>Mede mogelijk gemaakt door</strong><br/><a href="http://www.rijksoverheid.nl/ministeries/eleni" target="_blank">Regeling Draagvlak Natuur (Ministerie van EL&amp;I)</a><br/><a href="http://www.cultuurfonds.nl/" target="_blank">Prins Bernhard Cultuurfonds<br/></a><a href="http://www.nationaalgroenfonds.nl" target="_blank">Nationaal Groenfonds</a></p><p><strong>Conceptontwikkeling</strong><br/><a href="http://www.naturalis.nl" target="_blank">Naturalis Biodiversity Center<br/></a><a href="http://www.ntr.nl" target="_blank">NTR</a> / <a href="http://www.hetklokhuis.nl" target="_blank">Het Klokhuis<br/></a><a href="http://www.eis-nederland.nl" target="_blank">EIS-Nederland</a></p><p><strong>Ontwerp en front-end<br/></strong><a href="http://www.ijsfontein.nl" target="_blank">IJsfontein</a></p><p><strong>Back-end<br/></strong><a href="http://www.trezorix.nl" target="_blank">Trezorix</a></p><p><strong><strong>Logo Dierenzoeker</strong><br/></strong><a href="http://www.dawn.nl" target="_blank">Dawn</a></p><p><strong>Determinatiesleutel</strong><br/><a href="http://www.naturalis.nl" target="_blank">Naturalis Biodiversity Center</a><br/><a href="http://www.eis-nederland.nl" target="_blank">EIS-Nederland</a></p><p><strong>Dierteksten</strong><br/><a href="http://www.naturalis.nl" target="_blank">Naturalis Biodiversity Center</a><br/><a href="http://www.eis-nederland.nl" target="_blank">EIS-Nederland</a></p><p><strong>Lesbrieven<br/></strong><a href="http://www.naturalis.nl" target="_blank">Naturalis Biodiversity Center</a><strong> </strong></p><p><strong>Tekeningen<br/></strong>Bas Blankevoort (<a href="http://www.naturalis.nl" target="_blank">Naturalis Biodiversity Center</a>)<br/>Erik-Jan Bosch (<a href="http://www.naturalis.nl" target="_blank">Naturalis Biodiversity Center</a>)<br/>Jeroen de Rond (<a href="http://www.naturalmedia.nl/" target="_blank">Natural Media</a>)<br/>Maaike Wijnands (<a href="http://www.oehoe.info/" target="_blank">oeHoe</a>; <a href="http://www.naturalis.nl" target="_blank">Naturalis Biodiversity Center</a>)<br/>Manon Zuurmond (<a href="http://www.manonproject.com" target="_blank">Manon Project</a>)</p><p><strong>Foto's<br/></strong>Jan van Asselt<br/>Madeleine Assink-Kleve<br/>Ab H. Baas<br/>Aat Bender<br/>Herman Berkhoudt<br/>Martin Bonte<br/>Karen Bosma<br/>Han Bouwmeester<br/>Wilbert van der Broek<br/>Annelies Buijs<br/>Wijnand van Buuren<br/>Gert-Jan Cromwijk<br/>Menno van Duijn<br/>Henny van Egdom<br/>Tim Faasen<br/>Wanda Floor-Zwart<br/>Ben Hamers<br/>Antonia Hens<br/>Jelger Herder<br/>Chris Herzog<br/>Theodoor Heijerman<br/>Marcel Holtjer<br/>Luc Hoogenstein<br/>Annemieke Hoozemans<br/>Dirk Huitzing<br/>Sytze Jongma<br/>Hans Jonkman<br/>Hannie Joziasse<br/>Guido Keijl<br/>Jan Kersten<br/>Michel Klemann<br/>Roy Kleukers<br/>René Krekels<br/>Susanne Kuijpers<br/>Hans van der Meulen<br/>Susan Meijnders<br/>Ans Molenkamp<br/>Jinze Noordijk<br/>Peter Notenbomer<br/>Henk Olieman<br/>Jeroen van Ophoven<br/>Hans Osinga<br/>Jan Piet Oudwater<br/>Jan Paul<br/>Bauke de Ruiter<br/>Ron Schoone<br/>Jankees Schwiebbe<br/>John T. Smit<br/>Pieter Smit<br/>Frank Stokvis<br/>Kees Venneker<br/>Nick Upton<br/>Hans Vink<br/>Jan Westgeest<br/>Albert de Wilde<br/>Frank de Winter</p></body>                        
                        <script language="JavaScript">
                            $(function(){
                                $(".optv-wrapper, .onderwijs-wrapper").append("<div class='clearer' />");
                                $(".optv-popup-link").click(function(e){
                                    e.preventDefault();                                    
                                    openStream($(this).attr("href"))
                                    return false;
                                });
                                $(".extra-inner").find("p:first").css("width", "620px");
                                
                                                                $(".extra-inner p").css("font-weight", "normal");
                                                                
                                $(".onderwijs-popup-link").attr("target", "_blank");
                                
                                $.backstretch("/app/webroot//img/desktop/background_blurry.jpg");
                            });
                            
                            //Copied from hetklokhuis.nl:
                            
                            function openWindow(p,n,w,h)
                            {
                                //console.log(w,h);                                
                                var win = window.open(p, n, "width=" + w + ",height=" + h +",toolbar=no,location=no,directories=no,status=0,resizable=no,scrollbars=no,menubar=no");
                                //win.moveTo(((screen.width/2)-(w/2)),((screen.height/2)-(h/2)));
                            }
                            
                            function openStream(p)
                            {
                                var n = 'klokhuisstream';
                                var w = 1024;
                                var h = 700;
                                openWindow( p, n, w, h);
                            }
                            
                        </script>
                     </div>
                    </div> 
                </div>
<?php
	include_once('_footer.php');
?>

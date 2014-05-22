<?php
	include_once('_header.php');
?>

            <div class="sub-header-wrapper" style="padding-bottom:1px;height:60%">
                <div class="sub-header" style="padding-bottom:200px;width:793px;margin-left:auto;margin-right:auto;height:100%">
                    <div class="sub-header-inner extra-inner" style="position:static;margin-left:0;padding:50px;padding-top:20px;width:693px;padding-top:0px;">
                        
                        
                        <h1>Op je mobiel</h1>
                        <body><p>De Dierenzoeker is ook beschikbaar voor smartphones en tablets.</p><p><strong>App voor Android en iOS</strong></p><p><a href="https://play.google.com/store/apps/details?id=nl.naturalis.dierenzoeker" target="_blank"><img class="intern" title="Android app in Google Play" src="http://determinatie.ncb.rnatoolset.net/Media/Image/c4705e85-1cb3-4d61-b5d4-411ffcb1aa35/android-app-on-google-play_200px.png" alt="Android app in Google Play" width="200" height="68"/></a></p><p><a href="https://itunes.apple.com/nl/app/id699543364"><img class="intern" title="iOS app in App Store" src="http://determinatie.ncb.rnatoolset.net/Media/Image/c4705e85-1cb3-4d61-b5d4-411ffcb1aa35/Available_on_the_App_Store_(black)_200px.png" alt="iOS app in App Store" width="200" height="59" border="0"/></a></p><p>De Dierenzoeker voor Android telefoons is te downloaden in de <a href="https://play.google.com/store/apps/details?id=nl.naturalis.dierenzoeker" target="_blank">Google Play app store</a>. De iOS versie is voor iPhone en iPad te downloaden in de <a href="https://itunes.apple.com/nl/app/id699543364" target="_blank">App Store</a>. Deze app werkt - wanneer eenmaal geïnstalleerd - ook zonder wifi- of mobiele internetverbinding.</p><p>NB. De iPad versie is op dit moment identiek aan de versie voor smartphones. De gebruikerservaring is dus nog niet geoptimaliseerd voor grotere tabletschermen.</p><p><strong>Mobiele website<br/><br/></strong>Wil je geen app installeren op je smartphone of tablet? Geen probleem, dan kun je onze mobiele website proberen. Om deze te gebruiken ga je in de webbrowser van je smartphone naar www.dierenzoeker.nl. Je krijgt dan automatisch de mobiele versie gepresenteerd. Deze vereist een internetverbinding (wifi of mobiel netwerk) op je telefoon.</p><p><img class="intern" title="Dierenzoeker mobiele website" src="http://determinatie.ncb.rnatoolset.net/Media/Image/c4705e85-1cb3-4d61-b5d4-411ffcb1aa35/iphone-dierenzoeker_v3_transparant_1.png" alt="Dierenzoeker mobiele website" width="250" height="449"/></p></body>                        
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

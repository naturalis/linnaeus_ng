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
						
						$.backstretch("../../media/system/skins/dierenzoeker/background_blurry.jpg");
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
          <div class="footer">
                <div class="footer-inner">
                <ul>
                    <li><a href="mobiel.php" class="no-text" id="mobiel-btn">Op je mobiel</a></li>
                    <li><a href="faq.php" class="no-text" id="faq-btn" >Veel gestelde vragen</a></li>
                    <li><a href="colofon.php" class="no-text" id="colofon-btn">Colofon</a></li>
                </ul>
                <div class="clearer"></div>
                    <a href='http://www.naturalis.nl' class="no-text" target="_blank" style='display:block;position:absolute;left:373px;top:30px;width:79px;height:83px;border: 0px solid black;'>Naturalis</a>
<a href='http://www.hetklokhuis.nl' class="no-text" target="_blank" style='display:block;position:absolute;left:458px;top:30px;width:67px;height:62px;border: 0px solid red;'>Klokhuis</a>
<a href='http://www.eis-nederland.nl' class="no-text" target="_blank" style='display:block;position:absolute;left:535px;top:30px;width:101px;height:68px;border: 0px solid orange;'>EIS</a>
<a href='http://www.cultuurfonds.nl' class="no-text" target="_blank" style='display:block;position:absolute;left:646px;top:30px;width:53px;height:68px;border:0px solid purple;'>Prins Bernhard fonds</a>
<a href='http://www.rijksoverheid.nl/ministeries/ez' target="_blank" class="no-text" style='display:block;position:absolute;left:722px;top:30px;width:153px;height:68px;border: 0px solid blue;'>Ministerie voor landbouw en innovatie.</a>

<div class="social-media">
<a href="http://www.facebook.com/dierenzoeker" target="_blank"><img src="../../media/system/skins/dierenzoeker/facebook.png" alt="" /></a>
<a href="http://twitter.com/dierenzoeker" target="_blank"><img src="../../media/system/skins/dierenzoeker/twitter.png" alt="" style="width:32px;height:32px;" /></a>
</div>                
                </div>
            </div>

       </div>
                    
    </body>
<script>
(function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){ (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
})(window,document,'script','//www.google-analytics.com/analytics.js','ga');

ga('create', 'UA-27823424-1', 'dierenzoeker.nl');
ga('send', 'pageview');

</script>
</html>
	<!DOCTYPE html>
	<html>
		<head>
		<title>Dierenzoeker</title>
		<meta property="og:description" content="Zie je een dier in je huis of tuin, en weet je niet wat het is? Kijk goed en ontdek het in de Dierenzoeker."/>
		<meta http-equiv="Content-type" content="text/html;charset=UTF-8" />
		<link rel="stylesheet" type="text/css" href="../../style/dierenzoeker/basics.css" />
		<link rel="stylesheet" type="text/css" href="../../style/dierenzoeker/jquery-ui-1.10.0.custom.min.css" />
		<link rel="stylesheet" type="text/css" href="../../style/dierenzoeker/prettyPhoto/prettyPhoto.css" />

	<link href="/linnaeus_ng/app/media/system/skins/dierenzoeker/favicon.ico" type="image/x-icon" rel="icon" />
    <link href="/linnaeus_ng/app/media/system/skins/dierenzoeker/favicon.ico" type="image/x-icon" rel="shortcut icon" />        

		<script type="text/javascript" src="../../javascript/jquery-3.0.0.min.js"></script>
		<script type="text/javascript" src="../../javascript/project_specific/backstretch.js"></script>

		</head>
        
        
        
        
    <body style="background-position-y:115px;background: url('../../media/system/skins/dierenzoeker/background_blurry.jpg');">       
        <div class="main-wrapper">
            
            <div class="header" style="height:115px;">
                <div class="header-inner">

		            {include file="../shared/top-banners.tpl"}


                </div><!-- /header-inder -->            
            </div><!-- /header -->                        
            <div class="sub-header-wrapper" style="padding-bottom:1px;height:60%">
                <div class="sub-header" style="padding-bottom:200px;width:793px;margin-left:auto;margin-right:auto;height:100%">
                    <div class="sub-header-inner extra-inner" style="position:static;margin-left:0;padding:50px;padding-top:20px;width:693px;padding-top:0px;">


		{$page.content}

{literal}

<!-- Begin comScore Inline Tag 1.1302.13 --> 
<script type="text/javascript"> 
// <![CDATA[
function udm_(e){var t="comScore=",n=document,r=n.cookie,i="",s="indexOf",o="substring",u="length",a=2048,f,l="&ns_",c="&",h,p,d,v,m=window,g=m.encodeURIComponent||escape;if(r[s](t)+1)for(d=0,p=r.split(";"),v=p[u];d<v;d++)h=p[d][s](t),h+1&&(i=c+unescape(p[d][o](h+t[u])));e+=l+"_t="+ +(new Date)+l+"c="+(n.characterSet||n.defaultCharset||"")+"&c8="+g(n.title)+i+"&c7="+g(n.URL)+"&c9="+g(n.referrer),e[u]>a&&e[s](c)>0&&(f=e[o](0,a-8).lastIndexOf(c),e=(e[o](0,f)+l+"cut="+g(e[o](f+1)))[o](0,a)),n.images?(h=new Image,m.ns_p||(ns_p=h),h.src=e):n.write("<","p","><",'img src="',e,'" height="1" width="1" alt="*"',"><","/p",">")};udm_('http'+(document.location.href.charAt(4)=='s'?'s://sb':'://b')+'.scorecardresearch.com/b?c1=2&c2=17827132&ns_site=po-totaal&name=hetklokhuis.dierenzoeker.optv.page&potag1=hetklokhuis&potag2=dierenzoeker&potag3=ntr&potag4=ntr&potag5=programma&potag6=video&potag7=npozapp&potag8=site&potag9=site&ntr_genre=jeugd');
// ]]>
</script>
<noscript><p><img src="http://b.scorecardresearch.com/p?c1=2&amp;c2=17827132&amp;ns_site=po-totaal&amp;name=hetklokhuis.dierenzoeker.optv.page&amp;potag1=hetklokhuis&amp;potag2=dierenzoeker&amp;potag3=ntr&amp;potag4=ntr&amp;potag5=programma&amp;potag6=video&amp;potag7=npozapp&amp;potag8=site&amp;potag9=site&amp;ntr_genre=jeugd" height="1" width="1" alt="*"></p></noscript> 
<script type="text/javascript" language="JavaScript1.3" src="http://b.scorecardresearch.com/c2/17827132/cs.js"></script>
<!-- End comScore Inline Tag -->
{/literal}

<script type="text/JavaScript">
$(document).ready(function(){
	document.title='{$headerTitles.title|@escape}';
});
</script>
{literal}

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
{/literal}
        
        {include file="../shared/bottom-banners.tpl"}

{literal}

       </div>
                    
    </body>
<script async src="https://www.googletagmanager.com/gtag/js?id=UA-27823424-1"></script>
<script>
        window.dataLayer = window.dataLayer || [];
        function gtag() {
            dataLayer.push(arguments);
        }
        gtag('js', new Date());

        gtag('config', 'UA-27823424-1', { 'anonymize_ip': true });

        $(document).ready(function(e)
        {
            $.backstretch("../../media/system/skins/dierenzoeker/background.jpg");
        });
</script>
{/literal}
</html>
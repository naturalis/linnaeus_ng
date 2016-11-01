	</div>
</div>

    <div class="footerContainer" id=footercontainer>
    </div>

<!-- div id="allLookupList" class="allLookupListInvisible"></div -->

<div id="jDialog" title="" class="ui-helper-hidden"></div>
<div id="tmpcontent" title="" class="ui-helper-hidden"></div>
<div id="hint-box" style="display:none"></div>

{if $googleAnalyticsCode}{include file="../shared/_google_analytics_code.tpl"}{/if}

<script type="text/javascript">

function setHeaderAndFooter()
{
	var nsr_domain = "http://www.nederlandsesoorten.nl";
	
	$.ajax({
		url : "../../../shared/tools/remote_service.php",
		type: "POST",
		data : ({
			url : encodeURIComponent(nsr_domain),
			original_headers : 1
		}),
		success : function( response )
		{
			try {
				$('body').append('<div id=ghost style="display:none"></div>');
				var filtered=response.replace( new RegExp("<script([^>]*)>","g"),"<script>");
				$('#ghost').append( $( filtered ) );
			} catch(e) {
			} finally {
				var footer = $('#block-nlsoort-components-nlsoort-components-footer').html();
				$( '#footercontainer' ).html( footer );
				$( '#footercontainer img' ).each(function()
				{
					$(this).attr("src",nsr_domain+$(this).attr("src"));
				});
				$( '.menuContainer' ).first().html( $('#block-nlsoort-components-nlsoort-components-main-menu').html() );
			}
		}
	});
}

$(document).ready(function()
{
	allLookupAlwaysFetch=true;
	setHeaderAndFooter();
});
  
</script>

{snippet}change_footer.html{/snippet}

</body>
</html>





















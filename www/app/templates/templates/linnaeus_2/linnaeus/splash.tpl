{include file="../shared/header.tpl"}
<div id="header-titles"></div>

<div id="page-main">
<p>
<h2>splash</h2>
</p>
<p>
<div id="status">
	loading<span id="dots"></span>
</div>
</p>
</div>

{literal}
<script type="text/JavaScript">
$(document).ready(function(){

    var dotCounter = 0;
	var dotMax = 5;
    (function addDot() {
      setTimeout(function() {
        if (dotCounter++ < dotMax) {
          $('#dots').append('.');
          addDot();
        } else
        if (dotCounter>=dotMax) {
          $('#dots').empty();
		  dotCounter=0;
          addDot();
        }
      }, 100);
    })();

	$('#status').load('?go=load', function(response,status,xhr) {

/* take me out for auto-forwarding after splash/preload */
$('#status').html($('#status').html()+'<br />'+'[temp fix so ruud can SEE] <a href="{/literal}{$startUrl}{literal}">'+_('Continue to ')+'{/literal}{$session.app.project.title}{literal}</a>');
return;
/* /take me out for auto-forwarding after splash/preload */

		if (status=='error') {
			$('#status').html('<a href="{/literal}{$startUrl}{literal}">'+_('Continue to ')+'{/literal}{$session.app.project.title}{literal}</a>');
		} else {			
			$('#status').html('done').fadeOut(200, function() {
				window.location.href='{/literal}{$startUrl}{literal}';
			});
		}
	});

	
});
</script>
{/literal}


{include file="../shared/footer.tpl"}
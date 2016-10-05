<script type="text/javascript">

var url;
var max=5;
var results=[];
var general_label="Visit page";
var remoteServiceUrl="../../../shared/tools/remote_service.php";

function draw()
{
	var b=[];
	for(var i=0;i<results.length;i++)
	{
		if (i>=max) continue;
		var tpl=fetchTemplate( 'aSoundTpl' );
		b.push(
			tpl
				.replace(/%MP3-PATH%/,results[i].media_url)
				.replace(/%LABEL%/,general_label)
				.replace(/%URL%/g,results[i].url)
			);
	}
	
	$( '#results' ).html( b.join("\n") );
}

function run()
{
	$.ajax({
		url : remoteServiceUrl,
		type: "POST",
		data: ({
			url : encodeURIComponent(url),
			original_headers : 1,
			request_headers: "Accept: application/json"
		}),
		success : function(data)
		{
			results=data.results;
			draw();
		},
		complete : function( jqXHR, textStatus )
		{
			//"success", "notmodified", "nocontent", "error", "timeout", "abort", "parsererror"
			if( textStatus != "success" )
			{
				$( '#results' ).html( fetchTemplate( 'errorOccurredTpl' ).replace( '%STATUS%', textStatus ) );
			}
		}
	});
}

$(document).ready(function()
{
	acquireInlineTemplates();
	url='{$external_content->full_url}';
	max={if $external_content->template_params_decoded->max}{$external_content->template_params_decoded->max}{else}10{/if};
	{if $external_content->template_params_decoded->general_label}general_label='{$external_content->template_params_decoded->general_label}';{/if}
	run();
});
</script>

<p>

    <h2 id="name-header">{$requested_category.title}</h2>

	<!-- 
	<a href="{$external_content->full_url}" target="_blank">{$external_content->full_url}</a>
    -->

    {if $content}
    <p>
        {$content}
    </p>
    {/if}
    
    <div id=results></div>
    <div>
        <a href="http://www.xeno-canto.org/species/{$external_content->subst_values['%GENUS%']}-{$external_content->subst_values['%SPECIES%']}" target="_blank">Naar de soortspagina op Xeno Canto.</a>
    </div>

</p>

<div class="inline-templates" id="aSoundTpl">
<!--
	<p>
    <audio class="my_audio" controls preload="none">
        <source src="%MP3-PATH%" type="audio/mpeg">
    </audio><br />
    <a href="%URL%" target="_blank" title="%LABEL%">%URL%</a>
    </p>
-->
</div>

<div class="inline-templates" id="errorOccurredTpl">
<!--
	Er is een fout opgetreden (%STATUS%).
-->
</div>

<script type="text/javascript">

var url;
var max=5;
var results=[];
var general_label="Visit page";
var remoteServiceUrl="../../../shared/tools/remote_service.php";
var spatial_filter;

function postProcess()
{
	var b=[];
	for(var i=0;i<results.length;i++)
	{
		if ((spatial_filter!=null && results[i].spatial==spatial_filter) || spatial_filter==null)
		{
			b.push(results[i]);
		}
	}
	results=b;
}

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
				.replace(/%LICENSE%/g,results[i].rights)
				.replace(/%CREATOR%/g,results[i].creator)
				.replace(/%SPATIAL%/g,results[i].spatial)
				.replace(/%TEMPORAL%/g,results[i].temporal)
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
			console.dir(data);
			results=data.results;
			postProcess();
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
	{if $external_content->template_params_decoded->spatial_filter}spatial_filter='{$external_content->template_params_decoded->spatial_filter}';{/if}
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
        <a href="http://www.xeno-canto.org/species/{$external_content->subst_values['%GENUS%']}-{$external_content->subst_values['%SPECIES%']}" target="_blank">
        Naar de soortpagina op Xeno-canto.
        </a>
    </div>

</p>

<div class="inline-templates" id="aSoundTpl">
<!--
	<p>
    <audio class="my_audio" controls preload="none">
        <source src="%MP3-PATH%" type="audio/mpeg">
    </audio><br />
    <a href="%URL%" target="_blank" title="%LABEL%">%URL%</a><br />
    Opegnomen door: %CREATOR% (%SPATIAL%, %TEMPORAL%)<br />
    Licentie: <a href="%LICENSE%">%LICENSE%</a>
	</p>
-->
</div>

<div class="inline-templates" id="errorOccurredTpl">
<!--
	Er is een fout opgetreden (%STATUS%).
-->
</div>

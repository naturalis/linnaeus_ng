<!--
	{"url":"http:\/\/www.digibird.org\/api\/objects?platform=xeno-canto&genus=%GENUS%&species=%SPECIES%","substitute":{"%SPECIES%":"name:specific_epithet","%GENUS%":"name:uninomial"},"subst_transformation":["none","none"],"substitute_encode":"urlencode","parameters":[],"param_transformation":[],"parameter_encode":"urlencode","rank":"75","check_type":"query","query":"select count(*) as result from %PRE%taxon_quick_parentage _sq\r\nleft join %PRE%taxa _e on _sq.taxon_id = _e.id and _sq.project_id = _e.project_id\r\nleft join %PRE%projects_ranks _pr on _e.rank_id = _pr.id and _e.project_id = _pr.project_id\r\nwhere _sq.project_id = %pid% and _e.id = %tid% and MATCH(_sq.parentage) AGAINST ( (select id from taxa where taxon = 'Aves' ) in boolean mode) and _pr.rank_id >= 75","link_embed":"embed_link","template":"_tab_ext_digibird.tpl","template_params":"{ \"max\": 5, \"general_label\": \"Naar de pagina op Xeno Canto\" }"} |

-->
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

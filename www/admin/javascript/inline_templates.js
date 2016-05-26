/*

	inline template format:

	<div class="inline-templates" id="exampleTpl">
		<b>template content</b>
	</div>

	call acquireInlineTemplates(); on page init
	
	call fetchTemplate( 'exampleTpl' ); to fetch template
	
	please be aware that acquireInlineTemplates() uses jQuery's
	html() function, which in turn uses the native innerHtml()
	function, which does not necessarily returns the literal
	content of an element: IE sometimes adds extra quotes, FF
	converts html-attributes to lowercase. be sure to take this
	into account when rendering your template, for instance by
	using (the slower)
		.replace(/%TAG%/i, 'value')
	rather than the regular
		.replace('%TAG%', 'value') 
	when appropriate.

*/


var inline_templates=Array();

function acquireInlineTemplates()
{
	$( '.inline-templates' ).each(function()
	{
		inline_templates.push({id:$(this).attr('id'),tpl:$(this).html().trim(),use:0});
	});
}

function fetchTemplate( name )
{
	var template="";

	$.each(inline_templates, function( index, value )
	{
		if( value && value.id==name )
		{
			template=value.tpl;
			inline_templates[index].use++;
		}
	});

	return template;
}

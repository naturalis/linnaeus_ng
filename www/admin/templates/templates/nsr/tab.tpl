{include file="../shared/admin-header.tpl"}

<style>
label {
	font-size:0.8em;
}
</style>


<div id="page-main" class="tab-definition">

	<h2>{$page.page}</h2>
	<h3>{t}special attributes{/t}</h3>

	<!-- (id {$page.id}) -->

	<form method="post" id="theForm">
    <input type="hidden" name="id" value="{$page.id}" />
    <input type="hidden" name="action" value="save" />
    <input type="hidden" name="rnd" value="{$rnd}" />

    <table>
    	<tr>
            <td>
				<label><input type="checkbox" name="always_hide"{if $page.always_hide==1} checked="checked"{/if} /> {t}always hide{/t}</label>
                <div class="explanation">
                    {t}hides the tab from the public menu, even if it contains published content. tab can still be accessed
                    directly. useful to directly retrieve specific content for a taxon, for instance for external use in a associated
                    project.{/t}<br />
                    {t}use{/t} <code>http://your.domain/linnaeus_ng/app/views/species/nsr_taxon.php?cat={$page.id}&id=[taxon id]</code> {t}to access the tab.{/t}
                </div>
			</td>
		</tr>

	   	<tr><td>&nbsp;</td></tr>

		{if $use_page_blocks}

    	<tr>
            <td><u>content</u></td>
		</tr>
    	<tr>
            <td>

            	<table>
                    <tr>
                    	<td>your page</td>
                        <td style="padding-left:10px;">available blocks</td>
                    </tr>
                    <tr>
                        <td style="border:1px solid #ddd;min-width:200px;padding:0 10px 0 10px;">
                            <ul id="block_list">
                            </ul>
                        </td>
                        <td style="padding-left:10px;">
                            <ul class="pick-list">
                            {foreach $tabs v k}
                            {* if $v.type=='auto' || ($v.external_reference_decoded && $v.external_reference_decoded->link_embed|@substr:0:8=='template') *}
                            {if $v.type=='auto'}
                            <li><span class="left-arrow" title="{t}add{/t}" onclick="addBlock( { id: {$v.id}, label: '{$v.page}' } );">&larr;</span>
                            {if $v.type=='auto'}<span class="auto-tab">{$v.page}</span>{else}{$v.page}{/if}
                            </li>
                            {/if}
                            {/foreach}
                            </ul>
                        </td>
                    </tr>
				</table>
                <div class="explanation">
				Only automatic tabs are allowed.
                </div>
                
			</td>
		</tr>

    	<tr><td>&nbsp;</td></tr>

        {/if}

    	<tr>
            <td>
            	<u>{t}external reference{/t}</u>

                <div class="explanation">
                	<p>
                    {t}URL to point to an external page or webservice. parameterization can be done through substitution and/or parameters. these achieve
                    similar goals, but work slightly different:{/t}
                    </p>
                    <p>
                    <ul>
                        <li>{t}use substitutions to replace placeholders in the URL itself with taxon-dependent values at runtime;{/t}</li>
                        <li>{t}use parameters to add name/value pairs to the query string of the URL.{/t}</li>
                    </ul>
                    </p>
                    <p>
                    {t}the data check is performed at runtime to decide whether the tab should be displayed in the taxon's menu (i.e., if any data is
                    available). when 'no check' is selected, the tab is always displayed. the reference can be implemented as an actual link, navigating away from the site (or opening in a new window), or as embedded, in
                    which case a template should be defined in which to display the retrieved data.{/t}<br />
                    </p>
					<p>
                    {t}please note that when used as direct link, or for retrieving remote data, the full URL will have to be valid at run-time.{/t}
                    </p>
					<p>
                    {t}the URL can be "abused" for the transportation of multiple data-values to a template. for instance, multiple parameterized
                    URL's could be entered, separated wiht line feeds. when combined with the presentation option "embed parameterized URL only", those would
                    be supplied to the template as the <code>$external_content->full_url</code> template-variable, which could then be split and processed
                    further. in this way, multiple URL's could be supplied at once, which can be useful for things like loading multiple layers on a map.{/t}
                    {t}(ps, if you do this, you will likely get an 'Invalid URL'-warning, which can be ignored){/t}
                    </p>
                </div>

            	<table>
                	<tr>
                    	<td class="sublabel">link</td>
                        <td>
                        	<table class="subsublabel">
                            	<tr class="tr-highlight">
                                    <td>{t}URL:{/t}</td>
                                    <td><textarea name="external_reference[url]">{$page.external_reference_decoded->url}</textarea></td>
                                </tr>
								<tr class="tr-highlight">
                                    <td>
                                    	{t}substitutions:{/t}<br />
										<a href="#" onclick="add_subst();return false;">{t}add{/t}</a>
                                    </td>
                                    <td>
                                    	<div id="substitutions" style="float:left;"></div>
                                    	<div id="substitutions_transformations"></div>
                                        <div style="clear:both;margin-bottom:5px;">
                                            {t}encoding method:{/t}
                                            {foreach $encoding_methods v k}
                                            <label>
                                                <input type="radio" name="external_reference[substitute_encode]" value="{$v}" {if $page.external_reference_decoded->substitute_encode==$v || (!$page.external_reference_decoded->substitute_encode && $k==1) } checked="checked"{/if} />{$v}</label>
                                            {/foreach}
                                        </div>
                                    </td>
                                </tr>
                                
                                <tr class="tr-highlight">
                                    <td>
                                    	{t}parameters:{/t}<br />
										<a href="#" onclick="add_param();return false;">{t}add{/t}</a>
									</td>
                                    <td>
                                    	<div id="parameters" style="float:left;"></div>
                                    	<div id="parameters_transformations"></div>
                                        <div style="clear:both;margin-bottom:5px;">
                                            {t}encoding method:{/t}
                                            {foreach $encoding_methods v k}
                                            <label><input type="radio" name="external_reference[parameter_encode]" value="{$v}" {if $page.external_reference_decoded->parameter_encode==$v || (!$page.external_reference_decoded->parameter_encode && $k==1) } checked="checked"{/if} />{$v}</label>
                                            {/foreach}
										</div>
                                    </td>
                                </tr>
                            </table>
						</td>
					</tr>

                    <tr>
                    	<td class="sublabel">{t}taxonomy{/t}</td>
                    	<td>
                        	<table class="subsublabel">
                                <tr class="tr-highlight">
                                	<td>
									{t}show when rank:{/t}
                                    </td>
                                	<td>
                                    	<select class="big" name="external_reference[rank]" >
                                            <option value="" {if !$page.external_reference_decoded->rank} selected="selected"{/if}>{t}show for all ranks{/t}</option>
                                            {foreach $ranks v k ranks}
                                            {if $smarty.foreach.ranks.index>0}
                                            <option value="{$v.rank_id}"{if $v.rank_id==$page.external_reference_decoded->rank} selected="selected"{/if}>
                                            	equal to or below "{$v.rank}"
											</option>
                                            {/if}
                                            {/foreach}
                                        </select>									
                                    </td>
                                </tr>
                            </table>
						</td>
					</tr>

                    <tr>
                    	<td class="sublabel">{t}data check{/t}</td>
                    	<td>
                        	<table class="subsublabel">
                                <tr class="tr-highlight">
                                    <td>{t}check type:{/t}</td>
                                    <td>
                                    	<select class="big" name="external_reference[check_type]" >
                                            {foreach $check_types v k}
                                            <option value="{$v.field}"{if $v.field==$page.external_reference_decoded->check_type} selected="selected"{/if}>{$v.label}</option>
                                            {/foreach}
                                        </select>
                                    </td>
                                </tr>
                                <tr class="tr-highlight">
                                    <td>
                                    	<span class="check-by-none">{t}n/a{/t}</span>
                                    	<span class="check-by-query" style="display:none">{t}query:{/t}</span>
                                    	<span class="check-by-url" style="display:none">{t}URL:{/t}</span>
                                    	<span class="check-by-output" style="display:none">{t}element (optional):{/t}</span>
                                    </td>
                                    <td>
                                    	<textarea name="external_reference[query]">{$page.external_reference_decoded->query}</textarea>
										<div class="explanation">
                                        <u>checking by webservice output</u>:
                                        {t}the system assumes the webservice returns JSON-encoded data. after decoding, the data is fed to the PHP
                                        function 'empty()'. if the function returns true, it is assumed there is no data for the taxon under
                                        consideration, and the tab is not shown.{/t}<br />
                                        {t}if you want to check a specific field in the output against empty(), enter the path to the field in the textarea below. for
                                        instance, if the output is something like
                                        <code>{ "search":"Meles","results":[ { "count":"23","data" : [ ... ] } ] }</code> enter <code>search->count</code> to check the value of the 
                                        field "count". you cannot access a numbered array-element, just object properties.{/t}
										<br /><br />
                                        <u>checking by URL</u>:
                                        {t}specify an additional URL below to use for checking, rather than checking the one specified above (useful
                                        when you have specified multiple URLs above). this URL is parameterized the same way as the one above, and its
                                        output also checked against the 'empty()'-function. the output is JSON-decoded if the response header is "application/json",
                                        otherwise it is checked directly.{/t}
                                        <br /><br />
                                        <u>checking by query</u>:
                                        {t}enter the SQL-query to run below. it can take two parameters, <code>%pid%</code> for project ID and <code>%tid%</code> for taxon ID.<br />
                                        query is expected to return one row with a column called <code>result</code> that has a value of either 1
                                        (data present) or 0 (no data present).<br />
										queries are run "as is" and have the potential to destroy your entire databas, so don't mess around.{/t}
                                        <br /><br />
                                        {t}please note that by using webservice- of URL-checks, you make the performance of your site partially dependent on the
                                        response time of the queried webservice or URL.{/t}
                                        </div>
									</td>
                                </tr>
							</table>
						</td>
					</tr>
                    <tr>
                    	<td class="sublabel">{t}presentation{/t}</td>
                    	<td>
                        	<table class="subsublabel">
                                <tr class="tr-highlight">
                                    <td>{t}link or embed:{/t}</td>
                                    <td>
                                    	<select class="big" name="external_reference[link_embed]">
                                            {foreach $link_embed v k}
                                            <option value="{$v.field}"{if $v.field==$page.external_reference_decoded->link_embed} selected="selected"{/if}>{$v.label}</option>
                                            {/foreach}
                                        </select>
                                    </td>
                                </tr>
                                <tr class="tr-highlight">
                                    <td>{t}template:<br />(when embedding){/t}</td>
                                    <td>
                                    	<input type="text" name="external_reference[template]" value="{$page.external_reference_decoded->template}" /><br />
                                        <div class="explanation">
                                        {t}enter the template name including the extension. the system will not check if the template actually exists. if it does not, no content will be displayed.{/t}
                                        </div>
                                    </td>
                                </tr>
                                <tr class="tr-highlight">
                                    <td>{t}template parameters:{/t}</td>
                                    <td>
                                    	<input class="big" type="text" name="external_reference[template_params]" value="{$page.external_reference_decoded->template_params|@escape}" />
                                        <div class="explanation">
                                        optional JSON-encoded parameters. these will be decoded and passed "as is" to the template.
                                        </div>
                                    </td>
                                </tr>
							</table>
						</td>
					</tr>
				</table>
			</td>
		</tr>

	</table>


    <input type="button" value="save" onclick="save_page();" />

    </form>
</div>

{include file="../shared/admin-messages.tpl"}

<div class="page-generic-div">

    <a href="tabs.php">{t}back{/t}</a>

</div>

<div class="inline-templates" id="select_parameter">
<!--
<select name="external_reference[parameters][value][]" class="param_value medium" data-value="%VALUE%">
	{foreach $dynamic_fields v k}
	<option value="{$v.field}">{$v.label}</option>
    {/foreach}
</select>
-->
</div>
<div class="inline-templates" id="select_substitute">
<!--
<select name="external_reference[substitute][value][]" class="subst_value medium" data-value="%VALUE%">
	{foreach $dynamic_fields v k}
	<option value="{$v.field}">{$v.label}</option>
    {/foreach}
</select>
-->
</div>
<div class="inline-templates" id="list_item">
<!--
	<li class="page-blocks %CLASS% sortable" data-key="%KEY%" data-id="%ID%"><span class="move">&blk14;</span> %LABEL%%DEL-ICON%</li>
-->
</div>
<div class="inline-templates" id="del_icon">
<!--
	<span class="block-remove" onclick="removeBlock(%KEY%);">x</span>
-->
</div>
<div class="inline-templates" id="options">
<!--
<label title="{t}no transformation{/t}"><input data-index="%I%" type=radio name=external_reference[%TYPE%_transformation][%I%] value=none>{t}as is{/t}</label>
<label title="{t}make value lower case{/t}"><input data-index="%I%" type=radio name=external_reference[%TYPE%_transformation][%I%] value=lower>{t}lower{/t}</label>
<label title="{t}make value UPPER CASE{/t}"><input data-index="%I%" type=radio name=external_reference[%TYPE%_transformation][%I%] value=upper>{t}UPPER{/t}</label>
<label title="{t}make value Inital Capitals{/t}"><input data-index="%I%" type=radio name=external_reference[%TYPE%_transformation][%I%] value=initcap>{t}Init Caps{/t}</label> &nbsp;&nbsp; 
<label title="{t}spaces to underscores{/t}"><input data-index="%I%" type=checkbox name="external_reference[%TYPE%_underscores][%I%] title="'+_('convert spaces to underscores')+'">sp &rarr; _</label>
-->    
</div>
    
    
<script type="text/javascript">

var block_counter=0;

function removeBlock( i )
{
	$( 'li[data-key='+i+']').remove();
}

function addBlock( block )
{
	var tpl=fetchTemplate( 'list_item' );
	var tpl_del=fetchTemplate( 'del_icon' );
	var buffer=Array();

	$( '#block_list' ).append(
		tpl
			.replace(/%LABEL%/g,block.label)
			.replace('%ID%',block.id)
			.replace('%KEY%',block_counter)
			.replace('%DEL-ICON%',
				block.can_delete!==false ?
					tpl_del.replace('%KEY%',block_counter) : "" )
			.replace('%CLASS%',
				block.can_delete!==false ? 'auto-tab' : "" )
	);

	block_counter++;
}

var options='';
var subst=Array();

{foreach $page.external_reference_decoded->substitute v k foo}
subst.push({ name:'{$k}', value: '{$v}', transformation:'{$page.external_reference_decoded->subst_transformation[{$smarty.foreach.foo.index}]}', underscores:'{$page.external_reference_decoded->subst_underscores[{$smarty.foreach.foo.index}]}' });
{/foreach}
subst.push({ name:'', value: '', transformation: 'none', underscores: null });

function print_susbt()
{
	var buffer1=Array();
	var buffer2=Array();
	for(var i=0;i<subst.length;i++)
	{
		var slct=fetchTemplate( 'select_substitute' ).replace('%VALUE%',subst[i].value);
		buffer1.push('<input type="text" placeholder="placeholder" class="subst_name" name="external_reference[substitute][name][]" value="'+subst[i].name+'" /> &rarr; ' + slct + '<br />');
		buffer2.push( options.replace(/%I%/g,i).replace(/%TYPE%/g,'subst') + '<br />');
	}
	$('#substitutions').html( buffer1.join("\n") );
	$('#substitutions_transformations').html( buffer2.join("\n") );

	$('.subst_value').each(function()
	{
		$(this).val( $(this).attr('data-value') );
	});

	for(var i=0;i<subst.length;i++)
	{
		$('input[type=radio][name^=external_reference][name*=subst_transformation]').each(function(index,element)
		{
			if( $(this).attr('data-index')==i && $(this).val()==subst[i].transformation) $(this).prop('checked',true);
		});

		$('input[type=checkbox][name^=external_reference][name*=subst_underscores][data-index='+i+']').prop('checked',(subst[i].underscores=='on'));
	}
}

function add_subst()
{
	while(subst.length>0) subst.pop();
	$('.subst_name').each(function()
	{
		subst.push({ name:$(this).val(), value: $(this).next().val() });
	});
	subst.push({ name:'', value: '' });
	print_susbt()
}

var param=Array();
{foreach $page.external_reference_decoded->parameters v k}
param.push({ name:'{$k}', value: '{$v}', transformation:'{$page.external_reference_decoded->param_transformation[{$smarty.foreach.foo.index}]}', underscores:'{$page.external_reference_decoded->param_underscores[{$smarty.foreach.foo.index}]}' });
{/foreach}
param.push({ name:'', value: '', transformation: 'none', underscores: null });

function print_param()
{
	var buffer1=Array();
	var buffer2=Array();
	for(var i=0;i<param.length;i++)
	{
		var slct=fetchTemplate( 'select_parameter' ).replace('%VALUE%',param[i].value);
		buffer1.push('<input type="text" placeholder="parameter" class="param_name" name="external_reference[parameters][name][]" value="'+param[i].name+'" /> &nbsp;=&nbsp; ' + slct + '<br />');
		buffer2.push( options.replace(/%I%/g,i).replace(/%TYPE%/g,'param') + '<br />');
	}
	$('#parameters').html( buffer1.join("\n") );
	$('#parameters_transformations').html( buffer2.join("\n") );

	$('.param_value').each(function(){
		$(this).val( $(this).attr('data-value') );
	});

	for(var i=0;i<param.length;i++)
	{
		$('input[type=radio][name^=external_reference][name*=param_transformation]').each(function(index,element)
		{
			if( $(this).attr('data-index')==i && $(this).val()==param[i].transformation) $(this).prop('checked',true);
		});

		$('input[type=checkbox][name^=external_reference][name*=param_underscores][data-index='+i+']').prop('checked',(param[i].underscores=='on'));
	}
}

function add_param()
{
	while(param.length>0) param.pop();
	$('.param_name').each(function()
	{
		param.push({ name:$(this).val(), value: $(this).next().val() });
	});
	param.push({ name:'', value: '' });
	print_param()
}


function save_page()
{
	var form=$('#theForm');

	$( '.page-blocks' ).each(function()
	{
		$('<input type="hidden" name="page_blocks[]">').val($(this).attr('data-id')).appendTo(form);
	});

	form.submit();
}

$(document).ready(function()
{
	acquireInlineTemplates();

	{foreach $page.page_blocks_decoded v k}
	addBlock( { id:'{$v.id}', label: '{$v.label}', can_delete:{if $v.id!="data"}true{else}false{/if}} );
	{/foreach}

	$(".sortable").sortable({
		opacity: 0.6,
		cursor: 'move',
		items: "li:not(.ui-state-disabled)",
		connectWith: ".connectedSortable"
	});

	$( "#block_list" ).sortable();
	
	$( '[name^=external_reference\\[check_type\\]]' ).on( 'change', function(n)
	{
		$( '[name=external_reference\\[query\\]]' ).prop( 'disabled', $(this).val()=='none' );

		$( '.check-by-query' ).toggle( $(this).val()=='query' );
		$( '.check-by-none' ).toggle( $(this).val()=='none' );
		$( '.check-by-url' ).toggle( $(this).val()=='url' );
		$( '.check-by-output' ).toggle( $(this).val()=='output' );
	});

	$( '[name^=external_reference\\[check_type\\]]' ).trigger( 'change' );


	options=fetchTemplate( 'options' );
	print_susbt();
	print_param();
});
</script>

{include file="../shared/admin-footer.tpl"}
{include file="../shared/admin-header.tpl"}

<style>
h2 {
	margin-bottom:-10px;
}
table {
	border-collapse:collapse;
}

table tr td {
	vertical-align:top;
	padding:2px;
	width:auto;
}

table tr td.sublabel {
	font-style:italic;
}

table.subsublabel tr td:first-child {
	width:125px;
}

textarea {
	width:600px;
	height:75px;
	font-size:0.9em;
	font-family:consolas;
}
.explanation {
	font-size:0.9em;
	color:#666;
	margin:1px 0 2px 0;
	text-align:justify;
}

.explanation ul {
	padding-left:10px;
	margin:0 0 0 0;
}

</style>

<div id="page-main">

	<h2>{$page.page}</h2>
	<h3>{t}special attributes{/t}</h3>

	<!-- (id {$page.id}) -->
   
	<form method="post">
    <input type="hidden" name="id" value="{$page.id}" />
    <input type="hidden" name="action" value="save" />
    <input type="hidden" name="rnd" value="{$rnd}" />

    <table>
    	<tr>
            <td><u>display</u></td>
		</tr>
    	<tr class="tr-highlight">
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
        
    	<tr>
            <td>
            	<u>{t}external reference{/t}</u>

                <div class="explanation">
                	<p>
                    {t}URL to point to an external page or webservice. parametrization can be done through substitution and/or parameters. these achieve
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
                    {t}the URL can be "abused" for the transportation of multiple data-values to a template. for instance, multiple parametrized
                    URL's could be entered, separated wiht line feeds. when combined with the presentation option "embed parametrized URL only", those would 
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
                                    	<div id="substitutions"></div>
                                    	{t}encoding method:{/t}
                                    	{foreach $encoding_methods v k}
                                    	<label>
                                        	<input type="radio" name="external_reference[substitute_encode]" value="{$v}" {if $page.external_reference_decoded->substitute_encode==$v || (!$page.external_reference_decoded->substitute_encode && $k==1) } checked="checked"{/if} />{$v}</label>
                                    	{/foreach}
                                    </td>
                                </tr>
                                <tr class="tr-highlight">
                                    <td>
                                    	{t}parameters:{/t}<br />
										<a href="#" onclick="add_param();return false;">{t}add{/t}</a>
									</td>
                                    <td>
                                    	<div id="parameters"></div>
                                    	{t}encoding method:{/t}
                                    	{foreach $encoding_methods v k}
                                    	<label><input type="radio" name="external_reference[parameter_encode]" value="{$v}" {if $page.external_reference_decoded->parameter_encode==$v || (!$page.external_reference_decoded->parameter_encode && $k==1) } checked="checked"{/if} />{$v}</label>
                                    	{/foreach}
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
                                    	<select name="external_reference[check_type]">
                                            {foreach $check_types v k}
                                            <option value="{$v.field}"{if $v.field==$page.external_reference_decoded->check_type} selected="selected"{/if}>{$v.label}</option>
                                            {/foreach}
                                        </select>
                                    </td>
                                </tr>
                                <tr class="tr-highlight">
                                    <td>
                                    	{t}"check by" query:{/t}
                                    </td>
                                    <td>
                                    	<textarea name="external_reference[query]">{$page.external_reference_decoded->query}</textarea>
                                        <div class="explanation">
                                        {t}query can take two parameters, <code>%pid%</code> for project ID and <code>%tid%</code> for taxon ID.<br />
                                        query is expected to return one row with a column called <code>result</code> that has a value of either 1
                                        (data present) or 0 (no data present).<br />
										queries are run "as is" and have the potential to destroy your entire databas, so don't mess around.{/t}
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
                                    	<select name="external_reference[link_embed]">
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
							</table>
						</td>
					</tr>
				</table>
			</td>
		</tr>

	</table>
    
    <input type="submit" value="save" />
    </form>
</div>

{include file="../shared/admin-messages.tpl"}

<div class="page-generic-div">
    
    <a href="tabs.php">{t}back{/t}</a>

</div>

<div id="select_substitute" style="display:none">
<select name="external_reference[substitute][value][]" class="subst_value" data-value="%VALUE%">
	{foreach $dynamic_fields v k}
	<option value="{$v.field}">{$v.label}</option>
    {/foreach}
</select>
</div>
<div id="select_parameter" style="display:none">
<select name="external_reference[parameters][value][]" class="param_value" data-value="%VALUE%">
	{foreach $dynamic_fields v k}
	<option value="{$v.field}">{$v.label}</option>
    {/foreach}
</select>
</div>


<script type="text/javascript">

var subst=Array();
{foreach $page.external_reference_decoded->substitute v k}
subst.push({ name:'{$k}', value: '{$v}' });
{/foreach}
subst.push({ name:'', value: '' });

function print_susbt()
{
	var buffer=Array();
	for(var i=0;i<subst.length;i++)
	{
		var slct=$('#select_substitute').html().replace('%VALUE%',subst[i].value);
		buffer.push('<input type="text" placeholder="placeholder" class="subst_name" name="external_reference[substitute][name][]" value="'+subst[i].name+'" /> &rarr; ' + slct + '<br />');
	}
	$('#substitutions').html( buffer.join("\n") );

	$('.subst_value').each(function(){
		$(this).val( $(this).attr('data-value') );
	});
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
param.push({ name:'{$k}', value: '{$v}' });
{/foreach}
param.push({ name:'', value: '' });

function print_param()
{
	var buffer=Array();
	for(var i=0;i<param.length;i++)
	{
		var slct=$('#select_parameter').html().replace('%VALUE%',param[i].value);
		buffer.push('<input type="text" placeholder="parameter" class="param_name" name="external_reference[parameters][name][]" value="'+param[i].name+'" /> &nbsp;=&nbsp; ' + slct + '<br />');
	}
	$('#parameters').html( buffer.join("\n") );

	$('.param_value').each(function(){
		$(this).val( $(this).attr('data-value') );
	});
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


$(document).ready(function()
{
	print_susbt();
	print_param();
});
</script>

{include file="../shared/admin-footer.tpl"}
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
</style>

<div id="page-main">

	<h2>{$page.page}</h2>
	<h3>special attributes</h3>

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
                    hides the tab from the public menu, even if it contains published content. tab can still be accessed 
                    directly. useful to directly retrieve specific content for a taxon, for instance for external use in a associated
                    project.<br />
                    use <code>http://your.domain/linnaeus_ng/app/views/species/nsr_taxon.php?cat={$page.id}&id=[taxon id]</code> to access the tab.
                </div>
			</td>
		</tr>

    	<tr><td>&nbsp;</td></tr>
        
    	<tr>
            <td>
            	<u>{t}external reference{/t}</u>

                <div class="explanation">
                    URL to point to an external page or webservice. parametrization can be done through substitution and/or parameters. these achieve
                    similar goal, but work slightly different. use substitutions to replace placeholders in the URL itself with taxon-dependent values
                    at runtime; parameters will be added as name/value pairs to the query string of the URL. the data check is performed at runtime to decide whether the tab should be displayed in the taxon's menu (i.e., if any data is 
                    available). when 'no check' is selected, the tab is always displayed. the reference can be implemented as an actual link, navigating away from the site (or opening in a new window), or as embedded, in
                    which case a template can be defined in which to display the retrieved data.
                </div>
            
            	<table>
                	<tr class="tr-highlight">
                    	<td class="sublabel">link</td>
                        <td>
                        	<table class="subsublabel">
                            	<tr>
                                    <td>URL:</td>
                                    <td><textarea name="external_reference[url]">{$page.external_reference_decoded->url}</textarea></td>
                                </tr>

                                <tr>
                                    <td>
                                    	substitutions:<br />
										<a href="#" onclick="add_subst();return false;">add</a>                                        
                                    </td>
                                    <td>
                                    	<div id="substitutions"></div>
                                    	encoding method:
                                    	{foreach $encoding_methods v k}
                                    	<label>
                                        	<input type="radio" name="external_reference[substitute_encode]" value="{$v}" {if $page.external_reference_decoded->substitute_encode==$v || (!$page.external_reference_decoded->substitute_encode && $k==1) } checked="checked"{/if} />{$v}</label>
                                    	{/foreach}
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                    	parameters:<br />
										<a href="#" onclick="add_param();return false;">add</a>
									</td>
                                    <td>
                                    	<div id="parameters"></div>
                                    	encoding method:
                                    	{foreach $encoding_methods v k}
                                    	<label><input type="radio" name="external_reference[parameter_encode]" value="{$v}" {if $page.external_reference_decoded->parameter_encode==$v || (!$page.external_reference_decoded->parameter_encode && $k==1) } checked="checked"{/if} />{$v}</label>
                                    	{/foreach}
                                    </td>
                                </tr>
                            </table>
						</td>
					</tr>
                    <tr class="tr-highlight">
                    	<td class="sublabel">data check</td>
                    	<td>
                        	<table class="subsublabel">
                                <tr>
                                    <td>check type:</td>
                                    <td>
                                    	<select name="external_reference[check_type]">
                                            {foreach $check_types v k}
                                            <option value="{$v.field}"{if $v.field==$page.external_reference_decoded->check_type} selected="selected"{/if}>{$v.label}</option>
                                            {/foreach}
                                        </select>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                    	"check by" query:
                                    </td>
                                    <td>
                                    	<textarea name="external_reference[query]">{$page.external_reference_decoded->query}</textarea>
                                        <div class="explanation">
                                        query can take two parameters, <code>%pid%</code> for project ID and <code>%tid%</code> for taxon ID.<br />
                                        query is expected to return one row with a column called <code>result</code> that has a value of either 1
                                        (data present) or 0 (no data present).<br />
                                        and yes, queries are run "as is" and have the potential to destroy your entire databas, so don't mess around.
                                        </div>
									</td>
                                </tr>
							</table>
						</td>
					</tr>
                    <tr class="tr-highlight">
                    	<td class="sublabel">presentation</td>
                    	<td>
                        	<table class="subsublabel">
                                <tr>
                                    <td>link or embed:</td>
                                    <td>
                                    	<select name="external_reference[link_embed]">
                                            {foreach $link_embed v k}
                                            <option value="{$v.field}"{if $v.field==$page.external_reference_decoded->link_embed} selected="selected"{/if}>{$v.label}</option>
                                            {/foreach}
                                        </select>
                                    </td>
                                </tr>
                                <tr>
                                    <td>template:<br />(when embedding)</td>
                                    <td><input type="text" name="external_reference[template]" value="{$page.external_reference_decoded->template}" /></td>
                                </tr>
							</table>
						</td>
					</tr>
				</table>
			</td>
		</tr>

    	<tr><td>&nbsp;</td></tr>

    	<tr><td>TWO BELOW NEED TO BE COLLAPSED INTO THE ONE ABOVE</td></tr>

    	<tr>
            <td>
            	<u>{t}redirects to{/t}</u> {$page.redirect_to}
			</td>
		</tr>

    	<tr><td>&nbsp;</td></tr>
    	<tr>
            <td>
            	<u>{t}check query{/t}</u> {$page.check_query}
			</td>
		</tr>

    	<tr><td>&nbsp;</td></tr>
	</table>
    
    <input type="submit" value="save" />
    </form>
    
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
		buffer.push('<input type="text" class="subst_name" name="external_reference[substitute][name][]" value="'+subst[i].name+'" /> &rarr; ' + slct + '<br />');
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
		buffer.push('<input type="text" class="param_name" name="external_reference[parameters][name][]" value="'+param[i].name+'" /> &rarr; ' + slct + '<br />');
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
	$('#page-block-messages').fadeOut(3000);
});
</script>

{include file="../shared/admin-messages.tpl"}
{include file="../shared/admin-footer.tpl"}
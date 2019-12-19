{include file="../shared/admin-header.tpl"}
{include file="../shared/admin-messages.tpl"}

<div id="page-main">
<form action="" method="post" id="theForm" action="">
<input type="hidden" name="rnd" value="{$rnd}" />
<input type="hidden" name="id" id="id" value="{$ref.id}" />
<input type="hidden" name="action" id="action" value="" />


<p>
<input type="text" id="fuck" />
<div id="fuck_list" style="
background-color:#FFC;
border:1px solid #666;
width:250px;
height:400px;
overflow-y:scroll;
overflow-x:hidden;
"></div>

<script>
$(document).ready(function(){

	function species_lookup_list(data) 
	{
		if (typeof(data.data.callback)=='function')
		{
			var callback=data.data.callback;
		}
		
		var text=$(this).val();

		/*
			search			<string>
			match_start		[1,0]
			get_all			[1,0]
			concise			[1,0]
			formatted		[1,0]
			max_results		int > 0
		
			mandatory: search || get_all
		
		*/
		$.ajax({
			url : "../species/ajax_interface.php",
			type: "POST",
			data : ({
				'action' : 'get_lookup_list' ,
				'search' : text,
				'get_all' : 0,
				'match_start' : 0,
				'concise': 1,
				'max_results': 25,
				'formatted': 0,
				'time' : allGetTimestamp()
			}),
			success : function (data)
			{
				if (typeof(callback)=='function')
				{
					callback($.parseJSON(data));
				}
			}
		});

	}
	
	function build_list(data)
	{
		var d=Array();
		for(var i in data.results)
		{
			var t=data.results[i];
			if (t.label)
				d.push('<label><input type=checkbox value='+t.id+'>'+t.label+'</label>');
		}

		d.push('<a href="">all</a>');  // MAAR DABN GEPREPEND
		d.push('<hr>');

		$('#fuck_list').html(d.join('<br />'));
	}
	
	$('#fuck').bind('keyup', { callback:build_list } ,species_lookup_list);

});

</script>

	
	
{if $ref.multiple_authors==0 && $ref.author_second!=''}
{assign var=num value=2}
{elseif $ref.multiple_authors==1}
{assign var=num value=99}
{else}
{assign var=num value=1}
{/if}
<p>
    <table>
        <tr>
            <td colspan="2">
                <input type="button" value="{t}save{/t}" onclick="litCheckForm(this)" />
                {* <input type="button" value="{t}save and preview{/t}" onclick="$('#action').val('preview');litCheckForm(this)" /> *}
                {* if $session.admin.system.literature.taxon.taxon_id!=''}
                <input type="button" value="{t}back{/t}" onclick="window.open('../species/literature.php?id={$session.admin.system.literature.taxon.taxon_id}','_top')" />
                {else}
                <input type="button" value="{t}back{/t}" onclick="window.open('index.php','_top')" />
                {/if *}
                {if $ref.id}
                <input type="button" value="{t}delete{/t}" onclick="litDelete()" />
                {/if}
            </td>
        </tr>	
        <tr><td colspan="2">&nbsp;</td></tr>		
        <tr>
            <td>{t}Number of authors:{/t}</td>
            <td>
                <label><input type="radio" name="auths" id="auths-1" value="1" onchange="litToggleAuthorTwo()" {if $num==1}checked="checked"{/if} />{t}one{/t}</label>
                <label><input type="radio" name="auths" id="auths-2" value="2" onchange="litToggleAuthorTwo()" {if $num==2}checked="checked"{/if} />{t}two{/t}</label>
                <label><input type="radio" name="auths" id="auths-n" value="n" onchange="litToggleAuthorTwo()" {if $num==99}checked="checked"{/if}/>{t}more{/t}</label>
            </td>
        </tr>
        <tr>
            <td id="auth-label">
                {if $num==1}{t}Author:{/t}{else}{t}Authors:{/t}{/if}
            </td>
            <td>
                <input
                    type="text"
                    name="author_first"
                    id="author_first"
                    value="{$ref.author_first}"
                    autocomplete="off"
                    maxlength="32"
                    onkeyup="litShowAuthList(this)" />
                    <span id="auth-two" class="lit-author-two-{if $num!=2}hidden{/if}">
                    &amp;
                <input 
                    type="text" 
                    name="author_second" 
                    id="author_second" 
                    value="{$ref.author_second}" 
                    autocomplete="off"
                    maxlength="32" 
                    onkeyup="litShowAuthList(this)"/>
                </span>
                <span id="auth-etal" class="lit-author-etal-{if $num!=99}hidden{/if}">{t}et al.{/t}</span>
            </td>
        </tr>
        <tr>
            <td>{t}Year &amp; suffix (optional):{/t}</td>
            <td>
                <input
                    type="text" 
                    name="year" 
                    id="year" 
                    value="{$ref.year}" 
                    maxlength="4" 
                    style="width:50px" 
                    onfocus="litHideAuthList();" 
                    onkeyup="litCheckYear(this)"/>
                <input
                    type="text" 
                    name="suffix" 
                    id="suffix" 
                    value="{$ref.suffix}" 
                    maxlength="3" 
                    style="width:25px" />

				<span id="year2_stuff" style="display:none">
                
                <input type="hidden" name="use_year_range" id="use_year_range" value="0" />

                <select name="year_separator" id="year_separator" style="width:35px">
                	<option value="-"{if $ref.year_separator=='-'} selected="selected"{/if}>-</option>
                	<option value="&"{if $ref.year_separator=='&'} selected="selected"{/if}>&</option>
				</select> 
                    
                <input
                    type="text" 
                    name="year_2" 
                    id="year_2" 
                    value="{$ref.year_2}" 
                    maxlength="4" 
                    style="width:50px" 
                    onfocus="litHideAuthList();" 
                    onkeyup="litCheckYear(this)"/>
                <input
                    type="text" 
                    name="suffix_2" 
                    id="suffix_2" 
                    value="{$ref.suffix_2}" 
                    maxlength="3" 
                    style="width:25px" />

	                <a href="#" onclick="$('#year2_stuff').hide();$('#range_link').show();$('#use_year_range').val('0');return false;">hide year range</a>

				</span>
                {literal}
	                <a id="range_link" href="#" onclick="$('#year2_stuff').show();$(this).hide();$('#use_year_range').val('1');return false;">year range</a>
                {/literal}
				<script>
				{if $ref.year_2!=''}
                {literal}
					$('#use_year_range').val('1');
	                $('#year2_stuff').show();
					$('#range_link').hide();
                {/literal}
				{/if}
				</script>
                                        
                    
                <span id="msgYear"></span>
            </td>
        </tr>
    </table>
    
    <table>
        <tr style="vertical-align:top">
            <td>{t}Reference:{/t} *</td>
        </tr>
        <tr style="vertical-align:top">
            <td>
                <textarea
                    name="text"
                    id="text">{$ref.text}</textarea>
            </td>
        </tr>
    </table>
</p>

    <p>
        {t}Taxa this reference pertains to:{/t}<br />
        (you can drag and drop selected taxa to put them in the desired order)
    </p>

    <p>
        <div id="selected-taxa"></div>
    </p>
    <p>

        <select id="taxa" multiple="multiple" size="20" style="width:300px" ondblclick="litAddTaxon();">
        {foreach from=$taxa key=k item=v}
        {*if $v.id && (($isHigherTaxa && $v.lower_taxon==0) || !$isHigherTaxa)*}
        <option value="{$v.id}" {if $data.parent_id==$v.id}selected="selected"{/if}>{'&nbsp;&nbsp;'|str_repeat:$v.level-$taxa[0].level}{$v.taxon}</option>
        {*/if*}
        {/foreach}
        </select>
    </p>
    <p>
        <input type="button" onclick="litAddTaxon();" value="{t}add selected{/t}" />
    </p>
    <p>
        {t}(you can select multiple taxa by holding down the Ctrl or Shift key while selecting; you can also add single taxa by double-clicking their name){/t}
    </p>

</form>
</div>

<p id="dropdown" class="lit-dropdown-invisible"></p>

{literal}
<script type="text/JavaScript">
$(document).ready(function(){

$('body').click(function(e) {

	if(e.target.id!='dropdown') litHideAuthList();

});

{/literal}

initTinyMce(false,false);

{foreach from=$ref.taxa item=v}
litTaxonListAdd({$v.taxon_id});
{/foreach}
litTaxonListShow();
{if $ref}
litThisReference = ['{$ref.author_full|escape:'quotes'}, {$ref.year}{$ref.suffix|escape:'quotes'}'];
{/if}

{literal}

allInitDragtable(litTaxonListSave);

});
{/literal}
</script>

{include file="../shared/admin-footer.tpl"}

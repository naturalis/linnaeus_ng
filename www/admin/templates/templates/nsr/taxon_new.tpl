{include file="../shared/admin-header.tpl"}
{literal}
<style>
.tip-text {
	color:#888;
}
</style>
<script>

	function __untitled01(p)
	{
		// escape
		if (p.e.keyCode == 27)
		{ 
			closedropdownlist();
			return;
		}

		// calling element (input)
		var element=$('#'+p.id);
		// variable to lookup and to assign resulting value to
		var variable=element.attr('id').replace(/(^(__))/,'').replace(/((_INPUT)$)/,'');
		// value (entered text)
		var value=element.val();
		// minimal length of value to trigger list
		var minlength=element.attr('droplistminlength') ? element.attr('droplistminlength') : 1;

		if (value.length<minlength)
			return;

		var minlength=3;
		
		if (variable.indexOf('reference_id')!=-1)
		{
			url = '../literature2/ajax_interface.php';
		}
		else
		{
			url = 'ajax_interface.php';
		}
	
		data = {
			action: variable,
			search: value,
			time: allGetTimestamp()
		}
	
		$.ajax({
			url : url,
			type: "POST",
			data : data,
			success : function (data)
			{
				//console.log(data);
				buildlist($.parseJSON(data),variable);
			}
		});
		
	}
	
	function __untitled02(ele,variable)
	{
		// don't change order of lines
		$('#'+variable.replace(/(_id)$/,'')).html($(ele).text());
		$('#'+variable).val($(ele).attr('value')).trigger('change');
	}

	function buildlist(data,variable)
	{
		var buffer=Array();
	
		buffer.push('<li><a href="#" onclick="__untitled02(this,\''+variable+'\');closedropdownlist();return false;" value="-1">n.v.t.</a></li>');
	
		for(var i in data.results)
		{
			var t=data.results[i];

			if (variable=='dutch_name_organisation_id' && t.is_company!='1') continue;
			if (variable=='dutch_name_expert_id' && t.is_company=='1') continue;
			if (variable=='presence_organisation_id' && t.is_company!='1') continue;
			if (variable=='presence_expert_id' && t.is_company=='1') continue;
			if (variable=='name_organisation_id' && t.is_company!='1') continue;
			if (variable=='name_expert_id' && t.is_company=='1') continue;
			
			if (variable.indexOf('reference_id')!=-1)
			{
				var label=
					(t.author ? t.author+" - " : "")+
					(t.label)+
					(t.date ? " ("+t.date+")" : "");
			}
			else 
			{
				var label=t.label;
			}


			if (t.label && t.id)
			{
				buffer.push(
					'<li><a href="#" onclick="__untitled02(this,\''+variable+'\');closedropdownlist();return false;" value="'+t.id+'">'+label+'</a></li>'
				);
			}
		}
	
		$('#dropdown-list-content').html('<ul>'+buffer.join('')+'</ul>');
		
		showdropdownlist('__'+variable+'_INPUT');

	}

	function getinheritablename()
	{
		$.ajax({
			url : 'ajax_interface.php',
			type: "POST",
			data : {id:$('#parent_taxon_id').val(),action:'get_inheritable_name'},
			success : function (data)
			{
				inheritablename=data;
				partstoname()
			}
		});

	}


</script>
{/literal}


<div id="page-main">

<h2>nieuw concept</h2><span id="timer"></span>

<form id="data" onsubmit="return false;">
<p>

	<table>

		<tr><th></th><td><i>taxonomie</i></th><td></td></tr>
		<tr><th>rang:</th>
			<td>
				{foreach from=$ranks item=v}{if $v.id==$concept.rank_id}{$v.rank}{/if}{/foreach} 
				<select id="concept_rank_id" onchange="storedata(this);" mandatory="mandatory" label="rang">
				<option value=""></option>
				{foreach from=$ranks item=v}
				<option value="{$v.id}" {if $newrank && $v.id==$newrank} selected="selected"{/if}>{$v.label}</option>
				{/foreach}
				</select> *
			</td>
		</tr>
		<tr><th>ouder:</th>
			<td>
				<span id="parent_taxon">{$parent.taxon}</span>
				<input type="text" class="medium" id="__parent_taxon_id_INPUT" value=""  havedroplist="true" droplistminlength="3" /> *
				<input type="hidden" id="parent_taxon_id" value="" mandatory="mandatory" onchange="getinheritablename();" label="ouder" />
			</td>
		</tr>

		<tr><td colspan="2">&nbsp;</td></tr>
		<tr>
			<th></th>
			<td><i>geldige wetenschappelijke naam</i></th>
		</tr>
		<tr><th>genus:</th><td><input onkeyup="partstoname();" type="text" class="medium" id="name_uninomial" value="" mandatory="mandatory" label="genus" /> *</td></tr>
		<tr><th>soort:</th><td><input onkeyup="partstoname();" type="text" class="medium" id="name_specific_epithet" value="" label="soort" /></td></tr>
		<tr><th>ondersoort:</th><td><input onkeyup="partstoname();" type="text" class="medium" id="name_infra_specific_epithet" value="" /></td></tr>

		<tr><td colspan="2" style="height:5px;"></td></tr>
		
		<tr>
			<th title="vul de volledige waarde voor 'auteurschap' in, inclusief komma, jaartal en haakjes; het programma leidt de waarden voor auteur en jaar automatisch af.">	
				auteurschap:
			</th><td><input onkeyup="partstoname();" type="text" class="medium" id="name_authorship" value="" mandatory="mandatory" label="auteurschap" /> *</td>
		</tr>
		<tr><th>auteur(s):</th><td><input type="text" class="medium" id="name_name_author" value="" mandatory="mandatory" disabled="disabled" label="auteur" /></td></tr>	
		<tr><th>jaar:</th><td><input type="text" class="small" id="name_authorship_year" value="" mandatory="mandatory" disabled="disabled" label="jaar" /></td></tr>	

		<tr><td colspan="2" style="height:5px;"></td></tr>

		<tr>
			<th>expert:</th>
			<td>
				<select id="name_expert_id">
					<option value="" selected="selected">n.v.t.</option>
				{foreach from=$actors item=v key=k}
				{if $v.is_company=='0'}
					<option value="{$v.id}">{$v.label}</option>
				{/if}
				{/foreach}
				</select> 
			</td>
		</tr>
	
		<tr>
			<th>organisatie:</th>
			<td>
				<select id="name_organisation_id">
					<option value="" selected="selected">n.v.t.</option>
				{foreach from=$actors item=v key=k}
				{if $v.is_company=='1'}
					<option value="{$v.id}">{$v.label}</option>
				{/if}
				{/foreach}
				</select> 
			</td>
		</tr>
	
		{*
		<!--tr><th>expert:</th>
			<td>
				<span id="name_expert"></span>
				<input type="text" class="medium" id="__name_expert_id_INPUT" value="" havedroplist="true" />
				<input type="hidden" id="name_expert_id" value="" />
			</td>
		</tr>

		<tr><th>organisatie:</th>
			<td>
				<span id="name_organisation"></span>
				<input type="text" class="medium" id="__name_organisation_id_INPUT" value="" havedroplist="true" />
				<input type="hidden" id="name_organisation_id" value="" />
			</td>
		</tr-->
		*}

		<tr><th>publicatie:</th>
			<td>
				<span id="name_reference"></span>
				<input type="text" class="medium" id="__name_reference_id_INPUT" value="" havedroplist="true" />
				<input type="hidden" id="name_reference_id" value="" />
			</td>
		</tr>
		

		<tr><td colspan="2">&nbsp;</td></tr>
		<tr>
			<th></th>
			<td><i>concept</i></td>
		</tr>
		<tr><th title="conceptnaam wordt automatische samengesteld op basis van de geldige wetenschappelijke naam.">naam:</th><td>
			<input type="text" id="concept_taxon" value="" mandatory="mandatory" onchange="$('#name_name').val($(this).val()).trigger('change');" disabled="disabled" label="naam concept" />
			<input type="hidden" id="name_name" value="" mandatory="mandatory" />
		</td></tr>
		<tr><th>nsr id:</th><td>(wordt automatisch gegenereerd)</td></tr>

		<tr><td colspan="2">&nbsp;</td></tr>
		<tr>
			<th></th>
			<td><i>nederlandse naam</i></td>
		</tr>
		<tr><th>naam:</th><td>
			<input type="text" id="dutch_name" value="" onchange="" />
		</td></tr>

		<tr><td colspan="2" style="height:5px;"></td></tr>

		<tr>
			<th>expert:</th>
			<td>
				<select id="dutch_name_expert_id">
					<option value="" selected="selected">n.v.t.</option>
				{foreach from=$actors item=v key=k}
				{if $v.is_company=='0'}
					<option value="{$v.id}">{$v.label}</option>
				{/if}
				{/foreach}
				</select> 
			</td>
		</tr>
	
		<tr>
			<th>organisatie:</th>
			<td>
				<select id="dutch_name_organisation_id">
					<option value="" selected="selected">n.v.t.</option>
				{foreach from=$actors item=v key=k}
				{if $v.is_company=='1'}
					<option value="{$v.id}">{$v.label}</option>
				{/if}
				{/foreach}
				</select> 
			</td>
		</tr>
	

		{*
		<!--tr><th>expert:</th>
			<td>
				<span id="dutch_name_expert"></span>
				<input type="text" class="medium" id="__dutch_name_expert_id_INPUT" value="" havedroplist="true" />
				<input type="hidden" id="dutch_name_expert_id" value="" />
			</td>
		</tr>

		<tr><th>organisatie:</th>
			<td>
				<span id="dutch_name_organisation"></span>
				<input type="text" class="medium" id="__dutch_name_organisation_id_INPUT" value="" havedroplist="true"  />
				<input type="hidden" id="dutch_name_organisation_id" value="" />
			</td>
		</tr -->
		*}

		<tr><th>publicatie:</th>
			<td>
				<span id="dutch_name_reference"></span>
				<input type="text" class="medium" id="__dutch_name_reference_id_INPUT" value="" havedroplist="true" />
				<input type="hidden" id="dutch_name_reference_id" value="" />
			</td>
		</tr>

		<tr><td colspan="2">&nbsp;</td></tr>

		<tr>
			<th></th>
			<td><i>voorkomen</i></td>
		</tr>
		<tr><th>status:</th>
			<td>
				<select id="presence_presence_id" onchange="storedata(this);" >
				<option value="-1">n.v.t.</option>
				{assign var=first value=true}
				{foreach from=$statuses item=v}
					{if $v.index_label==99 && $first==true}
					<option value="" disabled="disabled">&nbsp;</option>
					{assign var=first value=false}
					{/if}
					<option value="{$v.id}">{$v.index_label}. {$v.label}</option>
				{/foreach}
				</select>
			</td>
		</tr>

		<tr><th>habitat:</th>
			<td>
				<select id="presence_habitat_id" onchange="storedata(this);" >
					<option value="-1">n.v.t.</option>
				{foreach from=$habitats item=v}
					<option value="{$v.id}">{$v.label}</option>
				{/foreach}
				</select>
			</td>
		</tr>

		<tr><td colspan="2" style="height:5px;"></td></tr>

		<tr>
			<th>expert:</th>
			<td>
				<select id="presence_expert_id">
					<option value="" selected="selected">n.v.t.</option>
				{foreach from=$actors item=v key=k}
				{if $v.is_company=='0'}
					<option value="{$v.id}">{$v.label}</option>
				{/if}
				{/foreach}
				</select> 
			</td>
		</tr>
	
		<tr>
			<th>organisatie:</th>
			<td>
				<select id="presence_organisation_id">
					<option value="" selected="selected">n.v.t.</option>
				{foreach from=$actors item=v key=k}
				{if $v.is_company=='1'}
					<option value="{$v.id}">{$v.label}</option>
				{/if}
				{/foreach}
				</select> 
			</td>
		</tr>
	
		{*
		<!-- tr><th>expert:</th>
			<td>
				<span id="presence_expert"></span>
				<input type="text" class="medium" id="__presence_expert_id_INPUT" value="" havedroplist="true" />
				<input type="hidden" id="presence_expert_id" value="" />
			</td>
		</tr>

		<tr><th>organisatie:</th>
			<td>
				<span id="presence_organisation"></span>
				<input type="text" class="medium" id="__presence_organisation_id_INPUT" value="" havedroplist="true" />
				<input type="hidden" id="presence_organisation_id" value="" />
			</td>
		</tr -->
		*}

		<tr><th>publicatie:</th>
			<td>
				<span id="presence_reference"></span>
				<input type="text" class="medium" id="__presence_reference_id_INPUT" value="" havedroplist="true" />
				<input type="hidden" id="presence_reference_id" value="" />
				<!-- span id="reference"></span> <input type="text" class="medium" id="__reference_list_input" value="" />
				<input type="hidden" id="presence_reference_id" value="" / -->
			</td>
		</tr>
		</table>
</p>


<input type="button" value="opslaan" onclick="savedataform();" />
</form>

<p>
	<a href="index.php">terug</a>
</p>

</div>

<div id="dropdown-list">
	<div id="dropdown-list-content"></div>
</div>


<script>
$(document).ready(function()
{
	//allLookupNavigateOverrideUrl('taxon.php?id=%s');

	$('#data :input[type!=button]').each(function(key,value) {
		var set={ name:$(this).attr('id'),label:$(this).attr('label'),current:$(this).val(), mandatory:$(this).attr('mandatory')=='mandatory' };
		{if $parent}
		if ($(this).attr('id')=='parent_taxon_id')
		{
			set.current=-1;
			set.new={$parent.id};
		}
		{/if}
		{if $newrank}
		if ($(this).attr('id')=='concept_rank_id')
		{
			set.current=-1;
			set.new={$newrank};
		}
		{/if}
		values.push( set );
		$(this).on('change',function() { setnewvalue( { name:$(this).attr('id'),value:$(this).val() } ); } );
	});

	values.push( { name:'name_type_id',current:'',new:{$name_type_id},mandatory:true } );
	values.push( { name:'name_language_id',current:'',new:{$name_language_id},mandatory:true } );

	$('[havedroplist=true]').each(function() {
		$(this).bind('keyup', function(e) { 
			__untitled01({ e:e, id: $(this).attr('id') } )
		} );
	});

	{if $parent}
	inheritablename='{$parent.inheritable_name|@escape}';
	partstoname();
	{/if}

});
</script>

{include file="../shared/admin-messages.tpl"}
{include file="../shared/admin-footer.tpl"}
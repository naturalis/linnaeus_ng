{include file="../shared/admin-header.tpl"}

<div id="page-main" class="taxonomy">

<p>
				
<form method=post>
<input type="hidden" name="id" value="{$concept.id}">
<input type="hidden" name="action" value="save">
<input type="hidden" name="rnd" value="{$rnd}">
Add a name:			
<table>
	<tr>
		<td>Name:</td>
		<td><input type="text" name="name" value="{$name.name}"/></td>
	</tr>		
	<tr>
		<td>Uninomial:</td>
		<td><input type="text" name="uninomial" value="{$name.uninomial}"/></td>
	</tr>		
	<tr>
		<td>Specific epithet:</td>
		<td><input type="text" name="specific_epithet" value="{$name.specific_epithet}"/></td>
	</tr>		
	<tr>
		<td>Infra specific epithet:</td>
		<td><input type="text" name="infra_specific_epithet" value="{$name.infra_specific_epithet}"/></td>
	</tr>		
	<tr>
		<td>Authorship:</td>
		<td><input type="text" name="authorship" value="{$name.authorship}"/></td>
	</tr>		
	<tr>
		<td>Name author:</td>
		<td><input type="text" name="name_author" value="{$name.name_author}"/></td>
	</tr>		
	<tr>
		<td>Authorship year:</td>
		<td><input type="text" name="authorship_year" value="{$name.authorship_year}"/></td>
	</tr>		

	<tr>
		<td>Type:</td>
		<td>
			<select>
			{foreach from=$types item=v}
			{if $v.nametype!='isValidNameOf'}
			<option value="{$v.id}"{if $v.id==$name.type_id} selected="selected"{/if}>{$v.nametype}</option>
			{/if}
			{/foreach}				
			</select>
		</td>
	</tr>		
	<tr>
		<td>Language:</td>
		<td>
			<select>
			<option value=""></option>
			{assign var=first value=true}
			{foreach from=$languages item=v key=k}
			{if $v.show_order==9999 && $first}
			<option value="" disabled="disabled"></option>
				{assign var=first value=false}
			{/if}
			<option value="{$v.id}"{if $v.id==$name.language_id} selected="selected"{/if}>{$v.language}</option>
			{if $k==0}<option value="" disabled="disabled"></option>{/if}
			{/foreach}				
			</select>
		</td>
	</tr>		
	<tr>
		<td>Expert:</td>
		<td>
			<select>
			<option value=""></option>
			{foreach from=$actors item=v}
			{if $v.is_company!='1'}
			<option value="{$v.id}"{if $v.id==$name.expert_id} selected="selected"{/if}>{$v.name}</option>
			{/if}
			{/foreach}				
			</select>
		</td>
	</tr>	
	<tr>
		<td>Institute:</td>
		<td>
			<select>
			<option value=""></option>
			{foreach from=$actors item=v}
			{if $v.is_company=='1'}
			<option value="{$v.id}"{if $v.id==$name.organisation_id} selected="selected"{/if}>{$v.name}</option>
			{/if}
			{/foreach}				
			</select>
		</td>
	</tr>
	</tr>	
	<tr>
		<td>Reference:</td>
		<td>
			<select>
			<option value=""></option>
			{foreach from=$references item=v}
			<option value="{$v.id}"{if $v.id==$name.reference_id} selected="selected"{/if}>{$v.label}{if $v.author} ({$v.author}){/if}</option>
			{/foreach}				
			</select>
		</td>
	</tr>
</table>

<input type="submit" value="save" />
</form>
</p>
</div>

<script type="text/JavaScript">
$(document).ready(function()
});
</script>


{include file="../shared/admin-messages.tpl"}
{include file="../shared/admin-footer.tpl"}
